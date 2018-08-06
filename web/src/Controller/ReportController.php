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
    const ERROR = ['Error' => 'You entered undefined report\'s ID or this report has been deleted earlier'];

    /**
     * @Route("/api/reports", name="reports_list")
     * @Method({"GET"})
     */
    public function showAll()
    {
        $reports = $this->getDoctrine()->getRepository(Report::class)->findAll();
        if (!empty($reports)) {
            return new Response(json_encode($reports), Response::HTTP_OK);
        } else {
            return new Response(['Error' => 'Your request did not return any results'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/reports/{id}", requirements={"id"="\d+"})
     * @Method({"GET"})
     */
    public function showReport($id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        if (!empty($report)) {
            return new Response(json_encode($report), Response::HTTP_OK);
        } else {
            return new Response(json_encode(self::ERROR), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("api/reports/delete/{id}", requirements={"id"="\d+"})
     */
    public function deleteReport(Request $request, $id)
    {
        $report = $this->getDoctrine()->getRepository(Report::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!empty($report)) {
            $entityManager->remove($report);
            $entityManager->flush();
            return new Response(json_encode($report), Response::HTTP_OK);
        } else {
            return new Response(json_encode(self::ERROR), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("api/reports/new")
     * @Method({"POST"})
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
//            throw $this->createNotFoundException('You did not fill all requested fields');
            return new Response(json_encode(['Error' => 'You did not fill all requested fields']), Response::HTTP_INTERNAL_SERVER_ERROR);

        } else {
            $report->setName($valueObj->name);
            $report->setClient($valueObj->client);
            $report->setDeviceID($valueObj->deviceID);
            $report->setDescription($valueObj->err_desc);
            $entityManager->persist($report);
            $entityManager->flush();
            return new Response(json_encode($report), Response::HTTP_OK);
        }
    }

    /**
     * @Route("api/reports/edit/{id}", requirements={"id"="\d+"})
     * @Method({"POST"})
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
            $report->setDescription($valueObj->err_desc);

            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($report);
            $entityManager->flush();
            return new Response(json_encode($report), Response::HTTP_OK);
        } else {
            return new Response(json_encode(self::ERROR), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}