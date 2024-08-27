<?php
include_once __DIR__ . '/index.php';

require_once __DIR__ .'/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$reactUrl = getenv('REACT_URL');

header("Access-Control-Allow-Origin: $reactUrl");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:POST ,GET , PUT ,OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$db = getDbConnection();

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
        $key = getenv('jwt_token');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $driverId = $decoded->id;

        $query = "SELECT id, name, is_available FROM drivers WHERE user_id = $1";
        $result = pg_query_params($db, $query, [$driverId]);

        if (pg_num_rows($result) > 0) {
            $driver = pg_fetch_assoc($result);
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
        $key = getenv('jwt_token');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $driverId = $decoded->id;

        error_log($driverId);

        $data = json_decode(file_get_contents("php://input"), true);
        $isAvailable = $data['is_available'] ?? '';

        if (!in_array($isAvailable, ['yes', 'no'])) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid input for is_available"));
            return;
        }

        $query = "UPDATE drivers SET is_available = $1 WHERE user_id = $2";
        $result = pg_query_params($db, $query, [$isAvailable, $driverId]);

        if ($result) {
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


?>
