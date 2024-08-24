<?php
function getDbConnection() {
    $host = $_ENV['PG_HOST'];
    $port = $_ENV['PG_PORT'];
    $db = $_ENV['PG_DB'];
    $user = $_ENV['PG_USER'];
    $password = $_ENV['PG_PASSWORD'];
    $endpoint = $_ENV['PG_ENDPOINT'];

    $connection_string = "host=" . $host . " port=" . $port . " dbname=" . $db . " user=" . $user . " password=" . $password . " options='endpoint=" . $endpoint . "' sslmode=require";

    error_log("Attempting to connect to the database at $host:$port with user $user.");

    $dbconn = pg_connect($connection_string);

    if (!$dbconn) {
        error_log("Connection failed: " . pg_last_error());
        die("Connection failed: " . pg_last_error());
    } 

        error_log("Connection to the database was successful.");
 
        echo "Connected successfully";

    return $dbconn;
}

getDbConnection();


?>