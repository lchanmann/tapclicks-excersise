<?php

include('array_helpers.php');
include('data_factory.php');

/**
* Data class
*/
class Data
{
    // Data log fields
    public static $logFields = array('Date', 'Impressions', 'Clicks', '25% Viewed', '50% Viewed', '75% Viewed', '100% Viewed');
    
    // Advertiser ids
    private $advertiser_ids;
    
    /**
    * Campaign level data
    */
    public $campaigns;
    
    /**
    * Order level data
    */
    public $orders;
    
    /**
    * Creative level data
    */
    public $creatives;
    
    // Constructor
    public function __construct($advertiser_ids) {
        $this->advertiser_ids = $advertiser_ids;
        $this->campaigns = array();
        $this->orders = array();
        $this->creatives = array();
    }
    
    /**
    * Import data from csv
    * import($filename)
    */
    public function import($filename) {
        $file = fopen($filename, "r");
        if ($file) {
            $fieldIndices = array_flip(fgetcsv($file));
            $dataFactory = new DataFactory($fieldIndices);
            while ( $row = fgetcsv($file) ) {
                $advertiser_id = $row[ $fieldIndices['Advertiser ID'] ];
                if (in_array($advertiser_id, $this->advertiser_ids)) {
                    $campaign = $dataFactory->create(array('Campaign ID', 'Campaign Name', 'Advertiser ID', 'Advertiser Name'), $row);
                    $this->add_campaign($campaign);
                    
                    $order = $dataFactory->create(array('Order ID', 'Campaign ID', 'Order Name'), $row);
                    $this->add_order($order);

                    $creative = $dataFactory->create(array('Creative ID', 'Order ID', 'Creative Name', 'Creative Preview URL'), $row);
                    $this->add_creative($creative);
                }
            }
            fclose($file);
        }
    }
    
    // Add campaign
    private function add_campaign($campaign) {
        foreach ($this->campaigns as &$cgn) {
            if (array_have_same(array('Campaign ID'), $cgn, $campaign)) {
                $this->add_or_update_logs($cgn['Logs'], $campaign['Logs'][0]);
                return;
            }
        }
        $this->campaigns[] = $campaign;
    }
    
    // Add order
    private function add_order($order) {
         foreach ($this->orders as &$ord) {
             if (array_have_same(array('Campaign ID', 'Order ID'), $ord, $order)) {
                 $this->add_or_update_logs($ord['Logs'], $order['Logs'][0]);
                 return;
             }
         }
         $this->orders[] = $order;
    }
    
    // Add creative
    private function add_creative($creative) {
         foreach ($this->creatives as &$ctv) {
             if (array_have_same(array('Order ID', 'Creative ID'), $ctv, $creative)) {
                 $this->add_or_update_logs($ctv['Logs'], $creative['Logs'][0]);
                 return;
             }
         }
         $this->creatives[] = $creative;
    }
    
    // Add or update log data
    private function add_or_update_logs(&$target, $data) {
        $logFields = array_slice(self::$logFields, 1);
        foreach ($target as &$log) {
            if (array_have_same(array('Date'), $data, $log)) {
                foreach($logFields as $field) {
                    $log[$field] += $data[$field];
                }
                return;
            }
        }
        $target[] = $data;
    }
}
?>