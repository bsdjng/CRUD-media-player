<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Main.css">
    <link rel="stylesheet" href="Header.css">
</head>

<body>
    <?php
    require("Connection.php");
    require("Header.php");
    require("Search.php");
    ?>
    <div class="MediaGrid">
        <?php

        $sqlVideos = "SELECT * FROM videos";
        $videos = $pdo->query($sqlVideos);

        $sqlAccounts = "SELECT * FROM accounts";
        $accountsResult = $pdo->query($sqlAccounts);

        // pleur het in een array
        $accounts = $accountsResult->fetchAll(PDO::FETCH_ASSOC);

        if ($videos->rowCount() > 0) {
            while ($video = $videos->fetch()) {
                if (isset($video)) {
        ?><ol class="GridItem">
                        <li class="thumbnail">
                            <?php
                            // blob decoderen
                            $imageSrc = "data:image/png;base64," . base64_encode($video['thumbnail_image']);
                            // laat alle variabelen zien
                            echo '<img src="' . $imageSrc . '" alt="Image">';
                            ?>
                        </li>
                        <li>
                            <?php
                            echo $video['video_name'];
                            ?>
                        </li>
                        <li>
                            <?php // zoek de uploader van de video
                            foreach ($accounts as $account) {
                                if ($account['id'] == $video['account_id']) {
                                    // blob decoderen
                                    $imageSrc = "data:image/png;base64," . base64_encode($account['profile_picture']);
                                    // laat alle variabelen zien
                                    echo '<img class="PFP" src="' . $imageSrc . '" alt="Image">';
                                    echo $account['username'];
                                }
                            }
                            ?>
                        </li>
                        <li>
                            <ol class="GridItem2">
                                <li>
                                    <?php
                                    echo "views: " . $video['views'];
                                    ?>
                                </li>

                                <li>
                                    <?php
                                    echo "Date Posted: " .  $video['created_at'];
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
</body>

</html>