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
        $count = rand(1, 10);

        for ($j = 0; $j < $count; $j++) {
            $client = new Client();
            for ($i = 0; $i < $count; $i++) {

                $client->setName($faker->name);
                $client->setCountry($faker->country);
                $client->setTelephone($faker->phoneNumber);
                $client->setCountry($faker->country);
                $client->setEmail($faker->email);
                $manager->persist($client);

                $report = new Report();
                $report->setName($faker->text(20));
                $report->setDeviceID($faker->numberBetween(1, 10000));
                $report->setDescription($faker->text);
                $report->setClient($client);
                $manager->persist($report);

                $manager->flush();
            }
        }

    }


}