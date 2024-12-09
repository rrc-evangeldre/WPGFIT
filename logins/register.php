<?php
/*******w******** 

    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: Handles the registration process by validating user inputs and storing multiple roles as a comma-separated string.

****************/
session_start();
include '../activity/db_connect.php';

// Redirect non-admin-created accounts to login.php after registration
$redirectPage = isset($_POST['is_admin_action']) && $_POST['is_admin_action'] === 'true' 
    ? "../admin/admin.php" 
    : "login.php";

// Check if passwords match
if ($_POST['password'] !== $_POST['password_confirm']) {
    $_SESSION['register_error'] = "Passwords do not match. Try again.";
    header("Location: $redirectPage");
    exit;
}

// Sanitize and hash input data
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$username = htmlspecialchars(trim($_POST['username']));
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle roles
$roles = isset($_POST['membership']) ? (array)$_POST['membership'] : (isset($_POST['roles']) ? (array)$_POST['roles'] : []);
if (!in_array('Member', $roles)) {
    $roles[] = 'Member'; // 'Member' will always be a role
}

// Role order
$roleOrder = ['Admin', 'Professional', 'Influencer', 'Member'];
$roles = array_values(array_filter($roleOrder, function ($role) use ($roles) {
    return in_array($role, $roles);
}));

// Convert roles array to a comma-separated string
$rolesString = implode(', ', $roles);

try {
    // Check if username or email is already taken
    $checkQuery = "SELECT * FROM users WHERE Email = :email OR Username = :username";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $_SESSION['register_error'] = "Username or email is already taken. Please choose another.";
        header("Location: $redirectPage");
        exit;
    }

    // Insert user data into the database with roles as a comma-separated string
    $query = "INSERT INTO users (Email, Username, Password, Role) VALUES (:email, :username, :password, :roles)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':roles', $rolesString);

    if ($stmt->execute()) {
        if ($redirectPage === "../admin/admin.php") {
            // Notify admin of successful user creation
            $_SESSION['success_message'] = "User '$username' successfully created.";
        } else {
            // Automatically log in the user for self-registration
            $userId = $db->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $roles; // Store roles as an array in the session
            header("Location: ../navlinks/index.php");
            exit();
        }
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

header("Location: $redirectPage");
exit;
?>