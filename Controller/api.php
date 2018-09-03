<?php
require '../Model/Init.php';
require '../Model/Scraper.php';
$scraper = new Scraper();

switch ($_GET['action']) {
    case 'get-options':
        echo json_encode($scraper->getOptions());
        break;
    case 'get-info':
        echo json_encode($scraper->getInfo($_GET['sort'], $_GET['y'], $_GET['city'], $_GET['commune'], $_GET['region'], $_GET['type'], $_GET['is_new_property'], $_GET['garage'], $_GET['terrace'], $_GET['garden'], $_GET['category'], $_GET['greenCities'], $_GET['tagOut'], $_GET['priceRange'], $_GET['percRange'], $_GET['table']));
        break;
    case 'scan':
        echo json_encode($scraper->activateScan());
        break;
    case 'tag':
        $data = json_decode($_POST['param'], true);
        $scraper->flagInfo($data);
        break;
    case 'check-process':
        echo json_encode($scraper->checkStatus());
        break;
    case 'price-history':
        $data = json_decode($_POST['param'], true);
        echo json_encode($scraper->getPriceHistory($data['id']));
        break;
    case 'get-last-schedule':
        echo json_encode($scraper->getLastSchedule());
        break;
    case 'add-comment':
        $data = json_decode($_POST['param'], true);
        echo json_encode($scraper->addComment($data));
        break;
}