<?php

// Inkluderar databasklassen
include_once 'Database.php';

// Klass som hanterar användare
class User {

    // Properties
    public $username;
    private $password;
    private $first_name;
    private $last_name;
    private $email;
    public $login;
    public $logout;
    public $error;
    public $conn;

    // Metoder
    // Konstruerare
    public function __construct() {

        $database = new Database();
        $this->conn = $database->conn;

        if ( $database->error ) {
            $this->error = $database->error;
        }
    }

    // Inloggning
    public function login( $username, $password ): bool {

        $query = $this->conn->prepare( 'SELECT FROM user_portfolio_2 WHERE username = ? AND `password` = ?' );
        $query->bind_param( 'ss', $username, $password );
        $result = $this->conn->query( $query );

        if ( $result ) {
            return true;

        } else {
            $this->error = 'Fel användarnamn eller lösenord.';
            return false;
        }
    }

    // Setters
    // Användarnamn
    public function setUsername($username) {
        $this->username = $username;
    }
}