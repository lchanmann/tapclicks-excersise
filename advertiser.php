<?php

/**
* Advertiser class
*/
class Advertiser
{
    /**
    * Advertiser ID
    */
    public $id;
    /**
    * Advertiser Name
    */
    public $name;
    
    // constructor
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    /**
    * Load advertiser record from csv file
    * ::from_csv($filename)
    * @return array(advertiser)
    */
    public static function from_csv($filename) {
        $records = array();
        $file = fopen($filename, "r");
        if ($file) {
            // skip csv header line
            fgetcsv($file);
            while ( $data = fgetcsv($file) ) {
                // TODO: refactor hard-coded column indexes
                $records[] = new Advertiser($data[0], $data[1]);
            }
            fclose($file);
        }
        return $records;
    }
}

?>