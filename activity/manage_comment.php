<?php
/*******w******** 
 
    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This file handles the deletion of comments from the database by Admins.
    
****************/
require '../activity/db_connect.php';

// Check if the request method is POST and if the action is 'delete'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    session_start();

    // Get the comment ID from the POST request
    $commentId = filter_input(INPUT_POST, 'commentid', FILTER_SANITIZE_NUMBER_INT);

    // Ensure the user is logged in and has admin privileges
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && in_array('Admin', $_SESSION['role'])) {
        
        // Only admins can delete comments
        // Check if the comment exists in the database
        $query = "SELECT * FROM comments WHERE commentid = :commentid";
        $stmt = $db->prepare($query);
        $stmt->execute([':commentid' => $commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment) {
            // Comment found, proceed to delete
            $deleteQuery = "DELETE FROM comments WHERE commentid = :commentid";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->execute([':commentid' => $commentId]);
            
            // Success
            echo json_encode(['success' => true]);
            exit;
        } else {
            // Comment not found, return an error
            echo json_encode(['success' => false, 'message' => 'Comment not found.']);
            exit;
        }
    } else {
        // Unauthorized action
        echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>