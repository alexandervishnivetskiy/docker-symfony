<?php

namespace App\Services;

use App\Entity\Report;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;


class StatisticObserver
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function showClientWithoutReports()
    {

        $clients = $this->entityManager->getRepository(Client::class)->findAll();
        $clientsID = array();
        foreach ($clients as $client) {
            $clientsID[] = $client->getID();
        }
        $emptyIDs = array();
        foreach ($clientsID as $id) {
            $reports = $this->entityManager->getRepository(Report::class)->findAllReportsByClientID($id);
            if (empty($reports)) {
                $emptyIDs[] = $id;
            }
        }
        $clientsArr = array();
        foreach ($emptyIDs as $emptyID) {
            $client = $this->entityManager->getRepository(Client::class)->find($emptyID);
            $clientsArr[] = $client;
        }
        return $clientsArr;
    }

    public function showTopThreeClient()
    {
        $reports = $this->entityManager->getRepository(Report::class)->findAll();
        $clients = [];
        foreach ($reports as $report) {
            $clients[] = $report->getClient()->getName();
        }
        $clients = array_count_values($clients);
        asort($clients);

        $keyArray = array_keys($clients);
        for ($i = 0; $i < 3; $i++) {
            $topClient['count'] = array_pop($clients);
            $topClient['name'] = array_pop($keyArray);
            if ($topClient['count'] == null || $topClient['name'] == null) {
                continue;
            }
            $topClients[] = $topClient;
        }
        return $topClients;
    }
}