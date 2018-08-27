<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ClientCommand extends Command\Command
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
            ->setName('app:add-clients')
                ->setDescription('Creates entries to client table')
            ->addArgument('number', InputArgument::REQUIRED);
    }

    function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('number');
        $faker = Factory::create();


        for ($i = 1; $i <= $count; $i++) {
            $client = new Client();
            $client->setName($faker->company);
            $client->setTelephone($faker->phoneNumber);
            $client->setCountry($faker->country);
            $client->setEmail($faker->email);
            $this->em->persist($client);
        }

        $this->em->flush();
        $output->writeln('You just created ' . $count . ' clients');
    }
}