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
        http_response_code(405);
        echo json_encode(array("message" => "Requested Page"));
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

function loginUser($db,$data){
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Please fill all the required fields."));
        return;
    }

    $query = "SELECT id, username, password, role FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful",
                "user" => array(
                    "id" => $id,
                    "username" => $username,
                    "email" => $email,
                    "role" => $role
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid email or password"));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid email or password"));
    }

    $stmt->close();
    
    
};


function createUser($db,$data) {
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';

    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Please fill all the required fields."));
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "User created successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "User creation failed: " . $stmt->error));
    }

    $stmt->close();
}



?>