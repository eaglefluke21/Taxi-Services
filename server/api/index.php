<?php
function getDbConnection() {
    $connection_string = getenv('POSTGRES_URL');

    error_log("Attempting to connect to the database using POSTGRES_URL: " . $connection_string);

    $dbconn = pg_connect($connection_string);

    if (!$dbconn) {
        die("Connection Failure: " . pg_last_error());
    }

    error_log("Connection to the database was successful.");

    // createUsersTable($dbconn);
    // createEnumType($dbconn);
    //createDriversTable($dbconn);
    //createUserBookingTable($dbconn);

    return $dbconn;
}



function createUsersTable($dbconn) {
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $result = pg_query($dbconn, $createTableSQL);

    if (!$result) {
        error_log("Error creating table: " . pg_last_error($dbconn));
        die("Error creating table: " . pg_last_error($dbconn));
    }

    error_log("Table 'users' created or it  already exists.");
}

function createEnumType($dbconn) {
    // Check if the ENUM type exists
    $checkTypeSQL = "SELECT 1 FROM pg_type WHERE typname = 'availability_enum'";
    $result = pg_query($dbconn, $checkTypeSQL);

    if (pg_num_rows($result) === 0) {
        // ENUM type does not exist, so create it
        $enumTypeSQL = "CREATE TYPE availability_enum AS ENUM ('available', 'unavailable')";
        $result = pg_query($dbconn, $enumTypeSQL);

        if (!$result) {
            error_log("Error creating ENUM type for availability: " . pg_last_error($dbconn));
            die("Error creating ENUM type for availability: " . pg_last_error($dbconn));
        }

        error_log("ENUM type 'availability_enum' created.");
    } else {
        error_log("ENUM type 'availability_enum' already exists.");
    }
}

function createDriversTable($dbconn) {
    // Ensure ENUM type exists
    createEnumType($dbconn);

    // Create table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS drivers (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        user_id INT NOT NULL,
        is_available availability_enum DEFAULT 'available',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";

    $result = pg_query($dbconn, $createTableSQL);

    if (!$result) {
        error_log("Error creating table 'drivers': " . pg_last_error($dbconn));
        die("Error creating table 'drivers': " . pg_last_error($dbconn));
    }

    error_log("Table 'drivers' created or it already exists.");
}






function createUserBookingTable($dbconn) {
    // Define the ENUM type for status if it does not exist
    $enumTypeSQL = "DO \$\$ BEGIN
        CREATE TYPE status_enum AS ENUM ('pending', 'accepted', 'rejected');
    EXCEPTION
        WHEN duplicate_object THEN NULL;
    END \$\$;";
    
    $result = pg_query($dbconn, $enumTypeSQL);
    
    if (!$result) {
        error_log("Error creating ENUM type for status: " . pg_last_error($dbconn));
        die("Error creating ENUM type for status: " . pg_last_error($dbconn));
    }
    
    error_log("ENUM type 'status_enum' created or it already exists.");

    // Create the userbooking table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS userbooking (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        pickup VARCHAR(255) NOT NULL,
        dropoff VARCHAR(255) NOT NULL,
        car VARCHAR(255) NOT NULL,
        passengers INT NOT NULL,
        description TEXT,
        status status_enum DEFAULT 'pending',
        user_id INT,
        driver_id INT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL
    );
    ";

    $result = pg_query($dbconn, $createTableSQL);

    if (!$result) {
        error_log("Error creating table 'userbooking': " . pg_last_error($dbconn));
        die("Error creating table 'userbooking': " . pg_last_error($dbconn));
    }

    error_log("Table 'userbooking' created or it already exists.");
}
getDbConnection();

?>
