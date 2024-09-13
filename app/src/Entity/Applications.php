<?php

namespace App\Entity;

use App\Repository\ApplicationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationsRepository::class)]
class Applications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $jobOffer = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidates $candidate = null;

    /**
     * Get the ID of the application.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the associated job offer.
     *
     * @return JobOffer|null
     */
    public function getJobOffer(): ?JobOffer
    {
        return $this->jobOffer;
    }

    /**
     * Set the associated job offer.
     *
     * @param JobOffer|null $jobOffer
     * @return self
     */
    public function setJobOffer(?JobOffer $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    /**
     * Get the associated candidate.
     *
     * @return Candidates|null
     */
    public function getCandidate(): ?Candidates
    {
        return $this->candidate;
    }

    /**
     * Set the associated candidate.
     *
     * @param Candidates|null $candidate
     * @return self
     */
    public function setCandidate(?Candidates $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }
}