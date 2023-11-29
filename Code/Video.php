<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Video.css">
    <link rel="stylesheet" href="Header.css">
</head>

<body style="background-color: violet;">
    <?php
    require("Header.php");
    require("Connection.php");
    require("Search.php");




    if (isset($_GET['id'])) {
        $videoId = $_GET['id'];
        $sqlVideos = "SELECT id, account_id, video_name, views, likes, dislikes, video_description, created_at FROM videos WHERE id = :videoId";
        $videos = $pdo->prepare($sqlVideos);
        $videos->bindParam(':videoId', $videoId, PDO::PARAM_INT);
        $videos->execute();

        $sqlLikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 0";
        $likesResult = $pdo->query($sqlLikes);
        $likes = $likesResult->fetchAll(PDO::FETCH_ASSOC);
        var_dump($likes);
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

            <button style="background-color: red; width: 100px; height 50px;" id="likeButton" onclick="toggleLike(<?php echo $videoId; ?>, 'likeButton', 'like', <?php echo $accounts[0]['id'] ?>)">
                <!-- <?php echo $likes[0]['COUNT(video_id)']; ?> -->
            </button>

            <button style="background-color: blue; width: 100px; height 50px;" id="dislikeButton" onclick="toggleLike(<?php echo $videoId; ?>, 'dislikeButton', 'dislike', <?php echo $accounts[0]['id'] ?>)">
                <?php echo $video['dislikes']; ?>
            </button>
    <?php
        } else {

            echo "Video not found.";
        }
    } else {
        echo "Video ID not provided.";
    }
    ?>
</body>
<script>
    function toggleLike(videoId, buttonId, action, accountId) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Log the response to the console
                    console.log('Response:', xhr.responseText);

                    // Update the UI with the response
                    document.getElementById(buttonId).innerHTML = xhr.responseText;

                    // Toggle the state
                    toggleState(buttonId);
                } else {
                    console.error('Error:', xhr.status);
                }
            }
        };

        // Update the path to your add_like.php script
        var url = 'add_like.php?videoId=' + videoId + '&action=' + action + '&accountId=' + accountId;
        xhr.open('GET', url, true);
        xhr.send();
    }

    function toggleState(buttonId) {
        var button = document.getElementById(buttonId);
        var currentState = button.getAttribute('data-state');

        if (currentState === 'liked') {
            button.setAttribute('data-state', 'unliked');
        } else {
            button.setAttribute('data-state', 'liked');
        }
    }
</script>