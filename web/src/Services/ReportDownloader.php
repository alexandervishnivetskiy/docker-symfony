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

    public function importReports()
    {
        $reports = $this->entityManager->getRepository(Report::class)->findAll();
        $reportArray = array();
        foreach ($reports as $report) {
            $arr = array();
            $arr['id'] = $report->getID();
            $arr['name'] = $report->getName();
            $arr['deviceID'] = $report->getDeviceID();
            $arr['description'] = $report->getDescription();
            $reportArray[] = $arr;
        };

        $file = fopen('php://output', 'w');
        header("Content-Disposition: attachment; filename=$file");

        foreach ($reportArray as $report) {
            fputcsv($file, $report);
        }
        fclose($file);
    }
}