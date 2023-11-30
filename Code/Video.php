<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="VideoStyle.css">
</head>

<body>
    <?php
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

        //count likes
        $sqlLikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 0";
        $likesResult = $pdo->query($sqlLikes);
        $likes = $likesResult->fetchAll(PDO::FETCH_ASSOC);

        //count dislikes
        $sqlDislikes = "SELECT COUNT(*) FROM likes WHERE video_id = " . $videoId . " AND dislike = 1";
        $DislikesResult = $pdo->query($sqlDislikes);
        $dislikes = $DislikesResult->fetchAll(PDO::FETCH_ASSOC);

        //search account that posted the video
        if ($videos->rowCount() > 0) {
            $video = $videos->fetch(PDO::FETCH_ASSOC);
            $sqlAccounts = "SELECT id, username, profile_picture FROM accounts WHERE id = " . $video['account_id'];
            $accountsResult = $pdo->query($sqlAccounts);
            $accounts = $accountsResult->fetchAll(PDO::FETCH_ASSOC);
    ?>
            <div class="centerdiv">
                <div class="video-container">
                    <video controls>
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
                            <button id="likeButton" onclick="likeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                                <?php echo $likes[0]['COUNT(*)']; ?>
                            </button>

                            <button id="dislikeButton" onclick="dislikeVideo(<?php echo $videoId; ?>, <?php echo $accounts[0]['id']; ?>)">
                                <?php echo $dislikes[0]['COUNT(*)']; ?>
                            </button>

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