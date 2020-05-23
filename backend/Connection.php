<?php

class Connection
{
    private $server = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'prescription_books';

    public $conn;

    public function __construct() {
        if(!$this->conn || $this->conn->connect_error) {
            $this->conn = new mysqli($this->server, $this->username, $this->password, $this->database);
        }
        $this->conn->set_charset('utf8');
    }

}