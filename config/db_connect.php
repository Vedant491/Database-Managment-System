<?php
/**
 * Database Connection Configuration
 * Uses mysqli for database operations
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'college_fees_db');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

/**
 * Function to sanitize input data
 */
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Function to execute query and return result
 */
function execute_query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    return $result;
}

/**
 * Function to fetch single row
 */
function fetch_single($query) {
    $result = execute_query($query);
    return mysqli_fetch_assoc($result);
}

/**
 * Function to fetch all rows
 */
function fetch_all($query) {
    $result = execute_query($query);
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Function to get last inserted ID
 */
function get_last_id() {
    global $conn;
    return mysqli_insert_id($conn);
}

/**
 * Function to close database connection
 */
function close_connection() {
    global $conn;
    mysqli_close($conn);
}
?>
