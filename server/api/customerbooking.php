<?php
include_once '../config/database.php';

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:POST ,GET ,OPTIONS");
header("Acces-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Headers, Authorization, X-Requested-With");



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
    case 'GET':
        error_log('Handling GET request');
        getUsers($db);
        break;
    case 'POST':
        error_log('Handling POST request');
            createUser($db);
            break;
    default:
            http_response_code(405);
            echo json_encode(array("message"=>"Method not allowed"));
            break;
            
}

function getUsers($db){
    $query = " SELECT id, name,pickup,dropoff,car,passengers,description FROM userbooking";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $users = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $user_item = array(

            "id" => $id,
            "name" => $name,
            "pickup" => $pickup,
            "dropoff" => $dropoff,
            "car" => $car,
            "passengers" => $passengers,
            "description" => $description

        );

        array_push($users, $user_item);
    }

    echo json_encode($users);
};


function createUser($db) {
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->name) &&
        !empty($data->pickup) &&
        !empty($data->dropoff) &&
        !empty($data->car) &&
        !empty($data->passengers) &&
        !empty($data->description)
    ) {
        $query = "INSERT INTO userbooking (name, pickup, dropoff, car, passengers, description) VALUES (:name, :pickup, :dropoff, :car, :passengers, :description)";

        $stmt = $db->prepare($query);

        // Store sanitized values in variables
        $name = htmlspecialchars(strip_tags($data->name));
        $pickup = htmlspecialchars(strip_tags($data->pickup));
        $dropoff = htmlspecialchars(strip_tags($data->dropoff));
        $car = htmlspecialchars(strip_tags($data->car));
        $passengers = htmlspecialchars(strip_tags($data->passengers));
        $description = htmlspecialchars(strip_tags($data->description));

        // Bind parameters
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":pickup", $pickup);
        $stmt->bindParam(":dropoff", $dropoff);
        $stmt->bindParam(":car", $car);
        $stmt->bindParam(":passengers", $passengers);
        $stmt->bindParam(":description", $description);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "User was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create user."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data."));
    }
}



?>