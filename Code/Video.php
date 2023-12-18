<!-- VIDEO.PHP -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Css/Header.css">
    <link rel="stylesheet" href="Css/VideoStyle.css">
    <link rel="stylesheet" href="Css/Comments.css">
    <link rel="stylesheet" href="Css/AccountSettings.css">
    <link rel="stylesheet" href="Css/VideoCreator.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>

    <?php
    require("Requires/Header.php");
    require("Requires/Connection.php");
    require("Requires/Search.php");

    // $ipAddress = $_SERVER['REMOTE_ADDR'];

    if (isset($_GET['id'])) {
        // Get video id from $_GET
        $videoId = $_GET['id'];

        // Query to retrieve video information with account details
        $sqlVideos = "SELECT v.id, v.account_id, v.video_name, v.views, v.video_description, v.created_at, a.username, a.profile_picture 
                      FROM videos v 
                      JOIN accounts a ON v.account_id = a.id 
                      WHERE v.id = :videoId";

        $videos = $pdo->prepare($sqlVideos);
        $videos->bindParam(':videoId', $videoId, PDO::PARAM_INT);
        $videos->execute();

        if ($videos->rowCount() > 0) {
            // Fetch video details
            $video = $videos->fetch(PDO::FETCH_ASSOC); ?>
            <!-- display the video information -->
            <div class="centerdiv">
                <div class="video-container">
                    <video id="myVideo" controls ontimeupdate="updateProgress()">
                        <source src="http://192.168.91.244/CRUD-media-player/Usercontent/<?php echo $video['account_id']; ?>/<?php echo $videoId . '.mp4' ?>" type="video/mp4">
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
                        <div id="PFP_NAME" onclick="redirectToChannel('<?php echo $video['account_id']; ?>')">
                            <?php
                            $imageSrc = "data:image/png;base64," . base64_encode($video['profile_picture']);
                            echo '<div class="PFP" style="background-image: url(\'' . $imageSrc . '\');"></div>';
                            echo '<p class="creator_name">' . $video['username'] . '</p>';
                            ?>
                        </div>
                        <div class="Like_dislike_btn">
                            <?php
                            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                                $sqlCheckLike = "SELECT dislike FROM likes WHERE video_id = :videoId AND account_id = :accountId";
                                $checkLike = $pdo->prepare($sqlCheckLike);
                                $checkLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                                $checkLike->bindParam(':accountId', $_SESSION['account_id'], PDO::PARAM_INT);
                                $checkLike->execute();
                                $userLiked = $checkLike->fetchColumn();

                                $sqlLikeCount = "SELECT COUNT(*) FROM likes WHERE video_id = :videoId AND dislike = 0";
                                $likeCountStmt = $pdo->prepare($sqlLikeCount);
                                $likeCountStmt->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                                $likeCountStmt->execute();
                                $likesCount = $likeCountStmt->fetchColumn();

                                $sqlDislikeCount = "SELECT COUNT(*) FROM likes WHERE video_id = :videoId AND dislike = 1";
                                $dislikeCountStmt = $pdo->prepare($sqlDislikeCount);
                                $dislikeCountStmt->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                                $dislikeCountStmt->execute();
                                $dislikesCount = $dislikeCountStmt->fetchColumn();
                            ?>
                                <button id="likeButton" onclick="toggleLikeStatus()">
                                    <?php echo $likesCount; ?>
                                </button>

                                <button id="dislikeButton" onclick="toggleDislikeStatus()">
                                    <?php echo $dislikesCount; ?>
                                </button>
                            <?php
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
                    require('Requires/Comments.php');
                    ?>
                </div>
            </div>

    <?php
        } else {
            // Video not found
            echo "Video not found.";
        }
    } else {
        // Video ID not provided
        echo "Video ID not provided.";
    }


    ?>
    <script>
    var video = document.getElementById("myVideo");
    var hasWatched30Percent = false;
    var viewAdded = false;
    var videoId = <?php echo isset($videoId) ? json_encode($videoId) : 'null'; ?>;
    var accountId = <?php echo isset($_SESSION['account_id']) ? json_encode($_SESSION['account_id']) : 'null'; ?>;
    
    function updateProgress() {
        if (hasWatched30Percent && !viewAdded) {
            jQuery.ajax({
                type: 'POST',
                url: 'processing.php',
                data: {
                    action: 'addView',
                    videoId: videoId,
                },
                success: function(response) {
                    console.log("updateProgress() called");
                    viewAdded = true;
                },
                error: function(error) {
                    console.error('AJAX request failed: ' + error.statusText);
                }
            });
        }
    }

    video.addEventListener('timeupdate', function() {
        var percentWatched = (video.currentTime / video.duration) * 100;

        if (percentWatched >= 30 && !hasWatched30Percent) {
            hasWatched30Percent = true;
            updateProgress();
        }
    });

    let userLiked = <?php echo isset($userLiked) ? json_encode($userLiked) : 'null'; ?>;

    let isLiked = false;
    let isDisliked = false;

    function toggleLikeStatus() {
        isLiked = !isLiked;
        isDisliked = false;
        console.log(isLiked, isDisliked);
    }

    function toggleDislikeStatus() {
        isDisliked = !isDisliked;
        isLiked = false;
        console.log(isLiked, isDisliked);
    }

    function handleUnload() {
        console.log('isLiked:', isLiked);
        console.log('isDisliked:', isDisliked);
        console.log('userLiked:', userLiked);

        if (isLiked) {
            if (userLiked === null) {
                console.log('add_like');
                sendAjaxRequest('handle_like', 'add_like');
            } else if (userLiked === false) {
                console.log('remove_like');
                sendAjaxRequest('handle_like', 'remove_like');
            } else if (userLiked === true) {
                console.log('remove_dislike_add_like');
                sendAjaxRequest('handle_like', 'remove_dislike_add_like');
            }
        } else if (isDisliked) {
            if (userLiked === null) {
                console.log('add_dislike');
                sendAjaxRequest('handle_like', 'add_dislike');
            } else if (userLiked === true) {
                console.log('remove_dislike');
                sendAjaxRequest('handle_like', 'remove_dislike');
            } else if (userLiked === false) {
                console.log('remove_like_add_dislike');
                sendAjaxRequest('handle_like', 'remove_like_add_dislike');
            }
        } else {
            console.log('does not edit anything, or error!');
        }
    }

    function sendAjaxRequest(action, likeStatus) {
        // Use AJAX to send a request to the server
        jQuery.ajax({
            type: 'POST',
            url: 'processing.php',
            data: {
                action: 'handle_like',
                videoId: videoId,
                accountId: accountId,
                likeStatus: likeStatus,
            },
            success: function (response) {
                console.log(response);
                // You can handle the response or additional logic here
            },
            error: function (error) {
                console.error('AJAX request failed: ' + error.statusText);
            }
        });
    }


    window.addEventListener('beforeunload', handleUnload);

    function redirectToChannel(accountId) {
        event.stopPropagation();
        window.location.href = "Account.php?id=" + accountId;
    }
</script>

</body>

</html>