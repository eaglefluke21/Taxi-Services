<?php
include_once '../config/database.php';

require '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: http://localhost:5173");




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

function checkstatus($db) {
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

        

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}

function updatestatus($db) {
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

        

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Invalid token", "error" => $e->getMessage()));
    }
}