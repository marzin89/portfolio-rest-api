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
}