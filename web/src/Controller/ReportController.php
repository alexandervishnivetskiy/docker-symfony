<?php

namespace App\Controller;

use App\Entity\Report;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Dotenv\Dotenv;


//$dotenv = new Dotenv();
//$dotenv->load(__DIR__ . '/../../../.env');

class ReportController extends Controller
{

    /**
     * @Route("/env")
     */
    public function loadEnv(){
        $dotenv = new Dotenv();
//        $dotenv->load( '../../.env');
//        $_ENV['DATABASE_URL'] = 'DATABASE_URL=mysql://root:root@mysql:3306/testDB';
        var_dump($_ENV);

        return new Response();
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

        $reports = json_encode($reports);
        return new Response($reports, 200);
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
        $report = json_encode($report);
        return new Response($report, 200);
    }

    /**
     * @Route("api/reports/delete/{id}", requirements={"id"="\d+"})
     */
    public function deleteReport(Request $request, $id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!$report) {
            throw $this->createNotFoundException('No reports found for id ' . $id);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($report);
        $report = json_encode($report);
        $entityManager->flush();


        return new Response($report, 200);

    }

    /**
     * @Route("api/reports/new")
     * @Method({"POST"})
     */
    public
    function newReport(Request $request)
    {
        $report = new Report();
        $entityManager = $this->getDoctrine()->getManager();

        $valueObj = $request->getContent();
        $valueObj = json_decode($valueObj);

        if (empty($valueObj->name) ||
            empty($valueObj->client) ||
            empty($valueObj->deviceID) ||
            empty($valueObj->err_desc)) {
            throw $this->createNotFoundException('You did not fill all requested fields');
        } elseif (!is_int($valueObj->deviceID)) {
            throw $this->createNotFoundException('You must add integer value to deviceID field');
        }

        $report->setName($valueObj->name);
        $report->setClient($valueObj->client);
        $report->setDeviceID($valueObj->deviceID);
        $report->setDescription($valueObj->err_desc);

        $entityManager->persist($report);
        $entityManager->flush();

        $report = json_encode($report);
        return new Response($report, 200);

    }

    /**
     * @Route("api/reports/edit/{id}", requirements={"id"="\d+"})
     * @Method({"POST"})
     */

    public function editReport(Request $request, $id)
    {
        $report = new Report();
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!$report) {
            throw $this->createNotFoundException('No reports found for id ' . $id);
        }
        $valueObj = json_decode($request->getContent());
        $report->setName($valueObj->name);
        $report->setClient($valueObj->client);
        $report->setDeviceID($valueObj->deviceID);
        $report->setDescription($valueObj->err_desc);

        $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($report);
        $entityManager->flush();
        $report = json_encode($report);
        return new Response($report, 200);
    }

}