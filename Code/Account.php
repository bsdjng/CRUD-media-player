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
    <link rel="stylesheet" href="Css/Main.css">
    <link rel="stylesheet" href="Css/Header.css">
    <link rel="stylesheet" href="Css/DefaultVideoGrid.css">
    <link rel="stylesheet" href="Css/AccountSettings.css">
    <link rel="stylesheet" href="Css/VideoCreator.css">

</head>

<body>
    <?php
    require 'Requires/Header.php';
    require 'Requires/Connection.php';

    if (isset($_GET['id'])) {
        $idParam = $_GET['id'];
    } else {
        $idParam = $_SESSION['account_id'];
    }

    // pak informatie van het ingelogde account
    $sqlCurrentAccount = "SELECT id, username, created_at, profile_picture, banner, about_me FROM accounts WHERE id = :account_id";
    $stmtCurrentAccount = $pdo->prepare($sqlCurrentAccount);
    $stmtCurrentAccount->bindParam(':account_id', $idParam, PDO::PARAM_INT);
    $stmtCurrentAccount->execute();
    $currentAccount = $stmtCurrentAccount->fetch(PDO::FETCH_ASSOC);

    // pak alle videos van de ingelogde user
    $sqlUserVideos = "SELECT id, video_name, views, thumbnail_image, created_at FROM videos WHERE account_id = :account_id ORDER BY created_at DESC";
    $stmtUserVideos = $pdo->prepare($sqlUserVideos);
    $stmtUserVideos->bindParam(':account_id', $idParam, PDO::PARAM_INT);
    $stmtUserVideos->execute();
    $userVideos = $stmtUserVideos->fetchAll(PDO::FETCH_ASSOC);
    if (empty($currentAccount['banner'])) {
        $defaultBannerPath = "../images/defaultBanner.png";
        $banner = "data:image/png;base64," . base64_encode(file_get_contents($defaultBannerPath));
    } else {
        $banner = "data:image/png;base64," . base64_encode($currentAccount['banner']);
    }

    ?>
    <div class="AccountInformation">
        <img class="Banner" src="<?php echo $banner; ?>" alt="Banner"></img>
        <div class="Creator">
            <?php
            $pfp = "data:image/png;base64," . base64_encode($currentAccount['profile_picture']);
            echo '<img class="CurrentAccountPFP" src="' . $pfp . '"></img>';
            ?>
            <div class="CurrentCreatorInfo">
                <div class="CurrentCreatorName">
                    <?php
                    echo $currentAccount['username'];
                    ?>
                </div>
                <div>
                    <?php
                    echo count($userVideos) . " videos";
                    ?>
                </div>
                <div>
                    <?php
                    if (empty($currentAccount['about_me'])) {
                        echo "This channel does not have a description yet.";
                    } else {
                        echo $currentAccount['about_me'];
                    }
                    ?>
                </div>
                <div>
                    <?php
                    $joinDate = new DateTime($currentAccount['created_at']);
                    $formattedJoinDate = $joinDate->format("M d, Y");
                    echo $currentAccount['username'] . ' has been a communist since: ' . $formattedJoinDate;
                    ?>
                </div>
            </div>
        </div>
        <?php
        if (count($userVideos) > 0) {
        ?>
            <div class="MediaGrid">
                <?php
                foreach ($userVideos as $userVideo) {
                    $thumbnail_img = "data:image/png;base64," . base64_encode($userVideo['thumbnail_image']);
                    $account_img = "data:image/png;base64," . base64_encode($currentAccount['profile_picture']);
                ?>
                    <div class="GridItem" onclick="redirectToVideo(<?php echo $userVideo['id']; ?>)">

                        <div class="ItemThumbnail" style="background-image:url('<?php echo $thumbnail_img; ?>');"></div>
                        <div class="Itemlayout"><?php
                                                echo '<div class="ProfilePicture" style="background-image: url(\'' . $account_img . '\');"></div>'; ?>
                            <div class="ItemInfoLayout">
                                <?php
                                echo '<div class="Vid_Name">' . $userVideo['video_name'] . '</div>';
                                echo '<div class="creator_name">' . $currentAccount['username'] . '</div>';

                                // Calculate and display the time difference
                                $createdDateTime = new DateTime($userVideo['created_at']);
                                $currentDateTime = new DateTime();
                                $timeDifference = $currentDateTime->diff($createdDateTime);

                                echo '<p class="Item_view_time">';
                                echo $userVideo['views'] . ' views ' . ' • ';

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
                                ?></div>
                        </div>
                    </div>
                <?php
                } ?>
            </div>
        <?php
        } else {
            echo "You have not posted a video yet...";
        }
        ?>
    </div>
    </div>
    <script>
        function redirectToVideo(videoId) {
            window.location.href = "Video.php?id=" + videoId;
        }
    </script>
</body>

</html>