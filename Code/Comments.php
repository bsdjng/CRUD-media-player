<?php
// zoek alle comments die bij de video horen
$sqlComments = "SELECT id, account_id, comment_text, created_at FROM comments WHERE video_id = :videoId";
$stmtComments = $pdo->prepare($sqlComments);
$stmtComments->bindParam(':videoId', $videoId, PDO::PARAM_INT);
$stmtComments->execute();
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);


?>

<div id="commentsection">
    <form action="" method="post" id="commentSubmitForm">
        <input type="textfield" placeholder="Share your comment!" id="newCommentText"><br>
        <input type="submit" value="Submit" id="commentSubmitButton">
    </form>
    <?php
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
            <div class="commentPFP">
                <?php
                $imageSrc = "data:image/png;base64," . base64_encode($commentUser[0]['profile_picture']);
                echo '<div class="commentPFPimg" style="background-image: url(\'' . $imageSrc . '\');"></div>';
                ?>
            </div>
            <div class="commentDetails">
                <div class="commentUserame">
                    <?php echo $commentUser[0]['username']; ?>
                </div>
                <div class="commentText">
                    <?php echo $comment['comment_text']; ?>
                </div>
                <div class="commentDate">
                    <?php echo $comment['created_at']; ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>