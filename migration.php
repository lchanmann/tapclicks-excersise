<?php

// User UTC timezone
date_default_timezone_set("UTC");

include('sql_helpers.php');

/**
* Migration class
*/
class Migration
{
    private $mysqli;
    
    // constructor
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    // destructor
    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
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
        $stmt_regular = $this->build_insert_stmt("zz__yashi_cgn", array("campaign_id", "yashi_campaign_id", "name", "yashi_advertiser_id", "advertiser_name"));
        $stmt_regular->bind_param("iisis", $campaignId, $campaignId, $name, $advertiserId, $advertiserName);
        
        $stmt_data = $this->build_insert_stmt("zz__yashi_cgn_data", array("campaign_id", "log_date", "impression_count", "click_count", "25viewed_count", "50viewed_count", "75viewed_count", "100viewed_count"));
        $stmt_data->bind_param("iiiiiiii", $campaignId, $logDate, $impressionCount, $clickCount, $viewedCount25, $viewedCount50, $viewedCount75, $viewedCount100);
        
        foreach ($campaigns as $cgn) {
            $campaignId = $cgn['Campaign ID'];
            $name = $cgn['Campaign Name'];
            $advertiserId = $cgn['Advertiser ID'];
            $advertiserName = $cgn['Advertiser Name'];
            $stmt_regular->execute();
            foreach ($cgn['Logs'] as $log) {
                $logDate = strtotime($log['Date']);
                $impressionCount = $log['Impressions'];
                $clickCount = $log['Clicks'];
                $viewedCount25 = $log['25% Viewed'];
                $viewedCount50 = $log['50% Viewed'];
                $viewedCount75 = $log['75% Viewed'];
                $viewedCount100 = $log['100% Viewed'];
                $stmt_data->execute();
            }
        }
    }
    
    // build mysqli stmt for insert statement
    private function build_insert_stmt($table, $columns) {
        $tableName = backticks_wrap($table);
        $columnList = join(", ", array_map("backticks_wrap", $columns));
        $valueList = trim(str_repeat("?, ", count($columns)), ", ");
        $sql = "INSERT INTO {$tableName} ({$columnList}) VALUES ({$valueList});";
        return $this->mysqli->prepare($sql);
    }
}
?>