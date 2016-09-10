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
        $data = array();
        $file = fopen($filename, "r");
        if ($file) {
            $fieldIndices = array_flip(fgetcsv($file));
            while ( $row = fgetcsv($file) ) {
                $data[] = new Advertiser($row[ $fieldIndices['Advertiser ID'] ], $row[ $fieldIndices['Advertiser Name'] ]);
            }
            fclose($file);
        }
        return $data;        
    }
}

?>