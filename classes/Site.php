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

        // Lägger till webbplatser
        public function addSite(): bool {

            $query = $this->conn->prepare('INSERT INTO site_portfolio_2
                (site_name, site_image_path, site_description, site_url)
                VALUES (?, ?, ?, ?)');
            $query->bind_param('ssss', $this->name, $this->img_path, $this->description, 
                $this->url);
            
            $query->execute();

            if (!$this->conn->connect_error) {

                $this->confirm = 'Webbplatsen har lagts till.';
                return true;
            
            } else {

                $this->error = 'Det gick inte att lägga till webbplatsen: ' . 
                    $this->conn->connect_error;
                return false;
            }
        }

        // Uppdaterar webbplatser
        public function updateSite(): bool {

            $this->updated = date('Y-m-d H:i:s');
            
            $query = $this->conn->prepare('UPDATE site_portfolio_2 SET site_name = ?,
                site_image_path = ?, site_description = ?, site_url = ?, site_updated = ?
                WHERE site_id = ?');
            $query->bind_param('sssssi', $this->name, $this->img_path, $this->description,
                $this->url, $this->updated, $this->id);
            
            $query->execute();

            if (!$this->conn->connect_error) {

                $this->confirm = 'Webbplatsen har uppdaterats.';
                return true;

            } else {

                $this->error = 'Det gick inte att uppdatera webbplatsen: ' . 
                    $this->conn->connect_error;
                return false;
            }
        }

        // Hämtar enskilda webbplatsers ID ur arrayen
        public function getID($id): bool {

            if ($this->site = $this->siteArr[$id]) {

                $this->id = $this->site['site_id'];
                return true;

            } else {

                $this->error = 'Det gick inte att hitta webbplatsen.';
                return false;
            }
        }

        // Raderar webbplatser
        public function deleteSite() : bool {

            $query = $this->conn->prepare('DELETE FROM site_portfolio_2 WHERE site_id = ?');
            $query->bind_param('i', $this->id);
            $query->execute();

            if (!$this->conn->connect_error) {

                $this->confirm = 'Webbplatsen har raderats.';
                return true;

            } else {

                $this->error = 'Det gick inte att radera webbplatsen: ' . $this->conn->connect_error;
                return false;
            }
        }
    }