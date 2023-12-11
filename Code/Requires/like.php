<?php

require("Connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the POST request
    $videoId = $_POST['videoId'];
    $accountId = $_POST['accountId'];
    $status = $_POST['likeStatus'];

    switch ($status) {
        case "add_like":
            $sqlInsert = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 0)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtInsert->execute();
            break;
        case "add_dislike":
            $sqlInsert = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 1)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtInsert->execute();
            break;
        case "remove_like":
            $sqlDelete = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 0";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtDelete->execute();
            break;
        case "remove_dislike":
            $sqlDelete = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 1";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtDelete->execute();
            break;
        case "remove_like_add_dislike":
            $sqlRemoveLike = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 0";
            $stmtRemoveLike = $pdo->prepare($sqlRemoveLike);
            $stmtRemoveLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtRemoveLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtRemoveLike->execute();

            $sqlAddDislike = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 1)";
            $stmtAddDislike = $pdo->prepare($sqlAddDislike);
            $stmtAddDislike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtAddDislike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtAddDislike->execute();
            break;

        case "remove_dislike_add_like":
            $sqlRemoveDislike = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 1";
            $stmtRemoveDislike = $pdo->prepare($sqlRemoveDislike);
            $stmtRemoveDislike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtRemoveDislike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtRemoveDislike->execute();

            $sqlAddLike = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 0)";
            $stmtAddLike = $pdo->prepare($sqlAddLike);
            $stmtAddLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $stmtAddLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $stmtAddLike->execute();
            break;
        default:
            echo "Invalid status.";
            break;
    }
}
