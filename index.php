<?php

// Global settings
// TODO: use ENV instead of hard-corded values for ftp server configs
define('APP_FTP_HOST', "ftp.clickfuel.com");
define('APP_FTP_USER', "ftp_integration_test");
define('APP_FTP_PASS', "6k0Sb#EXT6jw");

define('APP_ADVERTISERS_CSV', 'Yashi_Advertisers.csv');
define('APP_DATAFILES_PATTERN', '/^Yashi_2016-05/');

define('APP_MYSQL_HOST', $_ENV['APP_MYSQL_HOST']);
define('APP_MYSQL_USER', $_ENV['APP_MYSQL_USER']);
define('APP_MYSQL_PASS', $_ENV['APP_MYSQL_PASS']);
define('APP_MYSQL_DB', $_ENV['APP_MYSQL_DB']);

// Include dependencies
include('ftp_client.php');
include('advertiser.php');
include('data.php');
include('migration.php');

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
    // Aggregated data
    private $data;
    
    
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
        $this->migrate_data();
        echo("\n\nDone!!!");
    }
    
    // connect to ftp server
    private function connect($server, $user, $password) {
        echo("\nConnecting to FTP server ... ");
        $success = $this->ftpClient->connect($server, $user, $password);
        if (!$success) {
            fwrite(STDERR, "Error: Unable to connect to FTP server.\n");
            exit(1);
        }
        echo("success.");
    }
    
    // get advertisers
    private function get_advertisers($filename) {
        echo("\nGet advertisers information: ");
        $advertisers_csv = $this->ftpClient->get($filename);
        return Advertiser::from_csv( $advertisers_csv );
    }
    
    // import data from csv
    private function import_csv($pattern, $advertiser_ids) {
        echo("\nImport data from CSV files: ");
        $dataFiles = $this->ftpClient->grep($pattern);
        $this->data = new Data($advertiser_ids);
        foreach ($dataFiles as $file) {
            $csv = $this->ftpClient->get($file);
            $this->data->import($csv);
        }
    }
    
    // migrate data to database
    private function migrate_data() {
        echo("\nStart migrating data: ");
        $mysqli = mysqli_connect(APP_MYSQL_HOST, APP_MYSQL_USER, APP_MYSQL_PASS, APP_MYSQL_DB);
        if (!$mysqli) {
            fwrite(STDERR, "Error: Unable to connect to the MySQL server.\n");
            exit(1);
        }
        
        $migration = new Migration($mysqli);
        $migration->start($this->data);
    }
}
?>