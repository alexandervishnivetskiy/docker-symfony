<?php

namespace App\Command;


use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateUserCommand extends Command\Command
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('app:get-count')
            ->setDescription('Gets number of reports would be created.')
            ->addArgument('number', InputArgument::REQUIRED);
    }

    function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('number');


        for ($i = 1; $i <= $count; $i++) {
            $report = new Report();
            $report->setName('testName');
            $report->setDeviceID(222222);
            $report->setDescription('Test description');
            $report->setClient('Client');
            $this->em->persist($report);
        }

        $this->em->flush();
        $output->writeln('You want to create: ' . $count . ' reports');
    }
}