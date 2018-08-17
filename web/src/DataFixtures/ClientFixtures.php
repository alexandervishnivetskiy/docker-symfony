<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;


class ClientFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $client = new Client();
        $client->setName('Sasha');
        $client->setCountry('s');
        $client->setTelephone('3424');
        $client->setEmail('asd');
        $client->setTelephone($faker->phoneNumber);
        $client->setCountry($faker->country);
        $client->setEmail($faker->email);
        $manager->persist($client);
        $manager->flush();

    }


}