<?php
/*******w******** 

    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This script deletes posts from the system. Only admins are allowed to 
                 access this functionality. 

****************/
require '../activity/db_connect.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !in_array('Admin', $_SESSION['role'])) {
    echo "Unauthorized action."; // Prevent unauthorized access
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the post ID from the request
    $postId = filter_input(INPUT_POST, 'postid', FILTER_SANITIZE_NUMBER_INT);

    // Delete the post
    $deleteQuery = "DELETE FROM Posts WHERE PostID = :postId";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindValue(':postId', $postId, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
        // Redirect to the home page with a success message
        header("Location: ../navlinks/index.php?message=PostDeleted");
        exit;
    } else {
        echo "Error deleting post."; // Display error if the query fails
    }
}
?>