<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UIController extends Controller
{
    /**
     * @Route("/", name="home_page")
     */
    public function renderClientTable(Request $request)
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        $clientName = array();
        foreach ($clients as $client) {
            $clientName[$client->getName()] = $client->getID();
        }

        $form = $this->createFormBuilder()
            ->add('select', ChoiceType::class,
                array('choices' => $clientName, 'attr' => array('class' => 'form-control')))
            ->add('submit', SubmitType::class,
                array('attr' => array('class' => 'form-control mt-3 bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->getData();
            $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(array('id' => $id));
            $clientID = $clients[0]->getID();
            $reportsArray = array();
            $reports = $this->getDoctrine()->getRepository(Report::class)->findAllReportByClientID($clientID);

//            $reports = $clients[0]->getReports();
            foreach ($reports as $report) {
                $arr = array();
                $arr['id'] = $report->getID();
                $arr['name'] = $report->getName();
                $arr['deviceID'] = $report->getDeviceID();
                $arr['description'] = $report->getDescription();
                $arr['client'] = $report->getClient()->getName();
                $reportsArray[] = $arr;
            }

            return $this->render('clients/reports.html.twig', array('reports' => $reportsArray));
        }
        return $this->render('clients/index.html.twig', array('form' => $form->createView()));
    }
}