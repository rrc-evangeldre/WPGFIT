<?php
/*******w******** 

    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This script makes sure that only logged in users can access the page 
                 and will redirect to a login page if they aren't.

****************/
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ../logins/login.php");
    exit;
}
?>