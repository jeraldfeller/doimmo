<?php
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';
$scraper = new Scraper();
$date = date('Y-m-d_H-i');
$csv = 'export-'.$date.'.csv';
$sort = 'price';
$y = 'DESC';
$table = $scraper->getInfo($sort, $y, $_GET['city'], $_GET['commune'], $_GET['region'], $_GET['type'], $_GET['is_new_property'], $_GET['garage'], $_GET['terrace'], $_GET['garden'], $_GET['category'], $_GET['greenCities'], $_GET['tagOut'], $_GET['priceRange'], $_GET['percRange'], $_GET['table']);

$data[] = implode('","', array(
    'Ref ID',
    'Title',
    'City',
    'Commune',
    'Region',
    'Cat Type',
    'Type',
    'Property',
    'Size',
    'Price',
    'P/S',
    'P/S B',
    'No. Bedrooms',
    'No. Bathrooms',
    'Garage',
    'Terrace',
    'Garden',
    'Period',
    'Url'
));
foreach($table as $row){
    $pricePerSqM = ($row['size'] != 0 ? ($row['price'] / $row['size']) : 0);
    $psb = ($row['average']['averageSize'] != 0 ? ($row['average']['averagePrice'] / $row['average']['averageSize']) : 0);
    $data[] = implode('","', array(
        $row['ref_id'],
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['title']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['city']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['commune']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['region']))))),
        $row['category'],
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['type']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['is_new_property']))))),
        ($row['size'] != 0 ? round($row['size']) : 'zero'),
        round($row['price']),
        round($pricePerSqM),
        round($psb),
        $row['no_of_bedrooms'],
        $row['no_of_bathrooms'],
        $row['garage'],
        $row['terrace'],
        $row['garden'],
        $row['date_created'],
        $row['url'],
    ));
}


$file = fopen($csv,"w");
foreach ($data as $line){
    fputcsv($file, explode('","',$line));
}
fclose($file);



// Output CSV-specific headers

header('Content-Type: text/csv; charset=utf-8');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($csv) . "\"");
readfile($csv);