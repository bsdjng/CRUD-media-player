<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'Connection.php';

$videoName = $_POST['videoName'];
$videoThumbnailFile = $_FILES['videoThumbnail'];
$videoFile = $_FILES['video'];
$videoDescription = $_POST['videoDescription'];
$currentDate = (new DateTime())->format('Y-m-d H:i:s');

// process the files uit de post
$videoThumbnailData = file_get_contents($videoThumbnailFile['tmp_name']);
$videoData = file_get_contents($videoFile['tmp_name']);
$dir = "../Usercontent/" . $_SESSION['account_id'];

if (!file_exists($dir) || !is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$sqlVideoInsert = "INSERT INTO videos (account_id, video_name, video_description, views, likes, dislikes, thumbnail_image, created_at) VALUES (:account_id, :video_name, :video_description, 0, 0, 0, :thumbnail_image, :created_at)";
$stmtVideoInsert = $pdo->prepare($sqlVideoInsert);
$stmtVideoInsert->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
$stmtVideoInsert->bindParam(':video_name', $videoName, PDO::PARAM_STR);
$stmtVideoInsert->bindParam(':video_description', $videoDescription, PDO::PARAM_STR);
$stmtVideoInsert->bindParam(':thumbnail_image', $videoThumbnailData, PDO::PARAM_LOB);
$stmtVideoInsert->bindParam(':created_at', $currentDate, PDO::PARAM_STR);

if ($stmtVideoInsert->execute()) {
    // Get the last inserted ID
    $lastInsertedID = $pdo->lastInsertId();

    // Rename the video file
    $newFileName = $lastInsertedID . '.mp4'; // Change the file extension based on the actual file type
    $destination = $dir . '/' . $newFileName;

    // Move the uploaded file to the destination directory
    if (move_uploaded_file($_FILES['video']['tmp_name'], $destination)) {
        echo "File uploaded successfully!";
    } else {
        echo "Error uploading file.";
    }

    Header("Location: Account.php?=" . $_SESSION['account_id']);
    exit();
} else {
    echo "Error inserting video information: " . print_r($stmtVideoInsert->errorInfo(), true);
}
