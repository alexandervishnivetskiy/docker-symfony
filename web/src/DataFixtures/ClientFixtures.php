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

        for ($i = 0; $i < 5; $i++) {
            $report = new Report();
            $report->setName($faker->text(20));
            $report->setDeviceID($faker->numberBetween(1, 10000));
            $report->setDescription($faker->text);
            $manager->persist($report);


            $client = new Client();
            $client->setName($faker->name);
            $client->setCountry($faker->country);
            $client->setTelephone($faker->phoneNumber);
            $client->setCountry($faker->country);
            $client->setEmail($faker->email);
            $client->addReport($report);
            $manager->persist($client);





//            $client->addReport($report);


            $manager->flush();
        }

    }


}