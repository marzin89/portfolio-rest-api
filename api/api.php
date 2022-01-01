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

// Skickar ett felmeddelande om anslutningen till databasen misslyckades
$error;
$confirm;

if($database->error) {
    $error = $database->error;
    echo json_encode($error);
}

// Switch-sats
switch($method) {

    // Om data ska läsas ut
    case 'GET':

        // Om utbildningar eller jobb ska hämtas
        if ($cat == 'education' || $cat == 'experience') {

            // Bekräftar anropet
            http_response_code(200);
            // Lägger till arrayen med utbildningar
            $response_education = $education->educationArr;
            // Lägger till arrayen med utbildningar
            $response_job = $experience->jobArr;
            // Slår samman arrayerna om utbildningar eller jobb ska hämtas
            $response = array_merge($response_education, $response_job);
            // Skickar svaret i JSON-format
            echo json_encode($response);
        
        // Om webbplatser ska hämtas
        } else if ($cat == 'site') {

            // Om alla webbplatser ska hämtas
            if ($id == '') {

                // Bekräftar anropet
                http_response_code(200);
                // Lägger till arrayen med webbplatser
                $response_site = $site->siteArr;
            
            // Om en enskild webbplats ska hämtas
            } else if ($id >= 0) {

                // Hämtar webbplatsen ur arrayen
                $site->getID($id);
                // Bekräftar anropet
                http_response_code(200);
                // Lägger till arrayen med webbplatser
                $response_site = $site->site;
            } 
    
            // Skickar svaret i JSON-format
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
                $confirm = array('message' => $education->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om utbildningen inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $education->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        
        // Om ett jobb ska läggas till
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
                $confirm = array('message' => $experience->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om jobbet inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $experience->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }

        // Om en webbplats ska läggas till
        } else if ($cat == 'site') {

            // Lagrar parametervärden i objektets properties
            $site->name = $data->name;
            $site->img_path = $data->img_path;
            $site->description = $data->description;
            $site->url = $data->url;

            // Om webbplatsen har lagts till
            if ($site->addSite()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $site->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om webbplatsen inte kunde läggas till
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $site->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        }

    break;
    
    // Om data ska uppdateras
    case 'PUT':

        // Läser in all data i anropet
        $data = json_decode(file_get_contents('php://input'));

        // Om en utbildning ska uppdateras
        if ($cat == 'education') {

            // Lagrar parametervärden i objektets properties
            $education->course = $data->course;
            $education->school = $data->school;
            $education->start_date = $data->start_date;
            $education->end_date = $data->end_date;

            // Hämtar utbildningens ID ur arrayen
            $education->getID($id);

            // Om det går att uppdatera utbildningen
            if ($education->updateCourse()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $education->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om det inte går att uppdatera utbildningen
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $education->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        
        // Om ett jobb ska uppdateras
        } else if ($cat == 'experience') {

            // Lagrar parametervärden i objektets properties
            $experience->employment = $data->employment;
            $experience->employer = $data->employer;
            $experience->start_date = $data->start_date;
            $experience->end_date = $data->end_date;

            // Hämtar jobbets ID ur arrayen
            $experience->getID($id);

            // Om jobbet har uppdaterats
            if ($experience->updateJob()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $experience->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om det inte går att uppdatera jobbet
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $experience->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            } 

        // Om en webbplats ska uppdateras
        } else if ($cat == 'site') {

            // Lagrar parametervärden i objektets properties
            $site->name = $data->name;
            $site->img_path = $data->img_path;
            $site->description = $data->description;
            $site->url = $data->url;

            // Hämtar webbplatsens ID ur arrayen
            $site->getID($id);

            // Om webbplatsen har uppdaterats
            if ($site->updateSite()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $site->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om det inte går att uppdatera webbplatsen
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $site->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        }   

    break;

    // Om data ska raderas
    case 'DELETE':

        // Läser in all data i anropet
        $data = json_decode(file_get_contents('php://input'));

        // Om en utbildning ska raderas
        if ($cat == 'education') {

            // Hämtar utbildningens ID ur arrayen
            $education->getID($id);

            // Om utbildningen har raderats
            if ($education->deleteCourse()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $education->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);
            
            // Om det inte går att radera utbildningen
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $education->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        
        // Om ett jobb ska raderas
        } else if ($cat == 'experience') {

            // Hämtar jobbets ID ur arrayen
            $experience->getID($id);

            // Om jobbet har raderats
            if ($experience->deleteJob()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $experience->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);

            // Om det inte går att radera jobbet
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $experience->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }

        // Om en webbplats ska raderas
        } else if ($cat == 'site') {

            // Hämtar webbplatsens ID ur arrayen
            $site->getID($id);

            // Om webbplatsen har raderats
            if ($site->deleteSite()) {

                // Bekräftar anropet
                http_response_code(200);
                $confirm = array('message' => $site->confirm);
                // Skickar svaret i JSON-format
                echo json_encode($confirm);

            // Om det inte går att radera webbplatsen
            } else {

                // Skickar ett felmeddelande
                http_response_code(503);
                $error = array('message' => $site->error);
                // Skickar svaret i JSON-format
                echo json_encode($error);
            }
        }

    break;
}