<div class="MediaGrid">
    <?php
    $sqlVideos = "SELECT id, account_id, video_name, views, likes, dislikes, thumbnail_image, created_at FROM videos";
    $videos = $pdo->query($sqlVideos);

    $sqlAccounts = "SELECT username, id, profile_picture FROM accounts";
    $accountsResult = $pdo->query($sqlAccounts);

    // pleur het in een array
    $accounts = $accountsResult->fetchAll(PDO::FETCH_ASSOC);

    if ($videos->rowCount() > 0) {
        while ($video = $videos->fetch()) {
            if (isset($video)) {
                // blob decoderen
                $thumbnail_img = "data:image/png;base64," . base64_encode($video['thumbnail_image']);
                $account_img = "";

                foreach ($accounts as $account) {
                    if ($account['id'] == $video['account_id']) {
                        // blob decoderen
                        $account_img = "data:image/png;base64," . base64_encode($account['profile_picture']);
                        $creatorName = $account['username'];
                        // laat alle variabelen zien
                        // echo $account['username'];
                    }
                }
    ?>
                <div class="GridItem" onclick="redirectToVideo(<?php echo $video['id']; ?>)">
                    <div class="ItemThumbnail" style="background-image:url('<?php echo $thumbnail_img; ?>');"></div>
                    <div class="Itemlayout"><?php
                                            echo '<div class="ProfilePicture" style="background-image: url(\'' . $account_img . '\');"></div>'; ?>
                        <div class="ItemInfoLayout">
                            <?php
                            echo '<div class="Vid_Name">' . $video['video_name'] . '</div>';
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

            } else {
                echo "No data found for this record.<br>";
            }
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
</script>