<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\JobOffer;
use App\Entity\Candidates;
use App\Entity\Applications;

class JobOfferControllerTest extends WebTestCase
{
    private const APPLY_URL_TEMPLATE = '/%d/apply';
    private const LOGIN_URL = '/login';
    private const HOME_URL = '/';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_JOB_TITLE = 'Test Job';
    private const SUCCESS_JOB_TITLE = 'Software Engineer';

    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testApplyNotAuthenticated()
    {
        $this->client->request('GET', sprintf(self::APPLY_URL_TEMPLATE, 1));

        $this->assertResponseRedirects(self::LOGIN_URL);
        $this->client->followRedirect();
    }

    public function testApplyAlreadyApplied()
    {
        $candidate = $this->getCandidate(self::TEST_EMAIL);
        $this->client->loginUser($candidate);

        $jobOffer = $this->getJobOffer(self::TEST_JOB_TITLE);
        $this->createApplication($jobOffer, $candidate);

        $this->client->request('GET', sprintf(self::APPLY_URL_TEMPLATE, $jobOffer->getId()));

        $this->assertResponseRedirects(self::HOME_URL);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-warning', 'Już aplikowałeś na tę ofertę pracy.');
    }

    public function testApplySuccessfully()
    {
        $candidate = $this->getCandidate(self::TEST_EMAIL);
        $this->client->loginUser($candidate);

        $jobOffer = $this->getJobOffer(self::SUCCESS_JOB_TITLE);

        if ($this->applicationExists($jobOffer, $candidate)) {
            $this->client->request('GET', sprintf(self::APPLY_URL_TEMPLATE, $jobOffer->getId()));
            $this->assertResponseRedirects(self::HOME_URL);
            $this->client->followRedirect();
            $this->assertSelectorTextContains('.alert-warning', 'Już aplikowałeś na tę ofertę pracy.');
        } else {
            $crawler = $this->client->request('GET', sprintf(self::APPLY_URL_TEMPLATE, $jobOffer->getId()));
            $form = $crawler->selectButton('apply')->form();
            $this->client->submit($form);

            $this->assertResponseRedirects(self::HOME_URL);
            $this->client->followRedirect();
            $this->assertSelectorTextContains('.alert-success', 'Aplikacja została złożona pomyślnie.');
        }
    }

    private function getCandidate(string $email): Candidates
    {
        return $this->entityManager->getRepository(Candidates::class)->findOneBy(['email' => $email]);
    }

    private function getJobOffer(string $jobTitle): JobOffer
    {
        return $this->entityManager->getRepository(JobOffer::class)->findOneBy(['jobTitle' => $jobTitle]);
    }

    private function createApplication(JobOffer $jobOffer, Candidates $candidate): void
    {
        $application = new Applications();
        $application->setJobOffer($jobOffer);
        $application->setCandidate($candidate);
        $this->entityManager->persist($application);
        $this->entityManager->flush();
    }

    private function applicationExists(JobOffer $jobOffer, Candidates $candidate): bool
    {
        $applicationRepository = $this->entityManager->getRepository(Applications::class);
        return null !== $applicationRepository->findOneBy([
            'jobOffer' => $jobOffer,
            'candidate' => $candidate
        ]);
    }
}