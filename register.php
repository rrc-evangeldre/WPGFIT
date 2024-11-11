<?php
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

header("Location: index.php");
exit;
?>