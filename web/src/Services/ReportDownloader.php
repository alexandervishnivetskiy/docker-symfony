<?php

namespace App\Services;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class ReportDownloader
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importReports(){
        $path = '/var/www/html/reports/reports.csv';
        $reports = $this->entityManager->getRepository(Report::class)->findAll();
        $reportsArray = array();
        foreach ($reports as $report){
            $arr = array();
            $arr['id'] = $report->getID();
            $arr['name'] = $report->getName();
            $arr['deviceID'] = $report->getDeviceID();
            $arr['description'] = $report->getDescription();
            $reportsArray[] = $arr;
        };
        $file = fopen($path, 'w');
        foreach ($reportsArray as $report) {
            fputcsv($file, $report);
        }
    }

}