<?php
require("Connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the POST request
    $videoId = $_POST['videoId'];
    $accountId = $_POST['accountId'];

    // Insert a new dislike record into the database
    // (Make sure to handle database connections and queries securely)
    $sqlInsert = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 1)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $stmtInsert->bindParam(':videoId', $videoId, PDO::PARAM_INT);
    $stmtInsert->execute();
}
