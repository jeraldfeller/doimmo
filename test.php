<?php
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';
$scraper = new Scraper();
$url = 'https://www.athome.lu/en/buy/apartment/luxembourg/id-5999822.html';
$url = 'https://www.athome.lu/en/buy/house/filsdorf/id-5997687.html';
$url = 'https://www.athome.lu/en/buy/house/filsdorf/id-5997545.html';
$url = 'https://www.athome.lu/en/buy/house/noertzange/id-5999584.html';
$url = 'https://www.athome.lu/en/buy/apartment/frisange/id-5999370.html';
$url = 'https://www.athome.lu/en/buy/apartment/mersch/id-5741108.html';
$url = 'https://www.athome.lu/en/buy/apartment/luxembourg/id-5997759.html';
$url = 'https://www.athome.lu/en/buy/apartment/luxembourg/id-5983027.html';
$url = 'https://www.athome.lu/en/buy/apartment/bettembourg/id-6006763.html';
$htmlDataDetails = $scraper->curlTo($url);
if($htmlDataDetails['html']){
    $htmlDetails = str_get_html($htmlDataDetails['html']);
    if($htmlDetails){
        $scripts = $htmlDetails->find('script');
        if(count($scripts) > 0){
            for($s = 0; $s < count($scripts); $s++){
                $t = $scripts[$s]->getAttribute('type');
                $src = $scripts[$s]->getAttribute('src');
                if($t == false && $src == false){
                    $contentsJson = rtrim(trim(str_replace('window.__INITIAL_STATE__ =', '', $scripts[$s]->innertext)), ';');
                    $contents = json_decode($contentsJson, true)['detail'];
                    $desc = $contents['description']['fr'];
                    if(strpos($desc, "m2") != false){
                        $needle = "m2";
                    }else if(strpos($desc, "m 2") != false){
                        $needle = "m 2";
                    }else if(strpos($desc, "m²") != false){
                        $needle = "m²";
                    }else{
                        $needle = '';
                    }
                    if($needle == ''){
                        $size = 0;
                    }else{
                        $size = getM2($desc, $needle);
                    }
                }
            }
        }
    }
}

function getM2($string, $needle){
    $lastPos = 0;
    $sizes = array();

    $lastPos = 0;
    $positions = array();

    while (($lastPos = strpos($string, $needle, $lastPos))!== false) {
        $positions[] = $lastPos;
        $lastPos = $lastPos + strlen($needle);
    }

    for($x = 0; $x < count($positions); $x++){
        $desc = explode(' ',substr_replace($string, '', $positions[$x]));
        $size = $desc[count($desc)-1];
        if($size == ''){
            $size = $desc[count($desc)-2];
        }
        $sizes[] = preg_replace("/[^0-9.]/", "", str_replace(',', '.', $size));
    }

    return max($sizes);
}