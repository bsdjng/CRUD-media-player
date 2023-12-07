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

    if (isset($_POST['newUsername']) && !empty($_POST['newUsername'])) {
        $newUsername = $_POST['newUsername'];
        $sqlUpdateUsername = "UPDATE accounts SET username = :new_username WHERE id = :account_id";
        $stmt = $pdo->prepare($sqlUpdateUsername);
        $stmt->bindParam(':new_username', $newUsername, PDO::PARAM_STR);
        $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Handle description update
    if (isset($_POST['newDescription']) && !empty($_POST['newDescription'])) {
        $newDescription = $_POST['newDescription'];
        $sqlUpdateDescription = "UPDATE accounts SET about_me = :new_description WHERE id = :account_id";
        $stmt = $pdo->prepare($sqlUpdateDescription);
        $stmt->bindParam(':new_description', $newDescription, PDO::PARAM_STR);
        $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Handle profile picture update
    if (isset($_FILES['newProfilePicture']) && $_FILES['newProfilePicture']['error'] == UPLOAD_ERR_OK) {
        $profilePictureData = file_get_contents($_FILES['newProfilePicture']['tmp_name']);
        $sqlUpdateProfilePicture = "UPDATE accounts SET profile_picture = :profile_picture WHERE id = :account_id";
        $stmt = $pdo->prepare($sqlUpdateProfilePicture);
        $stmt->bindParam(':profile_picture', $profilePictureData, PDO::PARAM_LOB);
        $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Handle banner update
    if (isset($_FILES['newBanner']) && $_FILES['newBanner']['error'] == UPLOAD_ERR_OK) {
        $bannerData = file_get_contents($_FILES['newBanner']['tmp_name']);
        $sqlUpdateBanner = "UPDATE accounts SET banner = :banner WHERE id = :account_id";
        $stmt = $pdo->prepare($sqlUpdateBanner);
        $stmt->bindParam(':banner', $bannerData, PDO::PARAM_LOB);
        $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

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
                <button onclick="toggleForm('usernameForm')">Edit username</button>
            </div>
            <div class="infoItem">
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture">
                <button onclick="toggleForm('profilePictureForm')">Edit profile picture</button>
            </div>
            <div class="infoItem">
                <img src="<?php echo $banner; ?>" alt="Banner">
                <button onclick="toggleForm('bannerForm')">Edit banner</button>
            </div>
            <div class="infoItem">
                <?php echo $account['about_me']; ?>
                <button onclick="toggleForm('descriptionForm')">Edit description</button>
            </div>
        </div>
    </div>

    <div id="usernameForm" class="editForm">
        <form method="post">
            <input type="text" placeholder="Username" name="newUsername">
            <input type="submit" value="submit">
        </form>
    </div>

    <div id="profilePictureForm" class="editForm">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="newProfilePicture" accept="image/*">
            <input type="submit" value="Submit">
        </form>
    </div>

    <div id="bannerForm" class="editForm">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="newBanner" accept="image/*">
            <input type="submit" value="Submit">
        </form>
    </div>

    <div id="descriptionForm" class="editForm">
        <form method="post">
            <input type="text" placeholder="Description" name="newDescription">
            <input type="submit" value="submit">
        </form>
    </div>

    <script>
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>