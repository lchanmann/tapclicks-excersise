<?php

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
    private $dataFields = array(100 => 'Date', 'Impressions', 'Clicks', '25% Viewed', '50% Viewed', '75% Viewed', '100% Viewed');
    public function import($filename) {
        // FIXME: duplicate with Advertiser::from_csv
        $file = fopen($filename, "r");
        if ($file) {
            $fields = array_flip(fgetcsv($file));
            while ( $data = fgetcsv($file) ) {
                $advertiser_id = $data[ $fields['Advertiser ID'] ];
                if (in_array($advertiser_id, $this->advertiser_ids)) {
                    $this->add_campaign($data, $fields);
                }
            }
            fclose($file);
        }
    }
    
    // Add campaign
    private function add_campaign($data, $fields) {
         $campaign = $this->create_campaign($data, $fields);
         foreach ($this->campaigns as &$cgn) {
             if ($cgn['Campaign ID'] == $campaign['Campaign ID']) {
                 $this->update_stats($cgn, $campaign);
                 return;
             }
         }
         $this->campaigns[] = $campaign;
    }
    
    // Create campaign
    private function create_campaign($data, $fields) {
        $campaignFields = array('Campaign ID', 'Campaign Name', 'Advertiser ID', 'Advertiser Name') + $this->dataFields;
        $campaign = array();
        foreach ($campaignFields as $name) {
            $campaign[$name] = $data[ $fields[$name] ];
        }
        return $campaign;
    }
    
    // Update statistics data
    private function update_stats(&$target, $data) {
        foreach($this->dataFields as $field) {
            $target[$field] += $data[$field];
        }
    }
}
?>