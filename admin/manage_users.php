<?php
session_start();
include '../activity/db_connect.php';

// Check if the current user is an Admin
if (!isset($_SESSION['role']) || !in_array('Admin', (array)$_SESSION['role'])) {
    // Creates an error message and redirects non-admin users to the home page
    $_SESSION['register_error'] = "Unauthorized access.";
    header("Location: ../index.php");
    exit();
}

// Retrieve the user ID and action (edit or delete) from the form submission
$userId = $_POST['user_id'] ?? null;
$action = $_POST['action'] ?? null;

// Handle the 'edit' action: Update the user's roles
if ($action === 'edit') {
    // Fetch the roles from the form, default to 'Member' if none are selected
    $roles = isset($_POST['roles']) ? implode(', ', $_POST['roles']) : 'Member';
    try {
        // Prepare and execute the SQL statement to update roles
        $stmt = $db->prepare("UPDATE users SET Role = :roles WHERE UserID = :userId");
        $stmt->execute([':roles' => $roles, ':userId' => $userId]);
        // Success message
        $_SESSION['success_message'] = "User roles updated successfully.";
    } catch (PDOException $e) {
        // Error message if update fails
        $_SESSION['register_error'] = "Failed to update roles: " . $e->getMessage();
    }
// Handle the 'delete' action: Remove the user from the database
} elseif ($action === 'delete') {
    try {
        // Prepare and execute the SQL statement to delete the user
        $stmt = $db->prepare("DELETE FROM users WHERE UserID = :userId");
        $stmt->execute([':userId' => $userId]);
        // Success message
        $_SESSION['success_message'] = "User deleted successfully.";
    } catch (PDOException $e) {
        // Error message if deletion fails
        $_SESSION['register_error'] = "Failed to delete user: " . $e->getMessage();
    }
}

header("Location: ../admin/admin.php");
exit();