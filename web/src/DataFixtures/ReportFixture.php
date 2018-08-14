<?php

namespace App\DataFixtures;

use App\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class ReportFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 1; $i++) {
            $report = new Report();
            $report->setName('report ');
            $report->setClient('client');
            $report->setDeviceID(rand(1, 100));
            $report->setDescription('Test description');
            $manager->persist($report);
        }
        $manager->flush();

    }


}
