<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;


class ClientFixtures extends Fixture
{

    public function load(ObjectManager $manager)

    {
        $faker = Factory::create();

        $numClients = 10;
        for ($j = 0; $j < $numClients; $j++) {

            $client = new Client();
            $client->setName($faker->name);
            $client->setCountry($faker->country);
            $client->setTelephone($faker->phoneNumber);
            $client->setCountry($faker->country);
            $client->setEmail($faker->email);
            $manager->persist($client);

            $numReports = rand(0, 10);
            for ($i = 1; $i <= $numReports; $i++) {

                $report = new Report();
                $report->setName($faker->text(20));
                $report->setDeviceID($faker->numberBetween(1, 10000));
                $report->setDescription($faker->text);
                $report->setClient($client);
                $manager->persist($report);
            }
            $manager->flush();
        }
    }
}