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
    <link rel="stylesheet" href="AccountSettings.css">
</head>

<body>
    <?php
    require 'Connection.php';

    $sqlAccounts = "SELECT id, username, profile_picture, banner, about_me FROM accounts WHERE id = :account_id";
    $stmt = $pdo->prepare($sqlAccounts);
    $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
    $stmt->execute();
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    $profilePicture = "data:image/png;base64," . base64_encode($account['profile_picture']);
    $banner = "data:image/png;base64," . base64_encode($account['banner']);

    ?>
    <div class="centerDiv">
        <div class="accountInfo">
            <div class="infoItem">
                <?php echo $account['username']; ?>
            </div>
            <div class="infoItem">
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture">
            </div>
            <div class="infoItem">
                <img src="<?php echo $banner; ?>" alt="Banner">
            </div>
            <div class="infoItem">
                <?php echo $account['about_me']; ?>
            </div>
        </div>
    </div>
</body>