
<?php

/*******w******** 

    Name: Raphael Evangelista
    Date: Nov. 8, 2024
    Description: This file establishes a PDO connection to the WPGFIT database in phpMyAdmin.

****************/

// Prevent constant redefinition
if (!defined('DB_DSN')) {
    define('DB_DSN', 'mysql:host=localhost;dbname=wpgfit;charset=utf8');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

try {
    // Create a new PDO connection to MySQL
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    // Enable exceptions for error handling
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Print error message and stop execution
    print "Error: " . $e->getMessage();
    die(); 
    // Note: For production, use a secure error logging method instead of printing to the user
}

?>
