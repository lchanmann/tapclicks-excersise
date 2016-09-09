<?php

// Global settings
define('FTP_HOST', "ftp.clickfuel.com");
define('FTP_USER', "ftp_integration_test");
define('FTP_PASS', "6k0Sb#EXT6jw");

// FTPClient
include('ftp_client.php');

// Establish ftp connection
$client = new FTPClient();
$login  = $client->connect(FTP_HOST, FTP_USER, FTP_PASS);

if ($login) {
    print_r( $client->ls() );
}

// Close ftp connection
$client->close();
?>