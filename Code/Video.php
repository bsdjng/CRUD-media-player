<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Video.css">
    <link rel="stylesheet" href="Header.css">
</head>

<body>
    <?php
    require("Header.php");
    require("Connection.php");
    require("Search.php");




    if (isset($_GET['id'])) {
        //get video variables from id
        $videoId = $_GET['id'];
        $sqlVideos = "SELECT id, account_id, video_name, views, likes, dislikes, video_description, created_at FROM videos WHERE id = :videoId";
        $videos = $pdo->prepare($sqlVideos);
        $videos->bindParam(':videoId', $videoId, PDO::PARAM_INT);
        $videos->execute();

        //set like and dislike variables
        $sqlLikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 0";
        $likesResult = $pdo->query($sqlLikes);
        $likes = $likesResult->fetchAll(PDO::FETCH_ASSOC);
        $sqlDislikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 1";
        $DislikesResult = $pdo->query($sqlDislikes);
        $dislikes = $DislikesResult->fetchAll(PDO::FETCH_ASSOC);
        var_dump($likes, $dislikes);
        if ($videos->rowCount() > 0) {
            $video = $videos->fetch(PDO::FETCH_ASSOC);
            $sqlAccounts = "SELECT id, username, profile_picture FROM accounts WHERE id = " . $video['account_id'];
            $accountsResult = $pdo->query($sqlAccounts);
            $accounts = $accountsResult->fetchAll(PDO::FETCH_ASSOC);
            var_dump($accounts[0]['id']);
    ?>
            <video width="640" height="360" controls>
                <source src="http://localhost/CRUD-media-player/Usercontent/<?php echo $accounts[0]['id']; ?>/<?php echo $videoId . '.mp4' ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <?php
            echo $video['video_name'] . "<br>";

            echo $video['video_description'] . "<br>";

            echo $video['views'] . "<br>";

            ?>
            <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                <?php echo $likes[0]['COUNT(*)']; ?>
            </button>

            <button id="dislikeButton" onclick="dislikeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                <?php echo $dislikes[0]['COUNT(*)']; ?>
            </button>
    <?php
        } else {

            echo "Video not found.";
        }
    } else {
        echo "Video ID not provided.";
    }
    ?>
    <script>
        function likeVideo(videoId, accountId) {
            // Use AJAX to call like.php asynchronously
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "like.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("videoId=" + videoId + "&accountId=" + accountId);
        }

        function dislikeVideo(videoId, accountId) {
            // Use AJAX to call dislike.php asynchronously
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "dislike.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("videoId=" + videoId + "&accountId=" + accountId);
        }
    </script>
</body>

</html>