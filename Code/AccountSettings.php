<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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

<!-- <button onclick="openDialog()">Open Account Settings</button> -->
    <dialog id="accountSettingsDialog">
    <button id="closeBtn" onclick="closeDialog()">cancel</button>

        <form id="UpdateAccountform" action="main.php" method="post">
            <li class="accountFormLi">
                <p class="liText">change username:</p>
                <input type="text" class="FormText" placeholder="<?php echo $account['username']; ?>">
            </li>
           
            <li class="accountFormLi">
                <p class="liText">Change profile picture:</p>
                <div id="profilePicturePreview" class="image-preview">
                    <img id="previewProfilePicture" src="<?php echo $profilePicture; ?>" alt="Preview">
                </div>
                <input type="file" name="newProfilePicture" accept="image/*" onchange="previewImage(this, 'previewProfilePicture')">
            </li>
            <li class="accountFormLi">
                <p class="liText">Change Banner:</p>
                <div id="bannerPreview" class="image-preview">
                    <img id="previewBanner" src="<?php echo $banner; ?>" alt="You don't have a banner!">
                </div>
                <input type="file" name="newBanner" accept="image/*" onchange="previewImage(this, 'previewBanner')">
            </li>

            <li class="accountFormLi">
                <p class="liText">change description:</p>
                <input type="text" class="FormText" placeholder="<?php echo !empty($account['about_me']) ? $account['about_me'] : 'write about yourself'; ?>" name="newDescription">
            </li>

            <li class="accountFormLi">
                <input class="btns" type="submit" value="Update account">
            </li>
        </form>

    </dialog>

    <script>
        function openDialog() {
            var dialog = document.getElementById('accountSettingsDialog');
            dialog.showModal();
        }

        function closeDialog() {
            var dialog = document.getElementById('accountSettingsDialog');
            dialog.close();
            clearFormInputs();
        }

        function toggleForm(formId) {
            var form = document.getElementById(formId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
        
        function clearFormInputs() {
        var form = document.getElementById('UpdateAccountform');
        var inputs = form.getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
            // Check if the input type is not 'submit' or 'button'
            if (inputs[i].type !== 'submit' && inputs[i].type !== 'button') {
                inputs[i].value = '';
            }
        }

        // Reset image previews to default
        document.getElementById('previewProfilePicture').src = "<?php echo $profilePicture; ?>";
        document.getElementById('previewBanner').src = "<?php echo $banner; ?>";
    }
    function previewImage(input, previewId) {
        var preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the preview
                preview.alt = "<?php echo $profilePicture; ?>"; // Set alt attribute
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none'; // Hide the preview
            preview.alt = ''; // Clear alt attribute
        }
    }
    </script>