<?php  
/*******w******** 
 
    Name: Raphael Evangelista
    Date: November 14, 2024
    Description: Create a new post with this page.
    
****************/

include '../activity/db_connect.php';
include '../activity/header.php';

// Makes sure the user is logged in before allowing them to post
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message if the user is not logged in
    $_SESSION['login_error'] = "You must be logged in to make a post.";
    header("Location: ../logins/login.php");
    exit();
}

// Initialize variables
$uploadError = '';
$filePath = null;

// Function to check file MIME type and extension
function file_is_allowed($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];  // Allowed image MIME types
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];  // Allowed file extensions

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type = mime_content_type($temporary_path);

    return in_array($actual_file_extension, $allowed_file_extensions) && in_array($actual_mime_type, $allowed_mime_types);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form fields
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = isset($_POST['category']) ? implode(', ', $_POST['category']) : 'General'; // Default to 'General' if no category is selected
    $userID = $_SESSION['user_id']; // Get the logged-in user's ID
    $visibility = 'Public';

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $filePath = $uploadDir . basename($_FILES['file']['name']);

        // Validate file type
        if (file_is_allowed($_FILES['file']['tmp_name'], $filePath)) {
            // Move the uploaded file to the "uploads" directory
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                $uploadError = 'File upload failed.';
            }
        } else {
            $uploadError = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
            $filePath = null;  // Set filePath to null if file is not allowed
        }
    }

    // Only insert into the database if there's no upload error
    if (empty($uploadError)) {
        try {
            // Insert post into the database, including file path if valid
            $stmt = $db->prepare("INSERT INTO Posts (UserID, Title, Content, Category, Visibility, filePath) 
                                  VALUES (:userID, :title, :description, :category, :visibility, :filePath)");
            $stmt->execute([
                ':userID' => $userID,
                ':title' => $title,
                ':description' => $description,
                ':category' => $category,
                ':visibility' => $visibility,
                ':filePath' => $filePath
            ]);
            // Redirect to index page after successful post creation
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $uploadError = "Error: " . $e->getMessage();  // Catch any DB errors and display them
        }
    }
}
?>

<div class="container mt-5">
  <div class="post-container p-4 border rounded">
    <form accept-charset="UTF-8" action="" method="POST" enctype="multipart/form-data">
      <div class="post-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" id="title" placeholder="Enter a title for your post" required="required">
      </div>
      <div class="post-group">
        <label for="description">Description</label>
        <input type="text" name="description" class="form-control" id="description" placeholder="body text (optional)">
      </div>
      <div class="post-group">
      <div class="checkbox-group">
        <input type="checkbox" id="general" name="category[]" value="General" checked> General
        <input type="checkbox" id="advice" name="category[]" value="Advice"> Advice
        <input type="checkbox" id="question" name="category[]" value="Question"> Question
        <input type="checkbox" id="discussion" name="category[]" value="Discussion"> Discussion
        <input type="checkbox" id="cardio" name="category[]" value="Cardio"> Cardio
        <input type="checkbox" id="strength" name="category[]" value="Strength"> Strength
        <input type="checkbox" id="nutrition" name="category[]" value="Nutrition"> Nutrition
        <input type="checkbox" id="progress" name="category[]" value="Progress"> Progress
      </div>
      </div>
      <hr>
      <div class="post-group mt-3">
        <label class="mr-2">Upload your files:</label>
        <input type="file" name="files[]" multiple>
      </div>
      <hr>

      <!-- Display error message if file validation fails -->
      <?php if ($uploadError): ?>
        <div class="alert alert-danger mt-3"><?php echo $uploadError; ?></div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</div>

<script src="../js/post.js"></script>