<?php

// Global settings
define('APP_FTP_HOST', "ftp.clickfuel.com");
define('APP_FTP_USER', "ftp_integration_test");
define('APP_FTP_PASS', "6k0Sb#EXT6jw");

define('APP_ADVERTISERS_CSV', 'Yashi_Advertisers.csv');
define('APP_DATAFILES_PATTERN', '/^Yashi_2016-05/');

// Include dependencies
include('ftp_client.php');
include('advertiser.php');
include('data.php');

/**
* Run the application
*/
$app = new Application( new FTPClient() );
$app->run();

/**
* Application class
*/
class Application
{
    // FTP client
    private $ftpClient;
    
    // Constructor
    public function __construct($ftpClient) {
        $this->ftpClient = $ftpClient;
    }
    
    /**
    * Run the application
    */
    public function run() {
        $this->connect(APP_FTP_HOST, APP_FTP_USER, APP_FTP_PASS);
        $advertiser_ids = array_map(function($obj) {
            return $obj->id;
        }, $this->get_advertisers(APP_ADVERTISERS_CSV));
        $this->import_csv(APP_DATAFILES_PATTERN, $advertiser_ids);
    }
    
    // connect to ftp server
    private function connect($server, $user, $password) {
        $success = $this->ftpClient->connect($server, $user, $password);
        if (!$success) {
            fwrite(STDERR, "Could not connect to the ftp server.\n");
            exit(1);
        }
    }
    
    // get advertisers
    private function get_advertisers($filename) {
        $advertisers_csv = $this->ftpClient->get($filename);
        return Advertiser::from_csv( $advertisers_csv );
    }
    
    // import data from csv
    private function import_csv($pattern, $advertiser_ids) {
        $dataFiles = $this->ftpClient->grep($pattern);
        $data = new Data($advertiser_ids);
        foreach ($dataFiles as $file) {
            $csv = $this->ftpClient->get($file);
            $data->import($csv);
        }
    }
}
?>