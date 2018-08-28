<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Report;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UIController extends Controller
{
    /**
     * @Route("/", name="home_page")
     */
    public function renderClientTable(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('client', EntityType::class, array('class' => Client::class, 'choice_label' => 'name', 'attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('attr' => array('class' => 'form-control mt-3 bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($data['client'] == null) {
                $this->redirectToRoute('home_page');
            } else {
                $clientID = json_decode(json_encode($data['client']))->id;
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
        return $this->render('clients/index.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/client/new", name="new_client_page")
     */
    public function addNewClient(Request $request, CountrySelector $countrySelector, ValidatorInterface $validator)
    {
        $countryList = $countrySelector->countrySelector();
        $form = $this->createFormBuilder(new Client())
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('telephone', TelType::class, array('attr' => array('class' => 'form-control mb-3', 'placeholder' => 'xxx-xxx-xx-xx')))
            ->add('country', ChoiceType::class, array('choices' => $countryList, 'attr' => array('class' => 'form-control mb-3')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('label' => 'Add new client', 'attr' => array('class' => 'form-control bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = json_decode(json_encode($form->getData()), true);
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
        $form = $this->createFormBuilder(new Report())
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('device_id', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('description', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('client', EntityType::class, array('class' => Client::class, 'choice_label' => 'name', 'attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('label' => 'Add new report', 'attr' => array('class' => 'form-control bg-success')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = json_decode(json_encode($form->getData()));
            if ($data->client == null) {
                $this->redirectToRoute('new_report_page');
            } else {
                $clientName = $data->client->name;

                $client = $this->getDoctrine()->getRepository(Client::class)->findClientByName($clientName);

                $report = new Report();
                $report->setName($data->name);
                $report->setDeviceID((int)$data->deviceID);
                $report->setDescription($data->err_desc);
                $report->setClient($client);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($report);
                $entityManager->flush();
                $reportArray = json_decode(json_encode($report), true);

                return $this->render('reports/report_success.html.twig', array('report' => $reportArray));
            }
        }
        return $this->render('reports/new_report.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("client/info", name="client_page")
     */
    public function showClientInfo(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('client', EntityType::class, array('class' => Client::class, 'choice_label' => 'name', 'attr' => array('class' => 'form-control mb-3')))
            ->add('submit', SubmitType::class, array('attr' => array('class' => 'form-control mt-3 bg-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($data['client'] == null) {
                $this->redirectToRoute('client_page');
            } else {
                $client = json_decode(json_encode($data['client']), true);
                return $this->render('clients/client_info.html.twig', array('client' => $client));
            }
        }
        return $this->render('clients/index.html.twig', array('form' => $form->createView()));
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
    public function showTopClients(StatisticObserver $statisticObserver)
    {
        $topClients = $statisticObserver->showTopThreeClient();
        return $this->render('clients/top_clients.html.twig', array('clients' => $topClients));
    }
}