<?php

require("Connection.php");

?>
<script>
    console.log('working dude')
</script>
<?php

if (isset($_POST['videoId'])) {
    $videoId = $_POST['videoId'];

    // voegt 1 view toe
    $sqlUpdateViews = "UPDATE videos SET views = views + 1 WHERE id = :videoId";
    $stmtUpdateViews = $pdo->prepare($sqlUpdateViews);
    $stmtUpdateViews->bindParam(':videoId', $videoId, PDO::PARAM_INT);
    $stmtUpdateViews->execute();
} else { ?>
    <script>
        console.log('not working dude');
    </script>
<?php
}
