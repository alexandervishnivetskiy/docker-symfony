<?php

namespace App\Services;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;


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
        $reports = json_encode($reports);
        $reports = json_decode($reports, true);
        $file = fopen($path, 'w');
        foreach ($reports as $report) {
            fputcsv($file, $report);
        }
    }

}