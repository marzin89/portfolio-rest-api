<?php
// Inkluderar databasklassen
include_once 'Database.php';

// Klass som hanterar jobb
class Experience {

    // Properties
    public $id;
    public $employment;
    public $employer;
    public $start_date;
    public $end_date;
    public $updated;
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

        $query = '';

        if ($this->end_date) {

            $query = $this->conn->prepare('INSERT INTO experience_portfolio_2
                (job, employer, job_start_date, job_end_date)
                VALUES (?, ?, ?, ?)');
            $query->bind_param('ssss', $this->employment, $this->employer, $this->start_date,
                $this->end_date);
            
        } else {

            $query = $this->conn->prepare('INSERT INTO experience_portfolio_2
                (job, employer, job_start_date) VALUES (?, ?, ?)');
            $query->bind_param('sss', $this->employment, $this->employer, $this->start_date);
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

    // Uppdaterar jobb
    public function updateJob(): bool {

        $query = '';
        $this->updated = date('Y-m-d H:i:s');

        if ($this->end_date) {

            $query = $this->conn->prepare('UPDATE experience_portfolio_2 SET job = ?,
                employer = ?, job_start_date = ?, job_end_date = ?, job_updated = ?
                WHERE job_id = ?');
            $query->bind_param('sssssi', $this->employment, $this->employer, $this->start_date, 
                $this->end_date, $this->updated, $this->id);
        
        } else {

            $query = $this->conn->prepare('UPDATE experience_portfolio_2 SET job = ?,
                employer = ?, job_start_date = ?, job_updated = ? WHERE job_id = ?');
            $query->bind_param('ssssi', $this->employment, $this->employer, $this->start_date, 
                $this->updated, $this->id);
        }

        $query->execute();

        if (!$this->conn->connect_error) {

            $this->confirm = 'Jobbet har uppdaterats.';
            return true;
        
        } else {

            $this->error = 'Det gick inte att uppdatera jobbet: ' .
                $this->conn->connect_error;
            return false;
        }
    }

    // Raderar jobb
    public function deleteJob(): bool {

        $query = $this->conn->prepare('DELETE FROM experience_portfolio_2 WHERE job_id = ?');
        $query->bind_param('i', $this->id);
        $query->execute();

        if (!$this->conn->connect_error) {

            $this->confirm = 'Jobbet har raderats.';
            return true;

        } else {

            $this->error = 'Det gick inte att radera jobbet: ' . 
                $this->conn->connect_error;
            return false;
        }
    }

    // Hämtar enskilda utbildningars ID ur arrayen
    public function getID($id): bool {

        if ($this->job = $this->jobArr[$id]) {

            $this->id = $this->job['job_id'];
            return true;

        } else {

            $this->error = 'Det gick inte att hitta jobbet.';
            return false;
        }
    }
}