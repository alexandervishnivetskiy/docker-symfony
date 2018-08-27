<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Services\CountrySelector;
use App\Services\StatisticObserver;


class UIController extends Controller
{
    /**
     * @Route("/", name="home_page")
     */
    public function renderClientTable(Request $request)
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        $clientName = ['Please, select client   ...' => '0'];
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
            if ($id['select'] == 0) {
                $this->redirectToRoute('home_page');
            } else {
                $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(array('id' => $id));
                $clientID = $clients[0]->getID();
                $reportsArray = array();
                $reports = $this->getDoctrine()->getRepository(Report::class)->findAllReportsByClientID($clientID);

                foreach ($reports as $report) {
                    $arr = array();
                    $arr['id'] = $report->getID();
                    $arr['name'] = $report->getName();
                    $arr['deviceID'] = $report->getDeviceID();
                    $arr['description'] = $report->getDescription();
                    $arr['client'] = $report->getClient()->getName();
                    $reportsArray[] = $arr;
                }
                return $this->render('reports/reports.html.twig', array('reports' => $reportsArray));
            }
        }
        $urls = ['new_client' => 'client/new', 'new_report' => 'report/new'];

        return $this->render('clients/index.html.twig', array('form' => $form->createView(), 'urls' => $urls));
    }

    /**
     * @Route("/client/new", name="new_client_page")
     */
    public function addNewClient(Request $request, CountrySelector $countrySelector)
    {
        $countryList = $countrySelector->countrySelector();
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('telephone', TelType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('country', ChoiceType::class, array('choices' => $countryList, 'attr' => array('class' => 'form-control mb-3')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('label' => 'Add new client', 'attr' => array('class' => 'form-control bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = $form->getData();
            $client->setName($data['name']);
            $client->setTelephone($data['telephone']);
            $client->setCountry($data['country']);
            $client->setEmail($data['email']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            $clientArray = json_decode(json_encode($client), true);


            return $this->render('clients/client_success.html.twig', array('client' => $clientArray));
        }
        return $this->render('clients/new_client.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/report/new", name="new_report_page")
     */
    public function addNewReport(Request $request)
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        $clientName = ['Please, select client...' => 0];

        foreach ($clients as $client) {
            $clientName[$client->getName()] = $client->getID();
        }

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('device_id', TelType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('errorDescription', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('client', ChoiceType::class, array('choices' => $clientName, 'attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('label' => 'Add new report', 'attr' => array('class' => 'form-control bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['client'] != 0) {
                $report = new Report();
                $report->setName($data['name']);
                $report->setDeviceID($data['device_id']);
                $report->setDescription($data['errorDescription']);
                $client = $this->getDoctrine()->getRepository(Client::class)->find($data['client']);
                $report->setClient($client);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($report);
                $entityManager->flush();
                $reportArray = json_decode(json_encode($report), true);
                return $this->render('reports/report_success.html.twig', array('report' => $reportArray));
            } else {
                return $this->redirectToRoute('new_report_page');
            }
        }
        return $this->render('reports/new_report.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("client/zero_reports/")
     */
    public function showClientWithoutReports(StatisticObserver $statisticObserver)
    {
        $clients = $statisticObserver->showClientWithoutReports();
        return $this->render('clients/without_reports.html.twig', array('clients' => $clients));
    }

    /**
     * @Route("client/top/")
     */
    public function test(StatisticObserver $statisticObserver)
    {
        $topClients = $statisticObserver->showTopThreeClient();
        return $this->render('clients/top_clients.html.twig', array('clients' => $topClients));
    }
}