<div class="MediaGrid">
    <?php
    $sqlVideos = "SELECT v.id, v.account_id, v.video_name, v.views, v.thumbnail_image, v.created_at, a.username, a.profile_picture 
                  FROM videos v 
                  JOIN accounts a ON v.account_id = a.id 
                  ORDER BY v.created_at DESC";
    $videos = $pdo->query($sqlVideos);

    if ($videos->rowCount() > 0) {
        while ($video = $videos->fetch()) {
            // blob decoderen
            $thumbnail_img = "data:image/png;base64," . base64_encode($video['thumbnail_image']);
            $account_img = "data:image/png;base64," . base64_encode($video['profile_picture']);
            $account_id = $video['account_id'];
            $creatorName = $video['username'];
    ?>
            <div class="GridItem" onclick="redirectToVideo(<?php echo $video['id']; ?>)">
                <div class="ItemThumbnail" style="background-image:url('<?php echo $thumbnail_img; ?>');"></div>
                <div class="Itemlayout">
                    <?php
                    echo '<div class="ProfilePicture" onclick="redirectToChannel(' . $account_id . ')" style="background-image: url(\'' . $account_img . '\');"></div>';
                    ?>
                    <div class="ItemInfoLayout">
                        <?php
                        echo '<div class="Vid_Name" >' . $video['video_name'] . '</div>';
                        echo '<div class="creator_name">' . $creatorName . '</div>';

                        // Calculate and display the time difference
                        $createdDateTime = new DateTime($video['created_at']);
                        $currentDateTime = new DateTime();
                        $timeDifference = $currentDateTime->diff($createdDateTime);

                        echo '<p class="Item_view_time">';
                        echo $video['views'] . ' views ' . ' • ';

                        if ($timeDifference->days > 0) {
                            echo $timeDifference->days . ' day';
                            if ($timeDifference->days > 1) {
                                echo 's';
                            }
                            echo ' ago';
                        } elseif ($timeDifference->h > 0) {
                            echo $timeDifference->h . ' hour';
                            if ($timeDifference->h > 1) {
                                echo 's';
                            }
                            echo ' ago';
                        } else {
                            echo $timeDifference->i . ' minute';
                            if ($timeDifference->i > 1) {
                                echo 's';
                            }
                            echo ' ago';
                        }

                        echo '</p>';
                        ?>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        echo "No results found.";
    }
    ?>
</div>

<script>
    function redirectToVideo(videoId) {
        window.location.href = "Video.php?id=" + videoId;
    }

    function redirectToChannel(accountId) {
        event.stopPropagation();
        window.location.href = "Account.php?id=" + accountId;
    }
</script>