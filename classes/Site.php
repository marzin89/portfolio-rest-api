<?php
    // Inkluderar databasklassen
    include_once 'Database.php';

    // Klass som hanterar webbplatser
    class Site {

        // Properties
        public $id;
        public $name;
        public $img_path;
        public $description;
        public $url;
        public $updated;
        public $updated_by;
        public $siteArr = [];
        public $site = [];
        public $error;
        public $confirm;
        public $conn;

        // Metoder
        // Konstruerare
        public function __construct() {

            $database = new Database();
            $this->conn = $database->conn;

            $query = 'SELECT * FROM site_portfolio_2';
            $result = $this->conn->query($query);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    array_push($this->siteArr, $row);
                }
            
            } else {
                $this->error = 'Inga webbplatser hittades.';
            }
        }

        // Hämta enskild webbplats
        public function getSite($id): bool {

            if ($this->site = $this->siteArr[$id]) {
                return true;

            } else {
                return false;
            }
        }

        // Lägger till webbplatser
        public function addSite(): bool {

            $user = new User();
            $this->updated_by = $user->username;

            $query = $this->conn->prepare('INSERT INTO site_portfolio_2
                (site_name, site_image_path, site_description, site_url, site_updated_by)
                VALUES (?, ?, ?, ?, ?)');
            $query->bind_param('sssss', $this->name, $this->img_path, $this->description, 
                $this->url, $this->updated_by);
            
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