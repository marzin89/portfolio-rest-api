<?php
/* Webbtjänst som läser ut, lägger till, uppdaterar och raderar
utbildningar, jobb och webbplatser */

// Inkluderar klasserna
include_once '../classes/Database.php';
include_once '../classes/Education.php';

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
$education = new Education();

// Lägg till ett felmeddelande om anslutningen till databasen misslyckades
if($database->error) {
    $response = $database->error;
}

// Switch-sats
switch($method) {
    // Om data ska läsas ut
    case 'GET':

        // Om det finns utbildningar 
        if(count($education->educationArr) > 0) {

            // Bekräftar anropet
            http_response_code(200);
            // Lägg till arrayen med utbildningar
            $response = $education->educationArr;
        
        // Om inga utbildningar finns
        } else {

            // Lägg till ett felmeddelande
            http_response_code(404);
            $response = array('message' => $education->error);
        } 
        break;
    
    default:
        # code...
        break;
}

// Skickar svaret i JSON-format
echo json_encode($response);