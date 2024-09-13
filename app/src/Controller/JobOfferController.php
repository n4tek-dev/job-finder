<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Candidates;
use App\Entity\Applications;
use App\Form\CandidateType;
use App\Form\JobOfferSearchType;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class JobOfferController extends AbstractController
{
    /**
     * Search for job offers.
     *
     * @param Request $request
     * @param JobOfferRepository $repository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/', name: 'job_offer_search')]
    public function search(Request $request, JobOfferRepository $repository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(JobOfferSearchType::class);
        $form->handleRequest($request);

        $queryBuilder = $repository->createQueryBuilder('j');

        if ($form->isSubmitted() && $form->isValid()) {
            $keyword = $form->get('keyword')->getData();
            if ($keyword) {
                $queryBuilder->andWhere('j.jobTitle LIKE :keyword OR j.jobDescription LIKE :keyword')
                    ->setParameter('keyword', '%' . $keyword . '%');
            }
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('job_offer/search.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * Apply for a job offer.
     *
     * @param Request $request
     * @param JobOffer $jobOffer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/apply', name: 'job_offer_apply')]
    public function apply(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('info', 'Musisz się zalogować, aby aplikować na ofertę pracy.');
            return $this->redirectToRoute('app_login');
        }

        /** @var Candidates $candidate */
        $candidate = $this->getUser();

        if ($this->hasAlreadyApplied($candidate, $jobOffer, $entityManager)) {
            $this->addFlash('warning', 'Już aplikowałeś na tę ofertę pracy.');
            return $this->redirectToRoute('job_offer_search');
        }

        $form = $this->createForm(CandidateType::class, $candidate, [
            'include_password' => false,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->submitApplication($candidate, $jobOffer, $entityManager);
            $this->addFlash('success', 'Aplikacja została złożona pomyślnie.');
            return $this->redirectToRoute('job_offer_search');
        }

        return $this->render('job_offer/apply.html.twig', [
            'form' => $form->createView(),
            'jobOffer' => $jobOffer,
        ]);
    }

    /**
     * Check if the candidate has already applied for the job offer.
     *
     * @param Candidates $candidate
     * @param JobOffer $jobOffer
     * @param EntityManagerInterface $entityManager
     * @return bool
     */
    private function hasAlreadyApplied(Candidates $candidate, JobOffer $jobOffer, EntityManagerInterface $entityManager): bool
    {
        $applicationRepository = $entityManager->getRepository(Applications::class);
        $existingApplication = $applicationRepository->findOneBy([
            'jobOffer' => $jobOffer,
            'candidate' => $candidate
        ]);

        return $existingApplication !== null;
    }

    /**
     * Submit a job application.
     *
     * @param Candidates $candidate
     * @param JobOffer $jobOffer
     * @param EntityManagerInterface $entityManager
     */
    private function submitApplication(Candidates $candidate, JobOffer $jobOffer, EntityManagerInterface $entityManager): void
    {
        $application = new Applications();
        $application->setJobOffer($jobOffer);
        $application->setCandidate($candidate);
        $entityManager->persist($application);
        $entityManager->flush();
    }
}