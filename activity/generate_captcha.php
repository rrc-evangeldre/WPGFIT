<?php
/*******w******** 

    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This script generates a CAPTCHA image when a user is submitting a comment.

****************/
session_start();

header('Content-Type: image/png');

// Generate random 5 character CAPTCHA text
$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"), 0, 5);
$_SESSION['captcha'] = $captchaText;

// Create an image
$image = imagecreate(120, 40);
$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Add text to image
imagestring($image, 5, 30, 10, $captchaText, $textColor);

// Output the image
imagepng($image);
imagedestroy($image);
?>