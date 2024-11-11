<?php

/*******w******** 

    Name: Raphael Evangelista
    Date: Nov. 8, 2024
    Description: This file establishes a PDO connection to the WPGFIT database in phpMyAdmin.

****************/

define('DB_DSN', 'mysql:host=localhost;dbname=wpgfit;charset=utf8');
define('DB_USER', 'root');
define('DB_PASS', '');     

try {
    // Try creating a new PDO connection to MySQL
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    // Enable exceptions for error handling
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
    die(); // Force execution to stop on errors
    // When deploying to production, handle errors more gracefully ¯\_(ツ)_/¯
}
?>
