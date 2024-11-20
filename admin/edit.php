<?php  
/*******w******** 
    Name: Raphael Evangelista
    Date: November 14, 2024
    Description: Edit an existing post with this page.
****************/

include '../activity/db_connect.php';
include '../activity/header.php';

// Ensure the user is logged in before allowing them to edit
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "You must be logged in to edit a post.";
    header("Location: ../logins/login.php");
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
$isAdmin = isset($_SESSION['role']) && in_array('Admin', (array)$_SESSION['role']);

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
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type = mime_content_type($temporary_path);

    return in_array($actual_file_extension, $allowed_file_extensions) && in_array($actual_mime_type, $allowed_mime_types);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $title = $_POST['title'] ?? $post['Title'];
    $description = $_POST['description'] ?? $post['Content'];
    $selectedCategories = isset($_POST['category']) ? implode(', ', $_POST['category']) : $post['Category'];
    $removeImage = isset($_POST['remove_image']); // Check if the user wants to remove the current image

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $newFilePath = $uploadDir . basename($_FILES['file']['name']);

        // Validate file type
        if (file_is_allowed($_FILES['file']['tmp_name'], $newFilePath)) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $newFilePath)) {
                // Delete old file if a new file is uploaded
                if ($filePath && file_exists($filePath)) {
                    unlink($filePath);
                }
                $filePath = $newFilePath;
            } else {
                $uploadError = 'File upload failed.';
            }
        } else {
            $uploadError = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }
    }

    // Handle image removal
    if ($removeImage && $filePath) {
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file from the server
        }
        $filePath = null;
    }

    // Update the database if there's no upload error
    if (empty($uploadError)) {
        try {
            $updateQuery = "UPDATE Posts 
                            SET Title = :title, Content = :description, Category = :category, filePath = :filePath 
                            WHERE PostID = :postid";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':category' => $selectedCategories,
                ':filePath' => $filePath,
                ':postid' => $postId
            ]);
            header("Location: ../navlinks/single_post.php?postid=" . $postId);
            exit();
        } catch (PDOException $e) {
            $uploadError = "Error: " . $e->getMessage();
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
        <div class="checkbox-group"> 
        <?php
        $categoriesList = ['General', 'Advice', 'Question', 'Discussion', 'Cardio', 'Strength', 'Nutrition', 'Progress'];
        foreach ($categoriesList as $category) {
            $checked = in_array($category, $categories) ? 'checked' : '';
            echo "<input type='checkbox' name='category[]' value='$category' $checked> $category";
        }
        ?>
        </div>
      </div>
      <hr>
      <div class="post-group mt-3">
        <label class="mr-2">Upload a new file (optional):</label>
        <input type="file" name="file">
        <?php if ($post['filePath']): ?>
          <p>Current file: <a href="<?= htmlspecialchars($post['filePath']) ?>" target="_blank">View</a></p>
          <input type="checkbox" name="remove_image" id="remove_image">
          <label for="remove_image">Remove current image</label>
        <?php endif; ?>
      </div>
      <hr>

      <?php if ($uploadError): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($uploadError) ?></div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary">Update Post</button>
      <a href="../navlinks/single_post.php?postid=<?= $postId ?>" class="btn btn-secondary">Cancel</a>
    </form>
    <form action="delete.php" method="post" class="d-inline">
      <input type="hidden" name="postid" value="<?= $postId ?>">
      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
    </form>
  </div>
</div>

<?php include '../activity/footer.php'; ?>
