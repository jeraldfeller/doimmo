<?php
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';
$scraper = new Scraper();
$url = 'https://www.athome.lu/en/srp/?distance=49.6262074,6.15028019915565,15&tr=buy&price_min=25000&price_max=850000&sort=date_desc&has_excluded_borders=true&q=dea70e87&loc=L10-kirchberg&ptypes=house,flat&page=1';
$htmlData = $scraper->curlTo($url);
if($htmlData['html']) {
    $html = str_get_html($htmlData['html']);
    if ($html) {
        $totalPage = trim($html->find('.last', 0)->plaintext);
    }else{
        $totalPage = 100;
    }
}else{
    $totalPage = 100;
}

$scraper->reset($totalPage);
