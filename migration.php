<?php

include('sql_helpers.php');

/**
* Migration class
*/
class Migration
{
    private $mysqli;
    
    // Constructor
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    /**
    * Start migration
    * start($data)
    */
    public function start($data) {
        $this->insert_campaigns($data->campaigns);
    }
    
    // insert campaigns
    private function insert_campaigns($campaigns) {
        if (count($campaigns) > 0) {
            $sql = "INSERT INTO `zz__yashi_cgn` (`campaign_id`, `yashi_campaign_id`, `name`, `yashi_advertiser_id`, `advertiser_name`) VALUES ";
            foreach ($campaigns as $cgn) {
                $sql .= "\n({$cgn['Campaign ID']}, {$cgn['Campaign ID']}, '" . sanitize($cgn['Campaign Name']) ."', {$cgn['Advertiser ID']}, '{$cgn['Advertiser Name']}'),";
            }
            $sql[strlen($sql) - 1] = ";";
            $this->mysqli->query($sql);
        }
    }
}
?>