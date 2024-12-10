<?php
/*******w******** 
 
    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This script handles the submission of comments on posts. 
                 Including validation for comment text and the CAPTCHA.
    
****************/
session_start();
include '../activity/db_connect.php';

$response = [
    'success' => false,
    'errors' => [],
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commentText = trim($_POST['comment_text']);
    $captchaInput = trim($_POST['captcha']);
    $postId = $_POST['postid'];
    $userId = $_SESSION['user_id'];

    // Validation
    if (empty($commentText)) {
        $response['errors']['comment'] = "Comment should not be blank.";
    }

    if ($_SESSION['captcha'] !== $captchaInput) {
        $response['errors']['captcha'] = "CAPTCHA is incorrect.";
    }

    if (empty($response['errors'])) {
        // Insert comment into the database
        $query = "INSERT INTO comments (comment_text, postid, userid, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$commentText, $postId, $userId])) {
            $response['success'] = true;
            $response['username'] = $_SESSION['username'];
            $response['comment_text'] = htmlspecialchars($commentText);
            $response['created_at'] = date('Y-m-d H:i:s');
        }
    }
}

echo json_encode($response);
?>