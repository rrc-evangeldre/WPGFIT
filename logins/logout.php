<?php
/*******w******** 

    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This script logs out the user by ending the session.

****************/
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>