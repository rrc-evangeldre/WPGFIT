<?php
include 'activity/header.php';  // Include the header file for the page layout
require 'db_connect.php';  // Database connection file

if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start session if not already started
}

// Check if post ID is provided in the URL
if (isset($_GET['postid'])) {
    $postId = filter_input(INPUT_GET, 'postid', FILTER_SANITIZE_NUMBER_INT);

    // Query to fetch post details along with the username of the author
    $query = "SELECT Posts.*, Users.Username, Users.UserID 
              FROM Posts 
              JOIN Users ON Posts.UserID = Users.UserID 
              WHERE Posts.PostID = :postId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {

        if (isset($_SESSION['role'])) {
            // If $_SESSION['role'] is a string, split it into an array
            $roles = is_array($_SESSION['role']) ? $_SESSION['role'] : explode(', ', $_SESSION['role']);
        } else {
            $roles = [];
        }

        // Check if the logged-in user is the post author or an Admin
        $isEditable = isset($_SESSION['user_id']) && 
                      ($_SESSION['user_id'] == $post['UserID'] || 
                      in_array('Admin', $roles));
        ?>
        <div class="container mt-5">
    <div class="post-container position-relative p-4 border rounded">
        <!-- Edit/Delete Buttons (only for the author or admins) -->
        <?php if ($isEditable): ?>
        <div class="edit-button">
            <!-- Edit button -->
            <a href="edit.php?postid=<?= $postId ?>" class="fa-solid fa-pen-to-square"></a>
        </div>
        <?php endif; ?>

        <!-- Post Content -->
        <h1><?= htmlspecialchars($post['Title']) ?></h1>
        <p class="post-meta">By <?= htmlspecialchars($post['Username']) ?> | <?= date('F j, Y', strtotime($post['DateCreated'])) ?></p>
        
        <!-- Display image if it exists -->
        <?php if (!empty($post['filePath'])): ?>
        <div class="post-image mt-3">
            <img src="<?= htmlspecialchars($post['filePath']) ?>" alt="Post Image" class="img-fluid rounded">
        </div>
        <?php endif; ?>

        <div class="post-content mt-3">
            <?= nl2br(htmlspecialchars($post['Content'])) ?>
        </div>
        <hr>

        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Posts</a>
        </div>
    </div>
</div>

        <?php
    } else {
        // If the post is not found
        echo "<p class='text-center mt-5'>Post not found.</p>";
    }
} else {
    // If no post ID is provided in the URL
    echo "<p class='text-center mt-5'>No post ID provided.</p>";
}
?>
