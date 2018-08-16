<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 16.08.18
 * Time: 15:03
 */

namespace App\Services;


class ReportDownloader
{
    public function importReports($file, $list){
        foreach ($list as $fields) {
            fputcsv($file, $fields);
        }
    }

}