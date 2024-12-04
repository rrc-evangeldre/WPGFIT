<?php
include '../activity/header.php';
require '../activity/db_connect.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            // Splits role session into an array
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
                <!-- Post Title with Edit Button (only for the author or admins) -->
                <div class="post-header d-flex align-items-center">
                    <h1 class="flex-grow-1"><?= htmlspecialchars($post['Title']) ?></h1>
                    <?php if ($isEditable): ?>
                        <a href="../admin/edit.php?postid=<?= $postId ?>" class="fa-solid fa-pen-to-square edit-button ml-2" title="Edit Post"></a>
                    <?php endif; ?>
                </div>

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

                <!-- Comments Section -->
                <div class="comments-section mt-5">
                    <h3>Comments</h3>
                    <div id="comments-list">
                        <?php
                        // Fetch comments related to the current post
                        $commentsQuery = "SELECT c.commentid, c.comment_text, c.created_at, c.userid, u.username 
                                          FROM comments c 
                                          JOIN users u ON c.userid = u.userid 
                                          WHERE c.postid = :postid 
                                          ORDER BY c.created_at DESC";
                        $commentsStmt = $db->prepare($commentsQuery);
                        $commentsStmt->execute([':postid' => $postId]);
                        $comments = $commentsStmt->fetchAll();

                        if ($comments) {
                            foreach ($comments as $comment): ?>
                                <div class="comment mb-3" id="comment-<?= $comment['commentid']; ?>">
                                    <strong>
                                        <?= htmlspecialchars($comment['username']); ?>
                                        <?php if (in_array('Admin', $roles)): ?>
                                            <button class="btn btn-sm delete-comment" data-id="<?= $comment['commentid']; ?>" title="Delete Comment">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </strong>
                                    <p><?= htmlspecialchars($comment['comment_text']); ?></p>
                                    <small><?= date('Y-m-d H:i', strtotime($comment['created_at'])); ?></small>
                                </div>
                            <?php endforeach;
                        } else {
                            echo "<p>No comments yet. Be the first to comment!</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Leave a Comment Form -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="leave-comment mt-4">
                    <h3>Leave a Comment</h3>
                    <form id="comment-form">
                        <textarea name="comment_text" id="comment_text" class="form-control mb-2" required></textarea>
                        <div id="comment-error" class="text-danger"></div>
                        
                        <div class="captcha-container d-flex align-items-center mb-2">
                            <img src="../activity/generate_captcha.php" alt="CAPTCHA" id="captcha-image" class="mr-2">
                            <button type="button" id="regenerate-captcha" class="btn btn-sm ml-2" title="Regenerate CAPTCHA">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            </button>                        
                        </div>
                        <input type="text" name="captcha" id="captcha" class="form-control mb-2" placeholder="Enter CAPTCHA" required>
                        <div id="captcha-error" class="text-danger"></div>
                        
                        <input type="hidden" name="postid" value="<?= $postId; ?>">
                        <button type="button" id="submit-comment" class="btn btn-secondary">Submit Comment</button>
                    </form>
                </div>
                <?php else: ?>
                    <p class="mt-4">You must be <a href="../logins/login.php">logged in</a> to leave a comment.</p>
                <?php endif; ?>

                <div class="mt-3">
                    <a href="index.php" class="btn btn-secondary">Back to Posts</a>
                </div>
            </div>
        </div>

        <!-- AJAX for Comment Submission and Deletion -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $(document).ready(function() {
            // Event listeners for clearing error messages
            $('#comment_text').on('input', function() {
                $('#comment-error').text(''); // Clear the comment error message when typing starts
            });

            $('#captcha').on('input', function() {
                $('#captcha-error').text(''); // Clear the captcha error message when typing starts
            });

            // Handle comment submission via AJAX
            $('#submit-comment').click(function() {
                var commentText = $('#comment_text').val().trim();
                var captcha = $('#captcha').val().trim();
                var postId = <?= $postId; ?>;

                // Clear any existing errors
                $('#comment-error').text('');
                $('#captcha-error').text('');

                // AJAX request
                $.ajax({
                    url: '../activity/submit_comment_ajax.php',
                    type: 'POST',
                    data: {
                        comment_text: commentText,
                        captcha: captcha,
                        postid: postId
                    },
                    success: function(response) {
                        var data = JSON.parse(response);

                        if (data.success) {
                            // Append the new comment to the comments list
                            $('#comments-list').prepend(`
                                <div class="comment mb-3" id="comment-${data.comment_id}">
                                    <strong>${data.username}
                                        <?php if (in_array('Admin', $roles)): ?>
                                            <button class="btn btn-sm delete-comment" data-id="${data.comment_id}" title="Delete Comment">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </strong>
                                    <p>${data.comment_text}</p>
                                    <small>${data.created_at}</small>
                                </div>
                            `);
                            $('#comment_text').val('');
                            $('#captcha').val('');
                            regenerateCaptcha(); // Regenerate the CAPTCHA after successful submission
                        } else {
                            if (data.errors.comment) {
                                $('#comment-error').text(data.errors.comment);
                            }
                            if (data.errors.captcha) {
                                $('#captcha-error').text(data.errors.captcha);
                            }
                        }
                    }
                });
            });

            // Handle delete comment via AJAX
            $(document).on('click', '.delete-comment', function() {
                var commentId = $(this).data('id');
                
                // AJAX request to delete comment
                $.ajax({
                    url: '../activity/manage_comment.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        commentid: commentId
                    },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);

                            if (data.success) {
                                $('#comment-' + commentId).remove();
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error("Invalid JSON response from server:", response);
                            alert("An error occurred while trying to delete the comment.");
                        }
                    }
                });
            });

            // Handle CAPTCHA regeneration
            $('#regenerate-captcha').click(function() {
                regenerateCaptcha();
            });

            // Function to regenerate the CAPTCHA image
            function regenerateCaptcha() {
                $('#captcha-image').attr('src', '../activity/generate_captcha.php?' + new Date().getTime());
            }
        });
        </script>

        <?php
        //Troubleshooting
    } else {
        echo "<p class='text-center mt-5'>Post not found.</p>";
    }
} else {
    echo "<p class='text-center mt-5'>No post ID provided.</p>";
}
?>
