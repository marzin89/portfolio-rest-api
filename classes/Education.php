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

        $query = '';

        if ($this->end_date) {

            $query = $this->conn->prepare('INSERT INTO education_portfolio_2
                (course, school, education_start_date, education_end_date) 
                VALUES (?, ?, ?, ?)');
            $query->bind_param('ssss', $this->course, $this->school, $this->start_date,
                $this->end_date);
            
        } else {

            $query = $this->conn->prepare('INSERT INTO education_portfolio_2
                (course, school, education_start_date) VALUES (?, ?, ?)');
            $query->bind_param('sss', $this->course, $this->school, $this->start_date);
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

        $query = '';
        $this->updated = date('Y-m-d H:i:s');

        if ($this->end_date) {

            $query = $this->conn->prepare('UPDATE education_portfolio_2 SET course = ?, 
            school = ?, education_start_date = ?, education_end_date = ?, 
            education_updated = ? WHERE education_id = ?');
            $query->bind_param('sssssi', $this->course, $this->school, $this->start_date,
            $this->end_date, $this->updated, $this->id);
        
        } else {

            $query = $this->conn->prepare('UPDATE education_portfolio_2 SET course = ?, 
            school = ?, education_start_date = ?, education_updated = ? WHERE education_id = ?');
            $query->bind_param('ssssi', $this->course, $this->school, $this->start_date,
            $this->updated, $this->id);
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

    // Raderar utbildningar
    public function deleteCourse(): bool {

        $query = $this->conn->prepare('DELETE FROM education_portfolio_2 WHERE education_id = ?');
        $query->bind_param('i', $this->id);
        $query->execute();

        if (!$this->conn->connect_error) {

            $this->confirm = 'Utbildningen har raderats.';
            return true;

        } else {

            $this->error = 'Det gick inte att radera utbildningen: ' . 
                $this->conn->connect_error;
            return false;
        }
    }

    // Hämtar enskilda utbildningars ID ur arrayen
    public function getID($id): bool {

        if ($this->education = $this->educationArr[$id]) {

            $this->id = $this->education['education_id'];
            return true;
        
        } else {

            $this->error = 'Det gick inte att hitta utbildningen.';
            return false;
        }
    }
}