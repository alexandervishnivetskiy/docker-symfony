<?php


namespace App\Services;


class ClientManager
{

    public static function showStat($array){
        $count = 0;
        foreach ($array as $arr){
            $count++;
        }
        echo $count;
    }
}