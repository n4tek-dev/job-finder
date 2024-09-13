<?php

namespace App\Command;

use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Faker\Factory;

#[AsCommand(
    name: 'app:generate-job-offers',
    description: 'Generates sample job offers'
)]
class GenerateJobOffersCommand extends Command
{
    //protected static $defaultName = 'app:generate-job-offers';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates sample job offers')
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'Number of job offers to generate', 35);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = $input->getOption('count');
        $faker = Factory::create();

        for ($i = 0; $i < $count; $i++) {
            $jobOffer = new JobOffer();
            $jobOffer->setJobTitle($faker->jobTitle);
            $jobOffer->setJobDescription($faker->paragraph);
            $jobOffer->setDateAdded($faker->dateTimeBetween('-1 year', 'now'));

            $this->entityManager->persist($jobOffer);
        }

        $this->entityManager->flush();

        $io->success(sprintf('Generated %d job offers.', $count));

        return Command::SUCCESS;
    }
}