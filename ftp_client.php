<?php

/**
* FTPClient class
*/
class FTPClient
{
    private $connection;
        
    // constructor
    public function __construct() { }
    
    // destructor
    public function __destruct() {
        $this->close();
    } 
    
    /**
    * Establish connection to FTP server
    *
    * connect($server, $user, $password)
    * @return true if login successfully and false otherwise.
    */  
    public function connect($server, $user, $password)
    {
        $this->connection = ftp_connect($server);
        $login = ftp_login($this->connection, $user, $password);
        return $login;
    }
    
    /**
    * Close FTP connection
    *
    * close()
    */
    public function close() {
        if ($connection) {
            ftp_close($connection);
        }
    }
    
    /**
    * Directory listing
    *
    * ls($directory = '.')
    * @return $content:array of the directory
    */
    public function ls($directory = '.') {
        $content = ftp_nlist($this->connection, $directory);
        return $content;
    }
    
    /**
    * Find files with name that matches $pattern
    * grep($pattern)
    * @return matched files
    */
    public function grep($pattern='/*/') {
        $matched = array();
        $files = $this->ls();
        foreach ($files as $file) {
            if (preg_match($pattern, $file)) {
                $matched[] = $file;
            }
        }
        return $matched;
    }
    
    /**
    * Get file from server
    *
    * get($filename)
    * @return path to downloaded file
    */
    public function get($filename) {
        $localFile = "downloads/{$filename}";
        $success = ftp_get($this->connection, $localFile, $filename, FTP_ASCII);
        return $success ? $localFile : null;
    }
}

?>