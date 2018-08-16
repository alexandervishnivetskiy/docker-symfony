<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Report;
use App\Entity\Client;


class RelationsController extends Controller
{
    /**
     * @Route("/api/reports/{client}")
     */

    public function showReportsByClient($client){

    }
}