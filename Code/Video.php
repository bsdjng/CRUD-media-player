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
                    <video id="myVideo" controls ontimeupdate="updateProgress()">
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
                            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                                // Query om te kijken of het geliked of gedisliked is
                                $sqlCheckLike = "SELECT dislike FROM likes WHERE video_id = :videoId AND account_id = :accountId";
                                $checkLike = $pdo->prepare($sqlCheckLike);
                                $checkLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                                $checkLike->bindParam(':accountId', $_SESSION['account_id'], PDO::PARAM_INT);
                                $checkLike->execute();
                                $userLiked = $checkLike->fetchColumn();

                                if ($userLiked === 0) {
                                    // is al geliked
                                    $likeStatus = "liked";
                            ?>
                                    <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'remove_like')">
                                        <?php echo $likes[0]['COUNT(*)']; ?>
                                    </button>

                                    <button id="dislikeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'remove_like_add_dislike')">
                                        <?php echo $dislikes[0]['COUNT(*)']; ?>
                                    </button>
                                <?php
                                } elseif ($userLiked == 1) {
                                    // is al gedisliked
                                ?>
                                    <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'remove_dislike_add_like')">
                                        <?php echo $likes[0]['COUNT(*)']; ?>
                                    </button>

                                    <button id="dislikeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'remove_dislike')">
                                        <?php echo $dislikes[0]['COUNT(*)']; ?>
                                    </button>
                                <?php
                                } else {
                                    // is nog niet geliked of gedisliked
                                ?>
                                    <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'add_like')">
                                        <?php echo $likes[0]['COUNT(*)']; ?>
                                    </button>

                                    <button id="dislikeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $_SESSION['account_id']; ?>, 'add_dislike')">
                                        <?php echo $dislikes[0]['COUNT(*)']; ?>
                                    </button>
                            <?php
                                }
                            } else {
                                echo "Log-in to like or dislike this video!";
                            }
                            ?>

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
        var accountId = <?php echo $_SESSION['account_id']; ?>;

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

        function likeVideo(videoId, accountId, likeStatus) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "like.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // reload pagina
                    location.reload();
                }
            };
            // Redirect naar like.php
            xhr.send("videoId=" + videoId + "&accountId=" + accountId + "&likeStatus=" + likeStatus);
        }
    </script>
</body>

</html>