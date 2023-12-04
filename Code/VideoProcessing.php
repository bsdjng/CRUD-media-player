<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'Connection.php';

$videoName = $_POST['videoName'];
$videoThumbnailFile = $_FILES['videoThumbnail']; // Corrected variable name
$videoDescription = $_POST['videoDescription'];
$currentDate = (new DateTime())->format('Y-m-d H:i:s');

// Read file contents for the video thumbnail
$videoThumbnailData = file_get_contents($videoThumbnailFile['tmp_name']); // Corrected variable name

$sqlVideoInsert = "INSERT INTO videos (account_id, video_name, video_description, views, likes, dislikes, thumbnail_image, created_at) VALUES (:account_id, :video_name, :video_description, 0, 0, 0, :thumbnail_image, :created_at)";
$stmtVideoInsert = $pdo->prepare($sqlVideoInsert);
$stmtVideoInsert->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
$stmtVideoInsert->bindParam(':video_name', $videoName, PDO::PARAM_STR);
$stmtVideoInsert->bindParam(':video_description', $videoDescription, PDO::PARAM_STR);
$stmtVideoInsert->bindParam(':thumbnail_image', $videoThumbnailData, PDO::PARAM_LOB);
$stmtVideoInsert->bindParam(':created_at', $currentDate, PDO::PARAM_STR);

if ($stmtVideoInsert->execute()) {
    Header("Location: Account.php?=" . $_SESSION['account_id']);
    exit();
} else {
    echo "Error inserting video information: " . print_r($stmtVideoInsert->errorInfo(), true);
}
