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

        if ($database->error) {
            $this->error = $database->error;
        }
    }

    // Inloggning
    public function login($username, $password): bool {
        $query = $this->conn->prepare('SELECT FROM user_portfolio_2 WHERE username = ? AND `password` = ?');
        $query->bind_param('ss', $username, $password);
        $result = $this->conn->query($query);

        if (!$user = $result->fetch_assoc()) {
            $this->error = 'Fel användarnamn eller lösenord.';
            return false;
        } else {
            return true;
        }
    }

    // Utloggning
    public function logout($logout): bool {
        if ($logout) {
            $_SESSION = [];
            session_destroy();
            header('Location: https://studenter.miun.se/~mazi2001/writeable/dt173g/projekt/webbplats/login.php');
            return true;     
        } else {
            return false;
        }
    }
}