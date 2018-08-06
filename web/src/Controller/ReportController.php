<?php

namespace App\Controller;

use App\Entity\Report;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ReportController extends Controller
{
    /**
     * @Route("/api")
     */
    public function testFunc(){
        return new Response('Working!');
    }


    const SUCCESS = array(
        'response' => 'HTTP/1.0 201 OK',
        'status' => 'success',
        'message' => 'request is valid'
    );
    const ERROR = array(
        'response' => 'HTTP/1.0 500 Internal Server Error',
        'status' => 'error',
        'message' => 'request is not valid'
    );

    /**
     * @Route("/api/reports", name="reports_list")
     * @Method({"GET"})
     */
    public function showAll()
    {
        $reports = $this->getDoctrine()->getRepository(Report::class)->findAll();
        if (!empty($reports)) {
            $allReports = array();
            foreach ($reports as $report) {
                $allReports[] = $report->getReportsArray();
            }
            $allReports[count($allReports)] = self::SUCCESS;
            return new Response(json_encode($allReports));
        } else {
            return new Response(json_encode(self::ERROR));
        }
    }

    /**
     * @Route("/api/reports/{id}", name="single_report", requirements={"id"="\d+"})
     * @Method({"GET"})
     */
    public function showReport($id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!empty($report)) {
            $singleReport = array($report->getReportsArray(), self::SUCCESS);
            return new Response(json_encode($singleReport));
        } else {
            return new Response(json_encode(self::ERROR));
        }
    }

    /**
     * @Route("api/reports/delete/{id}", name="delete_report", requirements={"id"="\d+"})
     * Method("DELETE")
     */
    public function deleteReport(Request $request, $id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();

        if (!empty($report)) {
            $entityManager->remove($report);
            $entityManager->flush();
            return new Response(json_encode(array($report->getReportsArray(), self::SUCCESS)));
        } else {
            $reason = array('reason' => 'you entered undefined report ID');
            return new Response(json_encode(array_merge(self::ERROR, $reason)));
        }
    }

    /**
     * @Route("api/reports/new", name="new_report")
     * Method({"POST"})
     */
    public function newReport(Request $request)
    {
        $report = new Report();
        $entityManager = $this->getDoctrine()->getManager();

        $valueObj = json_decode($request->getContent());
        if (empty($valueObj->name) ||
            empty($valueObj->client) ||
            empty($valueObj->deviceID) ||
            empty($valueObj->err_desc)) {
            return new Response(json_encode(self::ERROR));
        }
        $report->setName($valueObj->name);
        $report->setClient($valueObj->client);
        $report->setDeviceID($valueObj->deviceID);
        $report->setDescription($valueObj->err_desc);

        $entityManager->persist($report);
        $entityManager->flush();
        return new Response(json_encode(array($report->getReportsArray(), self::SUCCESS)));

    }

    /**
     * @Route("api/reports/edit/{id}", name="edit_report", requirements={"id"="\d+"})
     */

    public function editReport(Request $request, $id)
    {
        $report = new Report();
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!empty($report)) {
            $valueObj = json_decode($request->getContent());
            $report->setName($valueObj->name);
            $report->setClient($valueObj->client);
            $report->setDeviceID($valueObj->deviceID);
            $report->setDescription($valueObj->description);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return new Response(json_encode(array($report->getReportsArray(), self::SUCCESS)));
        } else {
            return new Response(json_encode(self::ERROR));
        }
    }
}