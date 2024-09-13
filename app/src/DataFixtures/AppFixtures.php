<?php

namespace App\DataFixtures;

use App\Entity\JobOffer;
use App\Entity\Candidates;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $candidate = new Candidates();
        $candidate->setFirstName('Test');
        $candidate->setLastName('User');
        $candidate->setEmail('test@example.com');
        $candidate->setPhoneNumber('123456789');
        $candidate->setPassword($this->passwordHasher->hashPassword($candidate, 'password'));

        $manager->persist($candidate);

        $jobOffer = new JobOffer();
        $jobOffer->setJobTitle('Test Job');
        $jobOffer->setJobDescription('This is a test job description.');
        $jobOffer->setDateAdded(new \DateTime());
        $manager->persist($jobOffer);


        $jobOffer2 = new JobOffer();
        $jobOffer2->setJobTitle('Software Engineer');
        $jobOffer2->setJobDescription('Develop and maintain software applications.');
        $jobOffer2->setDateAdded(new \DateTime());
        $manager->persist($jobOffer2);

        $manager->flush();
    }
}