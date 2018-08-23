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
    public function reset($totalPage){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `active` = 1, `total_page` = '.$totalPage.', `current_page` = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;
        return true;
    }

    public function recordData($data){
        $pdo = $this->getPdo();
        foreach($data as $row){
            $infoId = $this->getInfoById($row['refId']);
            if($infoId == false){
                // New Item
                $sql = 'INSERT INTO `info` SET `ref_id` = '.$row['refId'].', 
                        `title` = "'.addslashes($row['title']).'",
                        `city` ="'.$row['city'].'", 
                        `commune` ="'.$row['city'].'", 
                        `region` ="'.$row['region'].'", 
                        `type` ="'.$row['type'].'", 
                        `is_new_property` = "'.$row['isNewProperty'].'", 
                        `size` = '.$row['size'].',
                        `no_of_bedrooms` = '.$row['numberOfBedrooms'].',
                        `no_of_bathrooms` = '.$row['bathroom'].',
                        `year_of_construction` = '.$row['yearOfConstruction'].',
                        `garage` = "'.$row['garage'].'",
                        `terrace` = "'.$row['terrace'].'",
                        `garden` = "'.$row['garden'].'",
                        `url` = "'.$row['url'].'"
                        ';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $infoId = $pdo->lastInsertId();
            }else{
                $infoId = $infoId['id'];
            }
            $this->insertPriceHistory($infoId, $row['price']);
        }
        $pdo = null;
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
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `price_history` SET `info_id` = '.$id.', `price` = '.$price.', `date_created` = "'.$date.'"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pdo = null;

        return true;
    }

    public function setNextPage($nextPage){
        $pdo = $this->getPdo();
        $sql = 'UPDATE `options` SET `current_page` = '.$nextPage;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'SELECT `current_page`, `total_page` FROM `options` WHERE `id` = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $current = $stmt->fetch(PDO::FETCH_ASSOC)['current_page'];
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total_page'];

        if($current == $total){
            $pdo = $this->getPdo();
            $sql = 'UPDATE `options` SET `active` = 0';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
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