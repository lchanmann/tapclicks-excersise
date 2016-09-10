<?php

include('array_helpers.php');

/**
* Data class
*/
class Data
{
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
    private $dateField  = array(100 => 'Date');
    private $dataFields = array(101 => 'Impressions', 'Clicks', '25% Viewed', '50% Viewed', '75% Viewed', '100% Viewed');
    public function import($filename) {
        $file = fopen($filename, "r");
        if ($file) {
            $fieldIndices = array_flip(fgetcsv($file));
            while ( $row = fgetcsv($file) ) {
                $advertiser_id = $row[ $fieldIndices['Advertiser ID'] ];
                if (in_array($advertiser_id, $this->advertiser_ids)) {
                    $this->add_campaign($row, $fieldIndices);
                    $this->add_order($row, $fieldIndices);
                    $this->add_creative($row, $fieldIndices);
                }
            }
            fclose($file);
        }
    }
    
    // Add campaign
    private function add_campaign($row, $fieldIndices) {
        $campaign = $this->create_data(array('Campaign ID', 'Campaign Name', 'Advertiser ID', 'Advertiser Name'), $row, $fieldIndices);
        foreach ($this->campaigns as &$cgn) {
            if (array_have_same(array('Campaign ID'), $cgn, $campaign)) {
                $this->update_stats($cgn, $campaign);
                return;
            }
        }
        $this->campaigns[] = $campaign;
    }
    
    // Add order
    private function add_order($row, $fieldIndices) {
         $order = $this->create_data(array('Order ID', 'Campaign ID', 'Order Name'), $row, $fieldIndices);
         foreach ($this->orders as &$ord) {
             if (array_have_same(array('Campaign ID', 'Order ID'), $ord, $order)) {
                 $this->update_stats($ord, $order);
                 return;
             }
         }
         $this->orders[] = $order;
    }
    
    // Add creative
    private function add_creative($row, $fieldIndices) {
         $creative = $this->create_data(array('Creative ID', 'Order ID', 'Creative Name', 'Creative Preview URL'), $row, $fieldIndices);
         foreach ($this->creatives as &$ctv) {
             if (array_have_same(array('Order ID', 'Creative ID'), $ctv, $creative)) {
                 $this->update_stats($ctv, $creative);
                 return;
             }
         }
         $this->creatives[] = $creative;
    }
    
    // Create data
    private function create_data($fields, $row, $fieldIndices) {
        $fields += $this->dateField + $this->dataFields;
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $row[ $fieldIndices[$field] ];
        }
        return $data;
    }
    
    // Update statistics data
    private function update_stats(&$target, $data) {
        foreach($this->dataFields as $field) {
            $target[$field] += $data[$field];
        }
    }
}
?>