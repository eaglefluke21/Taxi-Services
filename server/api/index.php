<?php
include_once(__DIR__ . '/../config/database.php');
echo "Backend is Running.";
$databaseObj = new Database();
$db = $databaseObj->getConnection();

if ($db) {
    echo" Connected to database Successfully";
} else{
    echo " Failed to connect to database.";
}



?>