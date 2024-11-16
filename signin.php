<?php
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: This handles attempted user logins.
    
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
    // Split roles into an array if there are multiple roles
    $roles = explode(', ', $user['Role']);

    // Set session variables to indicate logged-in status and store user info
    $_SESSION['user_id'] = $user['UserID'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['role'] = $roles; // Store roles as an array
    $_SESSION['is_logged_in'] = true;

    // Redirect to the home page or dashboard
    header("Location: index.php");
    exit;
} else {
    // Login failed, redirect back to the login page with an error
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit;
}
