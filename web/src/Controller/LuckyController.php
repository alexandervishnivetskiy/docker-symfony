<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 05.08.18
 * Time: 20:00
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class LuckyController
{

    /**
     *  @Route("/test")
     */
    public function test()
    {
        $number = random_int(0, 100);

        return new Response("<h1>Ура!</h1>");
    }
}