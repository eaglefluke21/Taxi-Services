<?php
include_once __DIR__ . '/index.php';

require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

$reactUrl = getenv('REACT_URL');

header("Access-Control-Allow-Origin: $reactUrl");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$db = getDbConnection(); // Ensure this returns a valid PgSql connection resource

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
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
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getUsers($db) {
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
        $userId = $decoded->id;

        $query = "SELECT id, name, pickup, dropoff, car, passengers, description, status FROM userbooking WHERE user_id = $1";
        $result = pg_query_params($db, $query, array($userId));

        if (!$result) {
            http_response_code(500);
            echo json_encode(array("message" => "Query failed: " . pg_last_error($db)));
            return;
        }

        $users = array();

        while ($row = pg_fetch_assoc($result)) {
            $user_item = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "pickup" => $row['pickup'],
                "dropoff" => $row['dropoff'],
                "car" => $row['car'],
                "passengers" => $row['passengers'],
                "description" => $row['description'],
                "status" => $row['status']
            );

            array_push($users, $user_item);
        }

        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}

function createUser($db) {
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
        $userId = $decoded->id;

        error_log($userId);

        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->name) &&
            !empty($data->pickup) &&
            !empty($data->dropoff) &&
            !empty($data->car) &&
            !empty($data->passengers) &&
            !empty($data->description)
        ) {
            // Validate and sanitize the input
            $name = htmlspecialchars(strip_tags($data->name));
            $pickup = htmlspecialchars(strip_tags($data->pickup));
            $dropoff = htmlspecialchars(strip_tags($data->dropoff));
            $car = htmlspecialchars(strip_tags($data->car));
            $passengers = filter_var($data->passengers, FILTER_VALIDATE_INT);
            $description = htmlspecialchars(strip_tags($data->description));

            if ($passengers === false) {
                http_response_code(400);
                echo json_encode(array("message" => "Invalid number of passengers."));
                return;
            }

            // Check for available driver
            $query = "SELECT id FROM drivers WHERE is_available = 'available' LIMIT 1";
            $result = pg_query($db, $query);

            if (!$result) {
                http_response_code(500);
                echo json_encode(array("message" => "Query failed: " . pg_last_error($db)));
                return;
            }

            $driver = pg_fetch_assoc($result);

            if ($driver) {
                $driverId = $driver['id'];

                // Create a new booking with status 'pending'
                $query = "INSERT INTO userbooking (user_id, driver_id, name, pickup, dropoff, passengers, car, description, status) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'pending')";
                $params = array($userId, $driverId, $name, $pickup, $dropoff, $passengers, $car, $description);
                $result = pg_query_params($db, $query, $params);

                if (!$result) {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to create booking."));
                    return;
                }

                // Update driver availability
                $query = "UPDATE drivers SET is_available = FALSE WHERE id = $1";
                $result = pg_query_params($db, $query, array($driverId));

                if (!$result) {
                    http_response_code(500);
                    echo json_encode(array("message" => "Failed to update driver availability: " . pg_last_error($db)));
                    return;
                }

                http_response_code(201);
                echo json_encode(array("message" => "Booking created and driver assigned.", "driver_id" => $driverId));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No available drivers."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}
?>
