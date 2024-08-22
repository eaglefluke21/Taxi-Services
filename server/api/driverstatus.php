<?php
include_once '../config/database.php';

require '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: https://taxi-services-kappa.vercel.app");




header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:POST ,GET , PUT ,OPTIONS");
header("Acces-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Headers, Authorization, X-Requested-With");



if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}


$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();



$database = new Database();
$db = $database->getConnection();

$requestMethod = $_SERVER["REQUEST_METHOD"];


switch($requestMethod) {
    case 'GET':
        getInfoDriver($db);
        break;
    case 'PUT':
        changeAvailability($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getInfoDriver($db) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    error_log("Authorization Header: " . $authHeader);

    $token = str_replace('Bearer ', '', $authHeader);
    error_log("Extracted Token: " . $token);

    if (!$token) {
        http_response_code(401);
        echo json_encode(array("message" => "No token provided"));
        return;
    }

    try {
        $key = $_ENV['jwt_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $driverId = $decoded->id;

        $query = "SELECT id, name, is_available FROM drivers WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$driverId]);

        if ($stmt->rowCount() > 0) {
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($driver);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Driver not found"));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}

function changeAvailability($db) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (!$token) {
        http_response_code(401);
        echo json_encode(array("message" => "No token provided"));
        return;
    }

    try {
        $key = $_ENV['jwt_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $driverId = $decoded->id;

        error_log($driverId);

        $data = json_decode(file_get_contents("php://input"), true);
        $isAvailable = filter_var($data['is_available'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($isAvailable === null) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid input for is_available"));
            return;
        }
            
        error_log('before conversion'. $isAvailable);

        // Convert boolean to integer for SQL
        $isAvailable = $isAvailable ? 1 : 0;

        error_log('after conversion'. $isAvailable);

        $query = "UPDATE drivers SET is_available = ? WHERE user_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$isAvailable, $driverId])) {
            echo json_encode(array("message" => "Availability updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update availability"));
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}