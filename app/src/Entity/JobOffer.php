<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $jobTitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $jobDescription = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdded = null;

    /**
     * @var Collection<int, Applications>
     */
    #[ORM\OneToMany(targetEntity: Applications::class, mappedBy: 'jobOffer', orphanRemoval: true)]
    private Collection $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    /**
     * Get the ID of the job offer.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the job title.
     *
     * @return string|null
     */
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * Set the job title.
     *
     * @param string $jobTitle
     * @return self
     */
    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Get the job description.
     *
     * @return string|null
     */
    public function getJobDescription(): ?string
    {
        return $this->jobDescription;
    }

    /**
     * Set the job description.
     *
     * @param string $jobDescription
     * @return self
     */
    public function setJobDescription(string $jobDescription): self
    {
        $this->jobDescription = $jobDescription;

        return $this;
    }

    /**
     * Get the date the job offer was added.
     *
     * @return \DateTimeInterface|null
     */
    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    /**
     * Set the date the job offer was added.
     *
     * @param \DateTimeInterface $dateAdded
     * @return self
     */
    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get the applications for the job offer.
     *
     * @return Collection<int, Applications>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    /**
     * Add an application to the job offer.
     *
     * @param Applications $application
     * @return self
     */
    public function addApplication(Applications $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setJobOffer($this);
        }

        return $this;
    }

    /**
     * Remove an application from the job offer.
     *
     * @param Applications $application
     * @return self
     */
    public function removeApplication(Applications $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getJobOffer() === $this) {
                $application->setJobOffer(null);
            }
        }

        return $this;
    }
}