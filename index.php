<?php

// Global settings
define('APP_FTP_HOST', "ftp.clickfuel.com");
define('APP_FTP_USER', "ftp_integration_test");
define('APP_FTP_PASS', "6k0Sb#EXT6jw");

define('APP_ADVERTISERS_CSV', 'Yashi_Advertisers.csv');

// Include dependencies
include('ftp_client.php');
include('advertiser.php');

// Establish ftp connection
$client = new FTPClient();
$login  = $client->connect(APP_FTP_HOST, APP_FTP_USER, APP_FTP_PASS);

if ($login) {
    $advertiser = Advertiser::from_csv( $client->get(APP_ADVERTISERS_CSV) );
    print_r( $advertiser );
}
?>