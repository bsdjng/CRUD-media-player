<?php
// add_like.php

require("Connection.php");

if (isset($_GET['videoId']) && isset($_GET['action']) && isset($_GET['accountId'])) {
    $videoId = $_GET['videoId'];
    $accountId = $_GET['accountId'];
    $action = $_GET['action'];

    $dislikeValue = ($action === 'dislike') ? true : false;

    // Check if the user has already liked or disliked the video using cookies
    $cookieKey = 'likedVideos'; // Adjust as needed
    $userLikes = isset($_COOKIE[$cookieKey]) ? json_decode($_COOKIE[$cookieKey], true) : [];

    if ($action === 'like' && in_array($videoId, $userLikes)) {
        // User has already liked this video

    } elseif ($action === 'dislike' && in_array("-$videoId", $userLikes)) {
        // User has already disliked this vide
    } else {
        // User is liking or disliking the video for the first time
        $dislikeValue = ($action === 'dislike') ? true : false;

        // Update the likes or dislikes in the database
        $sqlUpdateCount = '';
        if ($action === 'like') {
            $sqlUpdateCount = "UPDATE videos SET likes = likes + 1 WHERE id = :videoId";
        } elseif ($action === 'dislike') {
            $sqlUpdateCount = "UPDATE videos SET dislikes = dislikes + 1 WHERE id = :videoId";
        }

        if (!empty($sqlUpdateCount)) {
            $updateCount = $pdo->prepare($sqlUpdateCount);
            $updateCount->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $updateCount->execute();

            // Insert the like or dislike into the database
            $sqlInsertLike = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, :dislike)";
            $insertLike = $pdo->prepare($sqlInsertLike);
            $insertLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $insertLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
            $insertLike->bindParam(':dislike', $dislikeValue, PDO::PARAM_BOOL);
            $insertLike->execute();

            // Update the user's cookies to track the liked or disliked video
            if ($action === 'like') {
                $userLikes[] = $videoId;
            } elseif ($action === 'dislike') {
                $userLikes[] = "-$videoId";
            }

            // Set the updated cookies with an expiration time (adjust as needed)
            $cookieExpiration = time() + (365 * 24 * 3600); // 1 year
            setcookie($cookieKey, json_encode($userLikes), $cookieExpiration, '/');

            // Respond with the updated count
            $sqlGetCount = "SELECT " . ($action === 'like' ? 'likes' : 'dislikes') . " FROM videos WHERE id = :videoId";
            $getCount = $pdo->prepare($sqlGetCount);
            $getCount->bindParam(':videoId', $videoId, PDO::PARAM_INT);

            if ($getCount->execute() && $getCount->rowCount() > 0) {
                $updatedCount = $getCount->fetch(PDO::FETCH_ASSOC)[$action === 'like' ? 'likes' : 'dislikes'];
                echo $updatedCount;
            } else {
                echo "Error retrieving updated count.";
            }
        } else {
            echo "Invalid action.";
        }
    }
} else {
    echo "Invalid request.";
}
