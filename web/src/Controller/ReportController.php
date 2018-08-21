<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Report;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends Controller
{
    /**
     * @Route("/api/client/delete/{id}")
     * @Method({"GET"})
     */
    public function deleteClient($id, EntityManagerInterface $entityManager)
    {
        $client = $this->getDoctrine()->getRepository(Client::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException('No client found for id' . " $id");
        }
        $entityManager->remove($client);
        $entityManager->flush();
        return new JsonResponse($client);
    }

    /**
     * @Route("/api/client/{id}")
     * @Method({"GET"})
     */
    public function showAllReportsOfClient($id)
    {
        $client = $this->getDoctrine()->getRepository(Client::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException('No client found for id' . " $id");
        }
        $reports = $client->getReports();


        $reportsArray = array();
        foreach ($reports as $report) {
            $arr = array();
            $arr['id'] = $report->getID();
            $arr['name'] = $report->getName();
            $arr['deviceID'] = $report->getDeviceID();
            $arr['description'] = $report->getDescription();
            $arr['client'] = $report->getClient()->getName();
            $reportsArray[] = $arr;
        }
        if (empty($reportsArray)){
            throw $this->createNotFoundException('The client with id ' . $id . ' doesn\'t have any reports');
        }
        return new JsonResponse($reportsArray);
    }

    /**
     * @Route("/api/reports/download")
     * @Method({"GET"})
     */
    public function importCSV()
    {
        $downloader = $this->get('report.downloader');
        $downloader->importReports();
        return new Response();
    }

    /**
     * @Route("/search/{name}")
     * @Method({"GET"})
     */
    public function searchReports($name)
    {
        $reports = $this->getDoctrine()->getRepository(Report::class)->findAllReportByName($name);
        if (!$reports) {
            throw $this->createNotFoundException("No reports found, please correct or specify report name");
        }
        return new JsonResponse($reports, 200);
    }

    /**
     * @Route("/api/reports", name="reports_list")
     * @Method({"GET"})
     */
    public function showAll()
    {
        $reports = $this->getDoctrine()->getRepository(Report::class)->findAll();
        if (!$reports) {
            throw $this->createNotFoundException('No reports found');
        }
        return new JsonResponse($reports, 200);
    }

    /**
     * @Route("/api/reports/{id}", requirements={"id"="\d+"})
     * @Method({"GET"})
     */
    public function showReport($id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);

        if (!$report) {
            throw $this->createNotFoundException('No reports found for id ' . $id);
        }
        return new JsonResponse($report, 200);
    }

    /**
     * @Route("/api/reports/delete/{id}", requirements={"id"="\d+"})
     */
    public function deleteReport(Request $request, $id)
    {

        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!$report) {
            throw $this->createNotFoundException('No reports found for id ' . $id);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($report);
        $entityManager->flush();

        return new JsonResponse($report, 200);
    }

    /**
     * @Route("/api/reports/new")
     * @Method({"POST"})
     */

    public function newReport(Request $request)
    {
        $report = new Report();
        $entityManager = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $report->setName($request->request->get('name'));
        $report->setClient($request->request->get('client'));
        $report->setDeviceID($request->request->get('deviceID'));
        $report->setDescription($request->request->get('err_desc'));

        $entityManager->persist($report);
        $entityManager->flush();

        return new JsonResponse($report, 200);
    }

    /**
     * @Route("/api/reports/edit/{id}", requirements={"id"="\d+"})
     */

    public function editReport(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!$report) {
            throw $this->createNotFoundException('No reports found for id ' . $id);
        }
        $report->setName($request->request->get('name'));
        $report->setClient($request->request->get('client'));
        $report->setDeviceID($request->request->get('deviceID'));
        $report->setDescription($request->request->get('err_desc'));

        $entityManager->flush();
        return new JsonResponse($report, 200);
    }
}