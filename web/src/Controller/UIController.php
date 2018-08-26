<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Report;
use function PHPSTORM_META\type;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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

            return $this->render('clients/reports.html.twig', array('reports' => $reportsArray));
        }
        return $this->render('clients/index.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/client/new")
     */
    public function addNewClient(Request $request)
    {
        $sql = "SELECT country_name FROM testDB.apps_countries";
        $entityManager = $this->getDoctrine()->getManager();
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $countries = $stmt->fetchAll();

        $countryList = array();
        foreach ($countries as $country) {
            $countryList[$country['country_name']] = $country['country_name'];
        }

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

            $entityManager->persist($client);
            $entityManager->flush();
        }
        return $this->render('clients/newClient.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/report/new")
     */
    public function addNewReport(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        $clientName = array();
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
            $report = new Report();
            $data = $form->getData();
            $report->setName($data['name']);
            $report->setDeviceID($data['device_id']);
            $report->setDescription($data['errorDescription']);
            $client = $this->getDoctrine()->getRepository(Client::class)->find($data['client']);
            $report->setClient($client);

            $entityManager->persist($report);
            $entityManager->flush();
        }
        return $this->render('clients/newReport.html.twig', array('form' => $form->createView()));
    }


}