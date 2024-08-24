<?php
function getDbConnection() {
    // Get the POSTGRES_URL environment variable
    $connection_string = $_ENV['POSTGRES_URL'];

    // Log the connection attempt
    error_log("Attempting to connect to the database using POSTGRES_URL.");

    // Attempt to connect to the database using the provided connection string
    $dbconn = pg_connect($connection_string);

    // Check if the connection was successful
    if (!$dbconn) {
        error_log("Connection failed: " . pg_last_error());
        die("Connection failed: " . pg_last_error());
    } 

    // Log successful connection
    error_log("Connection to the database was successful.");

    return $dbconn;
}
?>
