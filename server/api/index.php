<?php
function getDbConnection() {
    $connection_string = getenv('POSTGRES_URL');

    error_log("Attempting to connect to the database using POSTGRES_URL: " . $connection_string);

    $dbconn = pg_connect($connection_string);

    if (!$dbconn) {
        die("Connection Failure ");
    } 

    error_log("Connection to the database was successful.");

    return $dbconn;
}


?>
