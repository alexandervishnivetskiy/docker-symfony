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
     * @Route("/test")
     */
    public function test()
    {
        $servername = "mysql";
        $username = "root";
        $password = "root";

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=testDB", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        }
        catch(\PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }

        return new Response("<h1>Ура!</h1>");
    }
}