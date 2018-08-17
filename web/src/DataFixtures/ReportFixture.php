<?php

namespace App\DataFixtures;

use App\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;



class ReportFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $report = new Report();
        $report->setName( $faker->name);
        $report->setClientName('Sasha');
        $report->setDeviceID($faker->numberBetween(0,100));
        $report->setDescription($faker->text);
        $manager->persist($report);

        $manager->flush();

    }


}
