<?php
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: This handles the registration process by validating user inserted values.

****************/
session_start();
include 'db_connect.php';

// Check if passwords match
if ($_POST['password'] !== $_POST['password_confirm']) {
    $_SESSION['register_error'] = "Passwords do not match. Try again.";
    header("Location: login.php");
    exit;
}

// Sanitize and hash input data
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$username = htmlspecialchars($_POST['username']);
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if username or email is already taken
    $checkQuery = "SELECT * FROM users WHERE Email = :email OR Username = :username";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $_SESSION['register_error'] = "Username or email is already taken. Please choose another.";
        header("Location: login.php");
        exit;
    }

    // Insert user data into database
    $query = "INSERT INTO users (Email, Username, Password) VALUES (:email, :username, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registration successful. Please log in.";
    } else {
        $_SESSION['register_error'] = "Registration failed. Try again.";
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { 
        $_SESSION['register_error'] = "Email is already registered.";
    } else {
        $_SESSION['register_error'] = "Database error: " . $e->getMessage();
    }
}

header("Location: login.php");
exit;
?>
