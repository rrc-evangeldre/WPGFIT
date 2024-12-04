<?php
require '../activity/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    session_start();

    // Get the comment ID from the POST request
    $commentId = filter_input(INPUT_POST, 'commentid', FILTER_SANITIZE_NUMBER_INT);

    // Make sure the user is logged in and is an Admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && in_array('Admin', $_SESSION['role'])) {
        
        // Proceed to delete the comment if it exists
        $deleteQuery = "DELETE FROM comments WHERE commentid = :commentid";
        $deleteStmt = $db->prepare($deleteQuery);
        if ($deleteStmt->execute([':commentid' => $commentId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}

// Default response for invalid request
echo json_encode(['success' => false]);
?>