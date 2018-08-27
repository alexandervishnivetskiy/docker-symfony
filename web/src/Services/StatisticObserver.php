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
        $clients = array();
        foreach ($reports as $report) {
            $clients[] = $report->getClient()->getName();
        }

        $clients = json_decode(json_encode($clients), true);
        $stat = array_count_values($clients);
        arsort($stat);
        $stat = array_keys($stat);
        $topThreeNames = array();
        $i = 0;
        foreach ($stat as $s) {
            while ($i < 3) {
                $topThreeNames[] = array_shift($stat);
                $i++;
            }
        }
        $topClients = array();
        foreach ($topThreeNames as $topClientName) {
            $arr = $this->entityManager->getRepository(Client::class)->findClientByName($topClientName);
            $topClients[] = json_decode(json_encode($arr[0]), true);
        }

        $reportsQuantity = array();
        foreach ($topClients as $topClient) {
            $reportsQuantity[] = $this->entityManager->getRepository(Report::class)->findAllReportsByClientID($topClient['id']);
        }

        $countArray = array();
        foreach ($reportsQuantity as $reportQuantity) {
            $countArray[] = count($reportQuantity);
        }

        foreach ($topClients as $topClient) {
            $topClient['count'] = $countArray[0];
        }

        if (!empty($topClients)) {
            $topClients[0]['count'] = $countArray[0];
            $topClients[1]['count'] = $countArray[1];
            $topClients[2]['count'] = $countArray[2];
        }
        return $topClients;
    }
}