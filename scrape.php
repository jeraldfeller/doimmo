<?php
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
require 'Model/Init.php';
require 'Model/Scraper.php';
require 'simple_html_dom.php';
$letters=array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', '$', ',', ' ');
$scraper = new Scraper();
if($scraper->isActive() == true){
    $data = array();
    $pg = $scraper->getNextPage();
    $mainUrl = 'https://www.athome.lu';
    $url = 'https://www.athome.lu/en/srp/?distance=49.6262074,6.15028019915565,15&tr=buy&price_min=25000&price_max=850000&sort=date_desc&has_excluded_borders=true&q=dea70e87&loc=L10-kirchberg&ptypes=house,flat&page='.$pg;
    $bol = true;
    $htmlData = $scraper->curlTo($url);
    if($htmlData['html']){
        $html = str_get_html($htmlData['html']);
        if($html){
            if($pg == 1){
                $header = $html->find('.intro', 0);
                $totalListing = trim(str_replace($letters, '', $header->find('h2', 0)->plaintext));
                if($html->find('.last', 0)){
                    $totalPage = trim($html->find('.last', 0)->plaintext);
                }else{
                    $totalPage = 100;
                }
                $scraper->setTotalPage($totalPage, $totalListing);
            }
            $list = $html->find('.listItemMainCont');
            if(count($list) > 0){
                for($x = 0; $x < count($list); $x++){
                    $title = trim($list[$x]->find('h3', 0)->plaintext);
                    $goto = $mainUrl.$list[$x]->find('.goto', 0)->find('a', 0)->getAttribute('href');
                    $keyData = getKeyData($list[$x]->find('.goto', 0)->find('a', 0)->getAttribute('href'));
                    $refId = $keyData['refId'];
                    // details page
                    $htmlDataDetails = $scraper->curlTo($goto);
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
                                        $category = ($contents['immotype']['portal_group'] == 'flat' ? 'Apartment' : 'House');
                                        $refId = $contents['listingId'];
                                        $price = $contents['price'];
                                        $region = $contents['geo']['region'];
                                        $city = $contents['geo']['cityName'];
                                        $citySlug = $contents['search_data']['address'];
                                        $type = $contents['propertySubType'];
                                        $garage = $contents['characteristics']['has_garage'];
                                        $terrace = $contents['characteristics']['has_terraces'];
                                        $garden = $contents['characteristics']['has_garden'];
                                        $numBedRooms = $contents['characteristics']['min_bedrooms_count'];
                                        $bathroom = $contents['characteristics']['bathrooms_count'];
                                        $yearOfConstruction = (isset($contents['buildingYear']) ? $contents['buildingYear'] : 0);
                                        $isNewProperty = $contents['meta']['is_new_property'];
                                        $size = $contents['characteristics']['property_max_surface'];

                                        if($size == 0){
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
                                        echo $refId . ' - ' . $type. ' ' . $title . ' ' . $price . "\n";

                                    }
                                }
                            }
                        }
                    }

                    $data[] = array(
                        'refId' => $refId,
                        'title' => $title,
                        'region' => $region,
                        'city' => $city,
                        'citySlug' => $citySlug,
                        'title' => $title,
                        'price' => $price,
                        'type' => $type,
                        'category' => $category,
                        'size' => $size,
                        'numberOfBedrooms' => $numBedRooms,
                        'bathroom' => $bathroom,
                        'terrace' => ($terrace == true ? 'yes' : 'no'),
                        'garage' => ($garage == true ? 'yes' : 'no'),
                        'garden' => ($garden == true ? 'yes' : 'no'),
                        'isNewProperty' => ($isNewProperty == true ? 'New construction ' : 'established property'),
                        'yearOfConstruction' => $yearOfConstruction,
                        'url' => $goto
                    );
                }
            }else{
                $col = false;
            }
        }
    }

    $scraper->recordData($data);
    $scraper->setNextPage($pg + 1);
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

function getKeyData($url){
    $urlArr = explode('-', $url);
    $refId = trim(str_replace('.html', '', $urlArr[1]));
    $urlArr = explode('/', $url);
    $type = trim($urlArr[3]);
    return array('refId' => $refId, 'type' => $type);
}