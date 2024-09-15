<?php
include_once __DIR__ . '/index.php';

require_once __DIR__ .'/../vendor/autoload.php';

use \Firebase\JWT\JWT;

$reactUrl = getenv('REACT_URL');

header("Access-Control-Allow-Origin: $reactUrl");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
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
        http_response_code(405);
        echo json_encode(array("message" => "Requested Page : User Auth"));
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $action = $data['action'] ?? '';

        if ($action === 'signup') {
            createUser($db, $data);
        } elseif ($action === 'login') {
            loginUser($db, $data);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid action"));
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function createUser($db, $data) {
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';

    // Validate input data
    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Missing required fields"));
        return;
    }

    // Check if email already exists
    $query = "SELECT id FROM users WHERE email = $1";
    $result = pg_query_params($db, $query, array($email));

    if (!$result) {
        http_response_code(500);
        echo json_encode(array("message" => "Query failed: " . pg_last_error($db)));
        return;
    }

    if (pg_num_rows($result) > 0) {
        http_response_code(400);
        echo json_encode(array("message" => "Email already exists"));
        return;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user
    $query = "INSERT INTO users (username, email, password, role) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($db, $query, array($username, $email, $hashed_password, $role));

    if (!$result) {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to create user: " . pg_last_error($db)));
        return;
    }

    $user_id = pg_fetch_result(pg_query($db, "SELECT LASTVAL()"), 0, 0);

    if ($role === 'driver') {
        $driver_query = "INSERT INTO drivers (name, user_id, is_available) VALUES ($1, $2, 'available')";
        $driver_result = pg_query_params($db, $driver_query, array($username, $user_id));

        if (!$driver_result) {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create driver: " . pg_last_error($db)));
        } else {
            http_response_code(201);
            echo json_encode(array("message" => "User and driver created successfully"));
        }
    } else {
        http_response_code(201);
        echo json_encode(array("message" => "User created successfully"));
    }
}

function loginUser($db, $data) {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validate input data
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Missing required fields"));
        return;
    }

    // Fetch the user by email
    $query = "SELECT id, username, password, role FROM users WHERE email = $1";
    $result = pg_query_params($db, $query, array($email));

    if (!$result) {
        http_response_code(500);
        echo json_encode(array("message" => "Query failed: " . pg_last_error($db)));
        return;
    }

    if (pg_num_rows($result) === 0) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid email or password"));
        return;
    }

    $user = pg_fetch_assoc($result);
    $stored_password = $user['password'];

    if (password_verify($password, $stored_password)) {
        $key = getenv('jwt_token');
        $payload = array(
            'iss' => 'your_domain.com',
            'iat' => time(),
            'exp' => time() + 3600,
            'id' => $user['id'],
            'role' => $user['role']
        );

        $jwt = JWT::encode($payload, $key, 'HS256');

        http_response_code(200);
        echo json_encode(array("message" => "Login successful", "token" => $jwt));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid email or password"));
    }
}
?>
