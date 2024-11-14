<?php 
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 14, 2024
    Description: Create a new post with this page.

****************/

include 'activity/header.php';
?>

<div class="container mt-5">
  <div class="post-container p-4 border rounded">
    <form accept-charset="UTF-8" action="" method="POST" enctype="multipart/form-data" target="_blank">
      <div class="post-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" id="title" placeholder="Enter a title for your post" required="required">
      </div>
      <div class="post-group">
        <label for="description" required="required">Description</label>
        <input type="text" name="description" class="form-control" id="description" placeholder="body text (optional)">
      </div>
      <div class="post-group">
        <label for="category">Add a Tag (optional)</label>
        <select class="form-control" id="category" name="platform">
          <option>General (default)</option>
          <option>Advice</option>
          <option>Question</option>
          <option>Discussion</option>
          <option>Cardio</option>
          <option>Strength Training</option>
          <option>Nutrition</option>
        </select>
      </div>
      <hr>
      <div class="post-group mt-3">
        <label class="mr-2">Upload your files:</label>
        <input type="file" name="file">
      </div>
      <hr>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</div>
