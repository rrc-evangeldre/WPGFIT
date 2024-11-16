<?php  
/*******w******** 
    Name: Raphael Evangelista
    Date: November 14, 2024
    Description: Edit an existing post with this page.
****************/

include 'db_connect.php';
include 'activity/header.php';

// Ensure the user is logged in before allowing them to edit
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message if the user is not logged in
    $_SESSION['login_error'] = "You must be logged in to edit a post.";
    header("Location: login.php");
    exit();
}

// Fetch the post to edit
$postId = filter_input(INPUT_GET, 'postid', FILTER_SANITIZE_NUMBER_INT);
if (!$postId) {
    echo "<p class='text-center mt-5'>Invalid post ID.</p>";
    exit();
}

// Fetch post details from the database
$query = "SELECT * FROM Posts WHERE PostID = :postid";
$stmt = $db->prepare($query);
$stmt->bindValue(':postid', $postId, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<p class='text-center mt-5'>Post not found.</p>";
    exit();
}

// Check if the user is authorized to edit the post
$userID = $_SESSION['user_id'];
$isAdmin = false;

if (isset($_SESSION['role'])) {
    // If role is an array, don't use explode, just assign it
    if (is_array($_SESSION['role'])) {
        $roles = $_SESSION['role'];
    } else {
        // Otherwise, split the string into an array
        $roles = explode(', ', $_SESSION['role']);
    }
    $isAdmin = in_array('Admin', $roles);
}

if ($post['UserID'] != $userID && !$isAdmin) {
    echo "<p class='text-center mt-5'>You are not authorized to edit this post.</p>";
    exit();
}

// Initialize variables
$uploadError = '';
$filePath = $post['filePath'] ?? null;
$categories = explode(', ', $post['Category']); // Current categories

// Function to check file MIME type and extension
function file_is_allowed($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];  // Allowed image MIME types
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];  // Allowed file extensions

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type = mime_content_type($temporary_path);

    return in_array($actual_file_extension, $allowed_file_extensions) && in_array($actual_mime_type, $allowed_mime_types);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $title = $_POST['title'] ?? $post['Title'];
    $description = $_POST['description'] ?? $post['Content'];
    $selectedCategories = isset($_POST['category']) ? implode(', ', $_POST['category']) : 'General';
    
    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $filePath = $uploadDir . basename($_FILES['file']['name']);

        // Validate file type
        if (file_is_allowed($_FILES['file']['tmp_name'], $filePath)) {
            // Move uploaded file
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                $uploadError = 'File upload failed.';
            }
        } else {
            $uploadError = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
            $filePath = $post['filePath'];  // Revert to the existing file if upload fails
        }
    }

    // Update the database if there's no upload error
    if (empty($uploadError)) {
        try {
            $updateQuery = "UPDATE Posts SET Title = :title, Content = :description, Category = :category, filePath = :filePath 
                            WHERE PostID = :postid";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':category' => $selectedCategories,
                ':filePath' => $filePath,
                ':postid' => $postId
            ]);
            // Redirect to post view page after successful update
            header("Location: single_post.php?postid=" . $postId);
            exit();
        } catch (PDOException $e) {
            $uploadError = "Error: " . $e->getMessage();  // Catch any DB errors
        }
    }
}
?>

<div class="container mt-5">
  <div class="post-container p-4 border rounded">
    <form accept-charset="UTF-8" action="" method="POST" enctype="multipart/form-data">
      <div class="post-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" id="title" value="<?= htmlspecialchars($post['Title']) ?>" required>
      </div>
      <div class="post-group">
        <label for="description">Description</label>
        <input type="text" name="description" class="form-control" id="description" value="<?= htmlspecialchars($post['Content']) ?>">
      </div>
      <div class="post-group">
        <label>Add Tags (optional)</label><br>
        <?php
        $categoriesList = ['General', 'Advice', 'Question', 'Discussion', 'Cardio', 'Strength', 'Nutrition', 'Progress'];
        foreach ($categoriesList as $category) {
            $checked = in_array($category, $categories) ? 'checked' : '';
            echo "<input type='checkbox' name='category[]' value='$category' $checked> $category ";
        }
        ?>
      </div>
      <hr>
      <div class="post-group mt-3">
        <label class="mr-2">Upload a new file:</label>
        <input type="file" name="file">
        <?php if ($post['filePath']): ?>
          <p>Current file: <a href="<?= htmlspecialchars($post['filePath']) ?>" target="_blank">View</a></p>
        <?php endif; ?>
      </div>
      <hr>

      <!-- Display error message if file validation fails -->
      <?php if ($uploadError): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($uploadError) ?></div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary">Update Post</button>
      <a href="single_post.php?postid=<?= $postId ?>" class="btn btn-secondary">Cancel</a>
    </form>
    <!-- Delete button -->
    <form action="delete.php" method="post" class="d-inline">
                <input type="hidden" name="postid" value="<?= $postId ?>">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
            </form>
  </div>
</div>

<?php include 'activity/footer.php'; ?>
