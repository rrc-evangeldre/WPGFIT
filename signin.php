<?php
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: This handles attempted user log ins.
    
****************/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$username = htmlspecialchars($_POST['username']);
$password = $_POST['password'];

// Check the database for the username
$query = "SELECT * FROM users WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['Password'])) {
    // Set session variables to indicate logged-in status and store user info
    $_SESSION['user_id'] = $user['UserID'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['role'] = $user['Role'];
    // $_SESSION['login_success'] = "Login successful! Welcome, " . $user['Username'] . ".";
    $_SESSION['is_logged_in'] = true;  // Logged-in flag

    // Redirect to the home page or dashboard
    header("Location: index.php");
    exit;
} else {
    // Login failed, redirect back to the login page with an error
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit;
}