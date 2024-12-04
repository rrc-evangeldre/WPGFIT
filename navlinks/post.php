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
}
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message if the user is not logged in
    $_SESSION['login_error'] = "You must be logged in to make a post.";
    header("Location: ../logins/login.php");
    exit();
}

// Initialize variables
$filePath = null;
$uploadError = '';

// Function to check file MIME type and extension
function file_is_allowed($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];  // Allowed image MIME types
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];  // Allowed file extensions

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type = mime_content_type($temporary_path);

    return in_array($actual_file_extension, $allowed_file_extensions) && in_array($actual_mime_type, $allowed_mime_types);
}

// Function to resize the image using PHP GD library
function resize_image($source_path, $destination_path, $max_height) {
    $image_info = getimagesize($source_path);
    $mime_type = $image_info['mime'];

    switch ($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source_path);
            break;
        default:
            return false; // Unsupported file type
    }

    $width = imagesx($image);
    $height = imagesy($image);

    // Calculate the scaling factor to maintain aspect ratio
    $scale = $max_height / $height;

    $new_width = floor($width * $scale);
    $new_height = $max_height;

    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Preserve transparency for PNG and GIF
    if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
        imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }

    // Resize the image
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Save the resized image to the destination path
    switch ($mime_type) {
        case 'image/jpeg':
            imagejpeg($new_image, $destination_path, 90);
            break;
        case 'image/png':
            imagepng($new_image, $destination_path);
            break;
        case 'image/gif':
            imagegif($new_image, $destination_path);
            break;
    }

    imagedestroy($image);
    imagedestroy($new_image);

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form fields
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $categories = $_POST['category'] ?? []; // Categories are stored as an array
    $userID = $_SESSION['user_id']; // Get the logged-in user's ID
    $visibility = 'Public';

    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/'; // Set upload directory

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it does not exist
        }

        // Add a timestamp to prevent file overwriting
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $targetFilePath = $uploadDir . $fileName;

        // Validate file type
        if (file_is_allowed($_FILES['file']['tmp_name'], $targetFilePath)) {
            // Resize the image and move it to the target directory
            $resized = resize_image($_FILES['file']['tmp_name'], $targetFilePath, 220); // Set height to 220px
            if ($resized) {
                // Set filePath to be relative for accessing it correctly in your project
                $filePath = '../uploads/' . $fileName;
            } else {
                $uploadError = 'Error occurred while resizing and moving the uploaded file.';
            }
        } else {
            $uploadError = 'Invalid file type. Only JPG, PNG, and GIF files are allowed.';
        }
    }

    // Insert post into the database if there are no errors
    if (empty($uploadError)) {
        try {
            // Insert the post into the database
            $stmt = $db->prepare("INSERT INTO Posts (UserID, Title, Content, Category, Visibility, filePath) 
                                  VALUES (:userID, :title, :description, :category, :visibility, :filePath)");
            $stmt->execute([
                ':userID' => $userID,
                ':title' => $title,
                ':description' => $description,
                ':category' => 'General', // Default value
                ':visibility' => $visibility,
                ':filePath' => $filePath
            ]);
            
            $postId = $db->lastInsertId(); // Get the ID of the inserted post

            // Insert categories into the postcategories table
            foreach ($categories as $category) {
                $categoryStmt = $db->prepare("INSERT INTO PostCategories (PostID, CategoryID) VALUES (:postID, :categoryID)");
                $categoryStmt->execute([
                    ':postID' => $postId,
                    ':categoryID' => $category
                ]);
            }

            // Redirect to index page after successful post creation
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $uploadError = "Error inserting post into database.";
        }
    }
}
?>

<div class="container mt-5">
  <div class="post-container p-4 border rounded">
    <form accept-charset="UTF-8" action="" method="POST" enctype="multipart/form-data">
      <div class="post-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" id="title" placeholder="Enter a title for your post" required>
      </div>
      <div class="post-group">
        <label for="description">Description</label>
        <input type="text" name="description" class="form-control" id="description" placeholder="body text (optional)">
      </div>
      <div class="post-group">
        <div class="checkbox-group">
          <input type="checkbox" id="general" name="category[]" value="1" checked> General
          <input type="checkbox" id="advice" name="category[]" value="2"> Advice
          <input type="checkbox" id="question" name="category[]" value="3"> Question
          <input type="checkbox" id="discussion" name="category[]" value="4"> Discussion
          <input type="checkbox" id="cardio" name="category[]" value="5"> Cardio
          <input type="checkbox" id="strength" name="category[]" value="6"> Strength
          <input type="checkbox" id="nutrition" name="category[]" value="7"> Nutrition
          <input type="checkbox" id="progress" name="category[]" value="8"> Progress
        </div>
      </div>
      <hr>
      <div class="post-group mt-3">
        <label class="mr-2">Upload your file (optional):</label>
        <input type="file" name="file">
      </div>
      <hr>

      <!-- Display error message if file validation fails -->
      <?php if (!empty($uploadError)): ?>
        <div class="alert alert-danger mt-3"><?php echo $uploadError; ?></div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</div>

<script src="../js/post.js"></script>
