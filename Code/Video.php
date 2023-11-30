<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="VideoStyle.css">
    <link rel="stylesheet" href="Comments.css">
</head>

<body>
    <?php
    session_start();
    require("Header.php");
    require("Connection.php");
    require("Search.php");

    if (isset($_GET['id'])) {
        //haal video id uit de $_GET
        $videoId = $_GET['id'];
        $sqlVideos = "SELECT id, account_id, video_name, views, likes, dislikes, video_description, created_at FROM videos WHERE id = :videoId";
        $videos = $pdo->prepare($sqlVideos);
        $videos->bindParam(':videoId', $videoId, PDO::PARAM_INT);
        $videos->execute();

        //tel likes
        $sqlLikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 0";
        $likesResult = $pdo->query($sqlLikes);
        $likes = $likesResult->fetchAll(PDO::FETCH_ASSOC);

        //tel dislikes
        $sqlDislikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 1";
        $DislikesResult = $pdo->query($sqlDislikes);
        $dislikes = $DislikesResult->fetchAll(PDO::FETCH_ASSOC);

        //zoek account die de video heeft geupload
        if ($videos->rowCount() > 0) {
            $video = $videos->fetch(PDO::FETCH_ASSOC);
            $sqlAccounts = "SELECT id, username, profile_picture FROM accounts WHERE id = " . $video['account_id'];
            $accountsResult = $pdo->query($sqlAccounts);
            $accounts = $accountsResult->fetchAll(PDO::FETCH_ASSOC);
    ?>


            <!-- display de video informatie -->
            <div class="centerdiv">
                <div class="video-container">
                    <video id="myVideo" controls autoplay ontimeupdate="updateProgress()">
                        <source src="http://localhost/CRUD-media-player/Usercontent/<?php echo $accounts[0]['id']; ?>/<?php echo $videoId . '.mp4' ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="text-container">
                    <div class="title">
                        <?php
                        echo $video['video_name'] . "<br>";
                        ?>
                    </div>
                    <div class="creator">
                        <div id="PFP_NAME">
                            <?php
                            $imageSrc = "data:image/png;base64," . base64_encode($accounts[0]['profile_picture']);
                            echo '<div class="PFP" style="background-image: url(\'' . $imageSrc . '\');"></div>';

                            echo '<p class="creator_name">' . $accounts[0]['username'] . '</p>';
                            ?>
                        </div>
                        <div>
                            <?php
                            if ($_SESSION['logged_in']) { ?>
                                <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                                    <?php echo $likes[0]['COUNT(*)']; ?>
                                </button>

                                <button id="dislikeButton" onclick="dislikeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                                    <?php echo $dislikes[0]['COUNT(*)']; ?>
                                </button>
                            <?php } else {
                                echo "Log-in to like this video!";
                            } ?>

                        </div>
                    </div>
                    <div class="description">
                        <?php
                        $datetime = new DateTime($video['created_at']);
                        $formattedDate = $datetime->format("M d, Y");

                        echo '<p class="views_date">' . $video['views'] . " views - " . $formattedDate . "<br>" . '</p>';

                        echo $video['video_description'];
                        ?>
                    </div>
                </div>
                <div class="text-container">
                    <?php
                    require('Comments.php');
                    ?>
                </div>
            </div>

    <?php
        } else {

            echo "Video not found.";
        }
    } else {
        echo "Video ID not provided.";
    }

    ?>


    <script>
        var video = document.getElementById("myVideo");
        var hasWatched30Percent = false;
        var videoId = <?php echo $videoId; ?>;
        var accountId = <?php echo $accounts[0]['id']; ?>;

        function updateProgress() {
            // deze functie checked of je 30 procent van de video hebt gezien en add dan een view
            var currentTime = video.currentTime;
            var duration = video.duration;

            var percentageWatched = (currentTime / duration) * 100;

            if (percentageWatched >= 30 && !hasWatched30Percent) {
                hasWatched30Percent = true;
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "addView.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send("videoId=" + videoId + "&accountId=" + accountId);
            }
        }

        function likeVideo(videoId, accountId) {
            // redirect naar like.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "like.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("videoId=" + videoId + "&accountId=" + accountId);
        }

        function dislikeVideo(videoId, accountId) {
            // redirect naar dislike.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "dislike.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("videoId=" + videoId + "&accountId=" + accountId);
        }
    </script>
</body>

</html>