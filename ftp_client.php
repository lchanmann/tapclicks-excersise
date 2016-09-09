<?php

class FTPClient
{
    private $connection;
        
    // constructor
    public function __construct() { }
    
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
}

?>