<?php

// Klass som hanterar databasanslutningen
class Database {

    // Properties
    private $db_host = 'studentmysql.miun.se';
    private $db_username = 'mazi2001';
    private $db_password = 'vXsUGyt2cm';
    private $db_name = 'mazi2001';
    public $conn;
    public $error;

    // Metoder
    // Konstruerare
    public function __construct() {

        $this->conn = new mysqli($this->db_host, $this->db_username, $this->db_password, $this->db_name);

        if ($this->conn->connect_error) {
            $this->error = 'Kunde inte ansluta till databasen: ' . $this->conn->connect_error;
        }
    }
}
?>