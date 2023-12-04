<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "Connection.php";

var_dump($_SESSION);

$videoId = $_POST['videoId'];
$commenterID = $_SESSION['account_id'];
$commentText = $_POST['newCommentText'];
$commentText = htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8');
$date_Now = (new DateTime())->format('Y-m-d H:i:s');

// Sanitize the comment text to prevent HTML, CSS, or JavaScript injection
$commentText = htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8');

$sqlAddComment = "INSERT INTO comments (video_id, account_id, comment_text, created_at) VALUES (:video_id, :account_id, :comment_text, :created_at)";
$stmtAddComment = $pdo->prepare($sqlAddComment);
$stmtAddComment->bindParam(':video_id', $videoId, PDO::PARAM_INT);
$stmtAddComment->bindParam(':account_id', $commenterID, PDO::PARAM_INT);
$stmtAddComment->bindParam(':comment_text', $commentText, PDO::PARAM_STR);
$stmtAddComment->bindParam(':created_at', $date_Now, PDO::PARAM_STR);

$stmtAddComment->execute();

header('Location: Video.php?id=' . $videoId);
exit();
?>
