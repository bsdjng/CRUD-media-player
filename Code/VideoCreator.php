<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Main.css">
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="AccountLogin.css">
</head>

<body>
    <?php
    require 'Header.php';
    require 'Connection.php';
    ?>
    <form action="VideoProcessing.php" method="post" id="videoSubmitForm" enctype="multipart/form-data">
        <input type="text" name="videoName" required>
        <input type="file" name="video" required>
        <input type="file" name="videoThumbnail" required>
        <input type="text" name="videoDescription" required>
        <input type="submit">
    </form>