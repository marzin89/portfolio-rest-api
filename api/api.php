<?php
/* Webbtjänst som läser ut, lägger till, uppdaterar och raderar
utbildningar, jobb och webbplatser */

// Inkluderar klasserna
include_once '../classes/Database.php';
include_once '../classes/Site.php';
include_once '../classes/Education.php';
include_once '../classes/Experience.php';

// Headers
// Typ av innehåll
header('Content-Type: application/json; charset=UTF8');
// Tillåter åtkomst från alla domäner
header('Access-Control-Allow-Origin: *');
// CRUD
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT');
// CORS
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

// Läser in metod i anropet
$method = $_SERVER['REQUEST_METHOD'];

// Läser in ID om det finns
if(isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Läser in kategori (utbildning, jobb, webbplats) om den finns
if(isset($_GET['cat'])) {
    $cat = $_GET['cat'];
}

// Nya instanser
$database = new Database();
$site = new Site();
$education = new Education();
$experience = new Experience();

// Lägg till ett felmeddelande om anslutningen till databasen misslyckades
if($database->error) {
    $response_db = $database->error;
}

// Switch-sats
switch($method) {
    // Om data ska läsas ut
    case 'GET':

        // Om utbildningar eller jobb ska hämtas
        if ($cat == 'education' || $cat == 'experience') {

            // Om det finns utbildningar 
            if(count($education->educationArr) > 0) {

                // Bekräftar anropet
                http_response_code(200);
                // Lägg till arrayen med utbildningar
                $response_education = $education->educationArr;
            
            // Om inga utbildningar finns
            } else {

                // Lägg till ett felmeddelande
                http_response_code(404);
                $response_education = array('message' => $education->error);
            } 

            // Om det finns jobb
            if(count($experience->jobArr) > 0) {

                // Bekräftar anropet
                http_response_code(200);
                // Lägg till arrayen med utbildningar
                $response_job = $experience->jobArr;
                
            // Om inga jobb finns
            } else {
                
                // Lägg till ett felmeddelande
                http_response_code(404);
                $response_job = array('message' => $experience->error);
            }

            // Slår samman arrayerna om utbildningar eller jobb ska hämtas
            $response = array_merge($response_education, $response_job);

            // Skickar svaret i JSON-format
            echo json_encode($response);
        
        // Om webbplatser ska hämtas
        } else if ($cat == 'site') {

            // Om alla webbplatser ska hämtas
            if ($id == '') {

                // Om det finns webbplatser
                if (count($site->siteArr) > 0) {

                    // Bekräftar anropet
                    http_response_code(200);
                    // Lägg till arrayen med webbplatser
                    $response_site = $site->siteArr;
                
                // Om inga webbplatser finns
                } else {

                    // Lägg till ett felmeddelande
                    http_response_code(404);
                    $response_site = array('message' => $site->error);
                }
            
            // Om en enskild webbplats ska hämtas
            } else if ($id >= 0) {

                // Om det går att hitta webbplatsen
                if ($site->getSite($id)) {

                    // Bekräftar anropet
                    http_response_code(200);
                    // Lägg till arrayen med webbplatser
                    $response_site = $site->site;
                
                // Om det inte går att hitta webbplatsen
                } else {

                    // Lägg till ett felmeddelande
                    http_response_code(404);
                    $response_site = array('message' => $site->error);
                }
            } 

            echo json_encode($response_site);
        }

        break;

    // Om data ska läggas till
    case 'POST':

        // Läser in all data i anropet
        $data = json_decode(file_get_contents('php://input'));

        // Om en utbildning ska läggas till
        if ($cat == 'education') {

            // Lagrar parametervärden i objektets properties
            $education->course = $data->course;
            $education->school = $data->school;
            $education->start_date = $data->start_date;
            $education->end_date = $data->end_date;

            // Om utbildningen har lagts till
            if ($education->addCourse()) {

                // Bekräftar anropet
                http_response_code(200);
                $response_education = array('message' => $education->confirm);
            
                // Om utbildningen inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $response_education = array('message' => $education->error);
            }
        
        } else if ($cat == 'experience') {

            // Lagrar parametervärden i objektets properties
            $experience->employment = $data->employment;
            $experience->employer = $data->employer;
            $experience->start_date = $data->start_date;
            $experience->end_date = $data->end_date;

            // Om jobbet har lagts till
            if ($experience->addJob()) {

                // Bekräftar anropet
                http_response_code(200);
                $response_job = array('message' => $experience->confirm);
            
                // Om utbildningen inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $response_job = array('message' => $experience->error);
            }            
        } else if ($cat == 'site') {

            // Lagrar parametervärden i objektets properties
            $site->name = $data->name;
            $site->img_path = $data->img_path;
            $site->description = $data->description;
            $site->url = $data->url;

            // Om jobbet har lagts till
            if ($site->addSite()) {

                // Bekräftar anropet
                http_response_code(200);
                $response_site = array('message' => $site->confirm);
            
                // Om utbildningen inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $response_site = array('message' => $site->error);
            }
        }
    
    default:
        # code...
        break;
}