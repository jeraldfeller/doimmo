<?php

/**
 * Created by PhpStorm.
 * User: Grabe Grabe
 * Date: 8/20/2018
 * Time: 5:30 AM
 */
class Scraper
{
    public $debug = TRUE;
    protected $db_pdo;

    public function getOptions(){
        return array(
            'city' => $this->getOptionList('city'),
            'commune' => $this->getOptionList('commune'),
            'region' => $this->getOptionList('region'),
            'type' => $this->getOptionList('type'),
            'property' => $this->getOptionList('is_new_property')
        );
    }

    public function getOptionList($column){
        $pdo = $this->getPdo();
        $sql = 'SELECT DISTINCT `'.$column.'` FROM `info`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $return[] = $row[$column];
        }
        $pdo = null;

        return $return;
    }

    public function getInfo($sort, $y, $city, $commune, $region, $type, $is_new_property, $garage, $terrace, $garden, $category, $greenCities, $tagOut, $priceRange, $percRange, $table, $offset = 0, $limit = 20){
        $pdo = $this->getPdo();

        $cityQuery = ($city == 'any' ? '' : ' AND i.city="'.$city.'"');
        $communeQuery = ($commune == 'any' ? '' : ' AND i.commune="'.$commune.'"');
        $regionQuery = ($region == 'any' ? '' : ' AND i.region="'.$region.'"');
        $typeQuery = ($type == 'any' ? '' : ' AND i.type="'.$type.'"');
        $propertyQuery = ($is_new_property == 'any' ? '' : ' AND i.is_new_property="'.$is_new_property.'"');
        $garageQuery = ($garage == 'any' ? '' : ' AND i.garage="'.$garage.'"');
        $terraceQuery = ($terrace == 'any' ? '' : ' AND i.terrace="'.$terrace.'"');
        $gardenQuery = ($garden == 'any' ? '' : ' AND i.garden="'.$garden.'"');
        $categoryQuery = ($category == 'any' ? '' : ' AND i.category="'.$category.'"');
        $greenCitiesQuery = ($greenCities == 'true' ? ' AND i.city_found = 1' : '');
        $tagOutQuery = ($tagOut == 'true' ? ' AND i.flagged = 0' : '');
        $query = $cityQuery.$communeQuery.$regionQuery.$typeQuery.$propertyQuery.$garageQuery.$terraceQuery.$garageQuery.$gardenQuery.$categoryQuery.$greenCitiesQuery.$tagOutQuery;


        $priceRange = explode(',', $priceRange);
        $priceQuery = ' AND p.price >= '.$priceRange[0].' AND p.price <= '.$priceRange[1];

        $percRange = explode(',', $percRange);

        if($table == 'manual'){
            $sql = 'SELECT i.*, (SELECT p.price FROM `price_history` p WHERE `info_id` = i.id '.$priceQuery.' ORDER BY `id` LIMIT 1) AS price, (SELECT DATE_FORMAT(`date_created`, "%d/%m/%Y") FROM `price_history` WHERE `info_id` = i.id  ORDER BY `id` LIMIT 1) AS date_created 
                FROM `info` i
                WHERE i.is_active = 1 ' . $query. ' 
                ORDER BY '.$sort.' ' . $y;
        }else{
            $sql = 'SELECT i.*,p.price, DATE_FORMAT(p.date_created, "%d/%m/%Y") AS date_created
                FROM `info` i, `price_history` p
                WHERE i.is_active = 1 AND p.info_id = i.id ' . $query. ' 
                '.$priceQuery.'
                ORDER BY '.$sort.' ' . $y;
        }




        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // get benchmark
            $id = $row['id'];
            $city = $row['city'];
            $category = $row['category'];
            $garage = $row['garage'];
            $row['average'] = $this->getBenchmarkAverage($city, $category, $garage);

            if($row['price'] != null){
                $pricePerSqM = ($row['size'] != 0 ? ($row['price'] / $row['size']) : 0);
                $psb = ($row['average']['averageSize'] != 0 ? ($row['average']['averagePrice'] / $row['average']['averageSize']) : 0);
                // calculate price decrease percentage
                $decrease = ($psb - $pricePerSqM ) / ($psb) * 100;
                $descPerc = -$decrease;
                if($descPerc >= $percRange[0] && $descPerc <= $percRange[1]){
                    $return[] = $row;
                }
            }
        }

        $pdo = null;

        return $return;
    }

    public function getBenchmarkAverage($city, $category, $garage){
        $pdo = $this->getPdo();
        $sql = 'SELECT i.*, (SELECT `price` FROM `price_history`WHERE `info_id` = i.id ORDER BY `id` LIMIT 1) AS price
                FROM `info` i
                WHERE i.is_active = 1
                AND i.city = "'.$city.'"
                AND i.category = "'.$category.'"
                AND i.garage = "'.$garage.'"
                ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $prices = array();
        $sizes = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $prices[] = $row['price'];
            $sizes[] = $row['size'];
        }
        $pdo = null;
        return array(
            'averagePrice' => array_sum($prices)/count($prices),
            'averageSize' => array_sum($sizes)/count($sizes)
        );
    }

    public function addComment($data){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `info` SET `comment` = "'.trim($data['comment']).'" WHERE `id` = '.$data['id'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }
    public function flagInfo($data){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `info` SET `flagged` = '.$data['flag'].' WHERE `id` = '.$data['id'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }
    public function checkStatus(){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `options`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;

        return $result;
    }

    public function reset($totalPage){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `active` = 1, `total_page` = '.$totalPage.', `current_page` = 1, `total_listing` = 0, `total_scrape_count` = 0';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function setTotalPage($totalPage, $totalListing){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `total_page` = '.$totalPage.', `total_listing` = '.$totalListing;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function recordData($data){
        $date = date('Y-m-d');
        $pdo = $this->getPdo();
        foreach($data as $row){
            $infoId = $this->getInfoById($row['refId']);
            if($row['city'] == 'Luxembourg'){
                $city = explode('|',$row['citySlug']);
                $city = trim($city[0]);
                $city = 'Luxembourg-'.$city;
                $cityFound = true;
            }else{
                $city = $row['city'];
                $cityFound = false;
            }
            if($infoId['id'] > 0){
                if(date('N', strtotime($date)) == 7){
                    $newQry = ' AND `is_new` = 0';
                }else{
                    $newQry = '';
                }
                $sql = 'UPDATE `info` SET `is_active` = 1, `city` = "'.$city.'", `category` = "'.$row['category'].'", `city_found` = '.$cityFound.' '.$newQry.' WHERE `id` = '.$infoId['id'];
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $infoId = $infoId['id'];
            }else{
                $commune = $this->getCommune($row['city']);
                if($commune){
                    $commune = $commune['commune'];
                }else{
                    $commune = $row['city'];
                }
                // New Item
                $sql = 'INSERT INTO `info` SET `ref_id` = '.$row['refId'].', 
                        `title` = "'.addslashes($row['title']).'",
                        `city` ="'.$city.'", 
                        `commune` ="'.ucfirst($commune).'", 
                        `region` ="'.$row['region'].'", 
                        `type` ="'.$row['type'].'", 
                        `category` = "'.$row['category'].'",
                        `is_new_property` = "'.$row['isNewProperty'].'", 
                        `size` = '.$row['size'].',
                        `no_of_bedrooms` = '.$row['numberOfBedrooms'].',
                        `no_of_bathrooms` = '.$row['bathroom'].',
                        `year_of_construction` = "'.$row['yearOfConstruction'].'",
                        `garage` = "'.$row['garage'].'",
                        `terrace` = "'.$row['terrace'].'",
                        `garden` = "'.$row['garden'].'",
                        `url` = "'.$row['url'].'",
                        `is_active` = 1,
                        `city_found` = '.$cityFound.',
                        `comment` = "",
                        `is_new` = 1
                        ';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $infoId = $pdo->lastInsertId();
            }
            $this->recordAddedListing();
            $this->insertPriceHistory($infoId, $row['price']);
        }
        $pdo = null;
    }

    public function recordAddedListing(){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `total_scrape_count` = (`total_scrape_count` + 1)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function getInfoById($refId){
        $pdo = $this->getPdo();
        $sql = 'SELECT `id` FROM `info` WHERE `ref_id` = "'.$refId.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;

        return $return;
    }

    public function insertPriceHistory($id, $price){
        $date = date('Y-m-d');
        if(date('N', strtotime($date)) == 7){
            $isScheduled = true;
        }else{
            $isScheduled = false;
        }
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `price_history` SET `info_id` = '.$id.', `price` = '.$price.', `date_created` = "'.$date.'", `is_scheduled` = '.$isScheduled;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;

        return true;
    }

    public function getPriceHistory($id){
        $pdo = $this->getPdo();
        $sql = 'SELECT `date_created` as date, `price` FROM `price_history` WHERE `info_id` = "'.$id.'" ORDER BY `date_created` ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $return[] = $row;
        }
        $pdo = null;
        return $return;
    }

    public function setNextPage($nextPage){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `current_page` = '.$nextPage;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'SELECT `current_page`, `total_page` FROM `options` WHERE `id` = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $opt = $stmt->fetch(PDO::FETCH_ASSOC);
        $current = $opt['current_page'];
        $total = $opt['total_page'];

        if($total != 0){
            if($current > $total){
                $pdo = $this->getPdo();
                $sql = 'UPDATE `options` SET `active` = 0';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
        }
        $pdo = null;
        return true;
    }

    public function getNextPage(){
        $pdo = $this->getPdo();
        $sql = 'SELECT `current_page` FROM `options` WHERE `id` = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC)['current_page'];
        $pdo = null;
        return $return;
    }

    public function isActive(){
        $pdo = $this->getPdo();
        $sql = 'SELECT `active` FROM `options` WHERE `id` = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
        $pdo = null;
        return $return;
    }

    public function activateScan(){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `info` SET `is_active` = 0';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $sql = 'UPDATE `options` SET `active` = 1, `total_page` = 0, `current_page` = 1, `total_listing` = 0, `total_scrape_count` = 0';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;

        return true;
    }

    public function getCommune($city){
        $pdo = $this->getPdo();
        $sql = 'SELECT `commune` FROM `commune_list` WHERE `city` = "'.$city.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo = null;
        return $return;
    }

    public function updateInfoCommune($id, $commune){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `info` SET `commune` = "'.$commune.'" WHERE `id` = '.$id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function getAllInfo(){
        $pdo = $this->getPdo();
        $sql = 'SELECT * FROM `info`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $content[] = $row;
        }
        $pdo = null;
        return $content;
    }

    public function getLastSchedule(){
        $pdo = $this->getPdo();
        $sql = 'SELECT `date_created` FROM `price_history` WHERE `is_scheduled` = 1 ORDER BY `date_created` LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $content = date('d/m/Y', strtotime($stmt->fetch(PDO::FETCH_ASSOC)['date_created']));
        $pdo = null;
        return $content;
    }

    public function curlTo($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 85969a77-227f-4da2-ab22-81feaa26c0c4"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array('html' => $err);
        } else {
            return array('html' => $response);
        }
    }

    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
}