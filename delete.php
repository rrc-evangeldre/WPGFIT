<?php
require 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = filter_input(INPUT_POST, 'postid', FILTER_SANITIZE_NUMBER_INT);

    // Fetch post to verify ownership or Admin role
    $query = "SELECT UserID FROM Posts WHERE PostID = :postId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && (isset($_SESSION['UserID']) && 
                 ($_SESSION['UserID'] == $post['UserID'] || $_SESSION['Role'] == 'Admin'))) {
        // User is authorized to delete
        $deleteQuery = "DELETE FROM Posts WHERE PostID = :postId";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindValue(':postId', $postId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            header("Location: index.php?message=PostDeleted");
            exit;
        } else {
            echo "Error deleting post.";
        }
    } else {
        echo "Unauthorized action.";
    }
} else {
    echo "Invalid request.";
}
?>
