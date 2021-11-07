<?php
// Inkluderar databasklassen
include_once 'Database.php';
include_once 'User.php';

// Klass som hanterar jobb
class Experience {

    // Properties
    public $id;
    public $employment;
    public $employer;
    public $start_date;
    public $end_date;
    public $updated;
    public $updated_by;
    public $jobArr = [];
    public $job = [];
    public $error;
    public $confirm;
    public $conn;

    // Metoder 
    // Konstruerare
    public function __construct() {

        $database = new Database();
        $this->conn = $database->conn;

        if($database->error) {
            $this->error = $database->error;
        }

        $query = 'SELECT * FROM experience_portfolio_2';
        $result = $this->conn->query($query);

        if($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                array_push($this->jobArr, $row);
            }
        
        } else {
            $this->error = 'Inga jobb hittades.';
        }
    }

    // Lägger till jobb
    public function addJob(): bool {

        $user = new User();
        $this->updated_by = $user->username;
        $query = '';

        if ($this->end_date) {

            $query = $this->conn->prepare('INSERT INTO experience_portfolio_2
                (job, employer, job_start_date, job_end_date, updated_by)
                VALUES (?, ?, ?, ?, ?)');
            $query->bind_param('sssss', $this->employment, $this->employer, $this->start_date,
                $this->end_date, $this->updated_by);
            
        } else {

            $query = $this->conn->prepare('INSERT INTO experience_portfolio_2
                (job, employer, job_start_date, updated_by) VALUES (?, ?, ?, ?)');
            $query->bind_param('ssss', $this->employment, $this->employer, $this->start_date,
                $this->updated_by);
        }

        $query->execute();

        if (!$this->conn->connect_error) {
            
            $this->confirm = 'Jobbet har lagts till.';
            return true;
        
        } else {

            $this->error = 'Det gick inte att lägga till jobbet: ' . 
                $this->conn->connect_error;
            return false; 
        }
    }
}