<?php
include_once __DIR__ . '/index.php';

require_once __DIR__ .'/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$reactUrl = getenv('REACT_URL');


header("Access-Control-Allow-Origin: $reactUrl");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$db = getDbConnection();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
    case 'GET':
        checkStatus($db);
        break;
    case 'PUT':
        updateStatus($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function checkStatus($db) {
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
        $decodedDriverId = $decoded->id;

        // Verify driver ID
        $query = "SELECT * FROM drivers WHERE user_id = $1";
        $result = pg_query_params($db, $query, [$decodedDriverId]);
        $driver = pg_fetch_assoc($result);

        if (!$driver) {
            http_response_code(404);
            echo json_encode(array("message" => "Driver not found"));
            return;
        }

        // Fetch booking status for the driver
        $query = "SELECT * FROM userbooking WHERE driver_id = $1 AND status = 'pending'";
        $result = pg_query_params($db, $query, [$driver['id']]);
        $booking = pg_fetch_assoc($result);

        if ($booking) {
            http_response_code(200);
            echo json_encode($booking);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No pending bookings found"));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}

function updateStatus($db) {
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
        $decodedDriverId = $decoded->id;

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->status)) {
            http_response_code(400);
            echo json_encode(array("message" => "No status provided"));
            return;
        }

        // Verify driver ID
        $query = "SELECT * FROM drivers WHERE user_id = $1";
        $result = pg_query_params($db, $query, [$decodedDriverId]);
        $driver = pg_fetch_assoc($result);

        if (!$driver) {
            http_response_code(404);
            echo json_encode(array("message" => "Driver not found"));
            return;
        }

        // Update booking status
        $query = "UPDATE userbooking SET status = $1 WHERE driver_id = $2 AND status = 'pending'";
        $result = pg_query_params($db, $query, [$data->status, $driver['id']]);

        if ($result) {
            // Fetch updated booking
            $query = "SELECT * FROM userbooking WHERE driver_id = $1 AND status = $2";
            $result = pg_query_params($db, $query, [$driver['id'], $data->status]);
            $updatedBooking = pg_fetch_assoc($result);

            http_response_code(200);
            echo json_encode($updatedBooking);
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update booking status"));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}
?>