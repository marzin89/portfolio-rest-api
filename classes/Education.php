<?php
// Inkluderar databasklassen
include_once 'Database.php';

// Klass som hanterar utbildningar
class Education {

    // Properties
    public $id;
    public $course;
    public $school;
    public $start_date;
    public $end_date;
    public $updated;
    public $updated_by;
    public $educationArr = [];
    public $education = [];
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

        $query = 'SELECT * FROM education_portfolio_2';
        $result = $this->conn->query($query);

        if($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                array_push($this->educationArr, $row);
            }

        } else {
            $this->error = 'Inga utbildningar hittades.';
        }
    }

    // Lägger till utbildningar
    public function addCourse(): bool {

        $user = new User();
        $this->updated_by = $user->username;
        $query = '';

        if ($this->end_date) {

            $query = $this->conn->prepare('INSERT INTO education_portfolio_2
                (course, school, education_start_date, education_end_date, 
                education_updated_by) VALUES (?, ?, ?, ?, ?)');
            $query->bind_param('sssss', $this->course, $this->school, $this->start_date,
                $this->end_date, $this->updated_by);
            
        } else {

            $query = $this->conn->prepare('INSERT INTO education_portfolio_2
                (course, school, education_start_date, 
                education_updated_by) VALUES (?, ?, ?, ?)');
            $query->bind_param('ssss', $this->course, $this->school, $this->start_date,
                $this->updated_by);
        }

        $query->execute();

        if (!$this->conn->connect_error) {
            
            $this->confirm = 'Utbildningen har lagts till.';
            return true;
        
        } else {

            $this->error = 'Det gick inte att lägga till utbildningen: ' . 
                $this->conn->connect_error;
            return false; 
        }
    }

    // Uppdaterar utbildningar
    public function updateCourse(): bool {

        $user = new User();
        $this->updated_by = $user->username;
        $query = '';
        $this->updated = date('Y-m-d H:i:s');

        if ($this->end_date) {

            $query = $this->conn->prepare('UPDATE education_portfolio_2 SET course = ?, 
            school = ?, education_start_date = ?, education_end_date = ?, 
            education_updated = ?, education_updated_by = ? WHERE education_id = ?');
            $query->bind_param('ssssssi', $this->course, $this->school, $this->start_date,
            $this->end_date, $this->updated, $this->updated_by, $this->id);
        
        } else {

            $query = $this->conn->prepare('UPDATE education_portfolio_2 SET course = ?, 
            school = ?, education_start_date = ?, education_updated = ?, 
            education_updated_by = ? WHERE education_id = ?');
            $query->bind_param('sssssi', $this->course, $this->school, $this->start_date,
            $this->updated, $this->updated_by, $this->id);
        }

        $query->execute();

        if (!$this->conn->connect_error) {

            $this->confirm = 'Utbildningen har uppdaterats.';
            return true;
        
        } else {
            
            $this->error = 'Det gick inte att uppdatera utbildningen: ' . 
                $this->conn->connect_error;
            return false;
        }    
    }
}