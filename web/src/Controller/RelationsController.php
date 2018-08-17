<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RelationsController extends Controller
{
    /**
     * @Route("/api/reports/{client}")
     */

    public function showReportsByClient($client){

    }
}