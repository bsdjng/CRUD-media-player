<?php
// zoek alle comments die bij de video horen
$sqlComments = "SELECT id, account_id, comment_text, created_at FROM comments WHERE video_id = :videoId ORDER BY created_at DESC";
$stmtComments = $pdo->prepare($sqlComments);
$stmtComments->bindParam(':videoId', $videoId, PDO::PARAM_INT);
$stmtComments->execute();
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
?>
    <div id="commentsection">
        <form action="processing.php" method="post" id="commentSubmitForm">
            <input type="hidden" name="action" value="add_comment">
            <?php
            $InputFormPFPsrc = "data:image/png;base64," . base64_encode($loggedInAccount['profile_picture']);
            ?>
            <img id="InputFormAccountIMG" src="<?php echo $InputFormPFPsrc; ?>" alt="profile img">
            <input type="hidden" value="<?php echo $video['id']; ?>" name="videoId">
            <div id="TextInput_SubmitBtn">
                <input type="text" placeholder="Share your comment!" name="newCommentText" id="newCommentText" autocomplete="off" required>
                <input type="submit" value="Submit" id="commentSubmitButton">
            </div>
        </form>
    </div>
<?php

} else {
    echo "Log-in to comment on this video!";
}
foreach ($comments as $comment) {
    // zoek de accountinformatie voor elke comment
    $sqlAccountComments = "SELECT id, username, profile_picture FROM accounts WHERE id = :account_id";
    $stmtAccountComments = $pdo->prepare($sqlAccountComments);
    $newId = $comment['account_id'];
    $stmtAccountComments->bindParam(':account_id', $newId, PDO::PARAM_INT);
    $stmtAccountComments->execute();
    $commentUser = $stmtAccountComments->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="comment">
        <div class="commentPFP" onclick="redirectToChannel('<?php echo $commentUser[0]['id']; ?>')">
            <?php
            $imageSrc = "data:image/png;base64," . base64_encode($commentUser[0]['profile_picture']);
            echo '<div class="commentPFPimg" style="background-image: url(\'' . $imageSrc . '\');"></div>';
            ?>
        </div>
        <div class="commentDetails">
            <div class="commentUserame">
                <?php echo '@' . $commentUser[0]['username']; ?>
            </div>
            <div class="commentText">
                <?php echo $comment['comment_text']; ?>
            </div>
            <div class="commentDate">
                <?php
                $createdDateTime = new DateTime($comment['created_at']);
                $currentDateTime = new DateTime();
                $timeDifference = $currentDateTime->diff($createdDateTime);
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
                ?>
            </div>
        </div>
    </div>
<?php
}
?>
</div>