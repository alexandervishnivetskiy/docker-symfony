<?php

namespace App\Command;

//require_once __DIR__ . '../../faker/vendor/fzaninotto/faker/src/autoload.php';

use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ReportCommand extends Command\Command
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
            $report->setDeviceID(2);
            $report->setDescription('Test description');
            $report->setClient('Client');
            $this->em->persist($report);
        }

        $this->em->flush();
        $output->writeln('You just created ' . $count . ' reports');
    }
}