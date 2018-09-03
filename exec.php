<?php
require 'Model/Init.php';
require 'Model/Scraper.php';
$scraper = new Scraper();

$infos = $scraper->getAllInfo();
foreach($infos as $row){
    $id = $row['id'];
    $city = strtolower($row['city']);
    $commune = $scraper->getCommune($city);
    if($commune){
        $commune = $commune['commune'];
    }else{
        $commune = $city;
    }

    $scraper->updateInfoCommune($id, ucfirst($commune));
    echo $city . ' - ' . $commune . '<br>';
}