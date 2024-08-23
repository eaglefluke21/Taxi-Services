<?php
include_once __DIR__ . '/index.php';

require_once __DIR__ .'/../vendor/autoload.php';

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
        checkstatus($db);
        break;
    case 'PUT':
        updatestatus($db);
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
        $key = $_ENV['jwt_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $decodeddriverId = $decoded->id;

        // Verify driver ID
        $query = "SELECT * FROM drivers WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $decodeddriverId);
        $stmt->execute();
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$driver) {
            http_response_code(404);
            echo json_encode(array("message" => "Driver not found"));
            return;
        }

        // Fetch booking status for the driver
        $query = "SELECT * FROM userbooking WHERE driver_id = :driver_id AND status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":driver_id", $driver['id']);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $key = $_ENV['jwt_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $decodeddriverId = $decoded->id;

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->status)) {
            http_response_code(400);
            echo json_encode(array("message" => "No status provided"));
            return;
        }

        // Verify driver ID
        $query = "SELECT * FROM drivers WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":user_id", $decodeddriverId);
        $stmt->execute();
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$driver) {
            http_response_code(404);
            echo json_encode(array("message" => "Driver not found"));
            return;
        }

        // Update booking status
        $query = "UPDATE userbooking SET status = :status WHERE driver_id = :driver_id AND status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $data->status);
        $stmt->bindParam(":driver_id", $driver['id']);
        if ($stmt->execute()) {
            // Fetch updated booking
            $query = "SELECT * FROM userbooking WHERE driver_id = :driver_id AND status = :status";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":driver_id", $driver['id']);
            $stmt->bindParam(":status", $data->status);
            $stmt->execute();
            $updatedBooking = $stmt->fetch(PDO::FETCH_ASSOC);

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
