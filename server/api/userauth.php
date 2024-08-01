<?php
include_once '../config/database.php';

require '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: http://localhost:5173");




header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:POST ,GET ,OPTIONS");
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



function createUser($db,$data) {
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
    $query = "SELECT id FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(array("message" => "Email already exists"));
        return;
    }

     // Hash the password
     $hashed_password = password_hash($password, PASSWORD_BCRYPT);

     // Insert the new user
     $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
     $stmt = $db->prepare($query);
     $stmt->bindParam(':username', $username);
     $stmt->bindParam(':email', $email);
     $stmt->bindParam(':password', $hashed_password);
     $stmt->bindParam(':role', $role);
 
     if ($stmt->execute()) {

        $user_id = $db->lastInsertId();


        if ($role === 'driver') {
            $driver_query = "INSERT INTO drivers (name, user_id, is_available) VALUES (:name, :user_id, TRUE)";
            $driver_stmt = $db->prepare($driver_query);
            $driver_stmt->bindParam(':name', $username);
            $driver_stmt->bindParam(':user_id', $user_id);

            if ($driver_stmt->execute()) {
                http_response_code(201);
                echo json_encode(array("message" => "User and driver created successfully"));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Failed to create driver"));
            }
        } else {
            http_response_code(201);
            echo json_encode(array("message" => "User created successfully"));
        }


     } else {
         http_response_code(500);
         echo json_encode(array("message" => "Failed to create user"));
     }
 
}


function loginUser($db,$data){
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validate input data
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(array("message" => "Missing required fields"));
        return;
    }

       // Fetch the user by email

       $query = "SELECT id,username,  password ,role  FROM users WHERE email = :email";

       $stmt = $db->prepare($query);
       $stmt->bindParam(':email', $email);
       $stmt->execute();
   
       if ($stmt->rowCount() === 0) {
           http_response_code(401);
           echo json_encode(array("message" => "Invalid email or password"));
           return;
       }

       $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stored_password = $user['password'];


   
    if (password_verify($password, $stored_password)) {

        $key = $_ENV['jwt_token']; 
        $payload = array(
            'iss' => 'your_domain.com', 
            'iat' => time(), 
            'exp' => time() + 3600, 
            'id' => $user['id'] ,
            'role' => $user['role']
        );

        $jwt = JWT::encode($payload, $key,'HS256');

       
        http_response_code(200);
        echo json_encode(array("message" => "Login successful", "token" => $jwt));

    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid email or password"));
    }
    
};



?>