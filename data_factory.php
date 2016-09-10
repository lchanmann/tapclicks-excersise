<?php
/**
* Data factory class
*/
class DataFactory
{
    // field indices mapping
    private $fieldMapping;
    
    // Constructor
    public function __construct($fieldMapping) {
        $this->fieldMapping = $fieldMapping;
    }
    
    /**
    * Create row data
    * create($fields, $source)
    */
    public function create($fields, $row) {
        $data = $this->create_data($fields, $row);
        $data['Logs'] = array($this->create_data(Data::$logFields, $row));
        return $data;
    }
    
    // Create data as associative array
    private function create_data($fields, $row) {
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $row[ $this->fieldMapping[$field] ];
        }
        return $data;
    }
}
?>