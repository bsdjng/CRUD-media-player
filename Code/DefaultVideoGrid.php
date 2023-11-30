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
    ?>
    <ol class="GridItem" onclick="redirectToVideo(<?php echo $video['id']; ?>)">
        <?php
            // blob decoderen
            $thumbnail_img = "data:image/png;base64," . base64_encode($video['thumbnail_image']);
            foreach ($accounts as $account) {
                if ($account['id'] == $video['account_id']) {
                    // blob decoderen
                    $account_img = "data:image/png;base64," . base64_encode($account['profile_picture']);
                    // laat alle variabelen zien
                    // echo $account['username'];

                }
            }
        ?>
        <li class="thumbnail" style="background-image:url('<?php echo $thumbnail_img; ?>') ;"></li>
        <li>
            <div class="PFP_Name"><?php
                echo '<div class="PFP" style="background-image: url(\'' . $account_img . '\');"></div>';
                echo '<p class="Vid_Name">' . $video['video_name'] . '</p>';?>
            </div>
        </li>
        <li>
            <?php
                echo '<p class="creator_name">' . $account['username'] . '</p>';
            ?>
        </li>
        <li>
            <ol class="View_Time">
                <li>
                    <?php
                    echo "views: " . $video['views'];
                    ?>
                </li>
                <li>
                    <?php
                    $createdDateTime = new DateTime($video['created_at']);
                    // huidige tijd
                    $currentDateTime = new DateTime();
                    // Tijdverschil berekenen
                    $timeDifference = $currentDateTime->diff($createdDateTime);
                    // Display the time difference
                    if ($timeDifference->days > 0) {
                        if ($timeDifference->days == 1) {
                            echo $timeDifference->days . ' day ago';
                        } else {
                            echo $timeDifference->days . ' days ago';
                        }
                    } elseif ($timeDifference->h > 0) {
                        if ($timeDifference->h == 1) {
                            echo $timeDifference->h . ' hours ago';
                        } else {
                            echo $timeDifference->h . ' hours ago';
                        }
                    } else {
                        echo $timeDifference->i . ' minutes ago';
                    }
                    ?>
                </li>
            </ol>
        </li>
    </ol>
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