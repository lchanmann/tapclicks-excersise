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
        $this->insert_orders($data->orders);
        // $this->insert_creatives($data->creatives);
    }
    
    // insert campaigns
    private function insert_campaigns($campaigns) {
        $stmtRegular = $this->build_stmt("zz__yashi_cgn", array("campaign_id", "yashi_campaign_id", "name", "yashi_advertiser_id", "advertiser_name"));
        $stmtRegular->bind_param("iisis", $campaignId, $campaignId, $name, $advertiserId, $advertiserName);
        
        $stmtData = $this->build_data_stmt("zz__yashi_cgn_data", "campaign_id");
        foreach ($campaigns as $cgn) {
            $campaignId = $cgn['Campaign ID'];
            $name = $cgn['Campaign Name'];
            $advertiserId = $cgn['Advertiser ID'];
            $advertiserName = $cgn['Advertiser Name'];
            $stmtRegular->execute();
            $this->insert_logs($cgn['Logs'], $campaignId, $stmtData);
        }
    }
    
    // insert orders
    private function insert_orders($orders) {
        $stmtRegular = $this->build_stmt("zz__yashi_order", array("order_id", "campaign_id", "yashi_order_id", "name"));
        $stmtRegular->bind_param("iiis", $orderId, $campaignId, $orderId, $name);
        
        $stmtData = $this->build_data_stmt("zz__yashi_order_data", "order_id");
        foreach ($orders as $ord) {
            $orderId = $ord['Order ID'];
            $campaignId = $ord['Campaign ID'];
            $name = $ord['Order Name'];
            $stmtRegular->execute();
            $this->insert_logs($ord['Logs'], $orderId, $stmtData);
        }
    }
    
    // insert log data
    private $log;
    private function insert_logs($logs, $foreignKey, $stmt) {
        $this->log['FK'] = $foreignKey;
        foreach ($logs as $tmpLog) {
            $tmpLog['Date'] = strtotime($tmpLog['Date']);
            foreach ($tmpLog as $k => $v) {
                $this->log[$k] = $v;
            }
            $stmt->execute();
        }
    }
    
    // build mysqli stmt for insert statement
    private function build_stmt($table, $columns) {
        $tableName = backticks_wrap($table);
        $columnList = join(", ", array_map("backticks_wrap", $columns));
        $valueList = trim(str_repeat("?, ", count($columns)), ", ");
        $sql = "INSERT INTO {$tableName} ({$columnList}) VALUES ({$valueList});";
        return $this->mysqli->prepare($sql);
    }
    
    // build stmt for data table
    private function build_data_stmt($table, $foreignKey) {
        $stmt = $this->build_stmt($table, array($foreignKey, "log_date", "impression_count", "click_count", "25viewed_count", "50viewed_count", "75viewed_count", "100viewed_count"));
        $stmt->bind_param("iiiiiiii",
            $this->log['FK'],
            $this->log['Date'],
            $this->log['Impressions'],
            $this->log['Clicks'],
            $this->log['25% Viewed'],
            $this->log['50% Viewed'],
            $this->log['75% Viewed'],
            $this->log['100% Viewed']);
        return $stmt;
    }
}
?>