<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("Requires/Connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST);
    // Check if the action is set in the POST data
    if (isset($_POST["action"])) {
        $action = $_POST["action"];

        switch ($action) {
            case "register":
                // Handle user registration
                handleRegistration();
                break;

            case "login":
                // Handle user login
                handleLogin();
                break;

            case "search":
                // Handle user Search
                handleSearch();
                break;

            case "ChangeCreatorSettings":
                // Handle adding a comment
                ChangeCreatorSettings();
                break;

            case "upload_video":
                // Handle video upload
                handleVideoUpload();
                break;
                
            case "add_comment":
                // Handle adding a comment
                handleAddComment();
                break;

            case "handle_like":
                handle_like();
                break;

            case "addView":
                addView($_POST['videoId']);
                break;

            case "deleteAccount":
                deleteAccount($_SESSION['account_id']);
                break;

            default:
                echo "Invalid action.";
        }
    } else {
        echo "Action not provided.";
    }
} else {
    echo "Invalid request method.";
}

function handleRegistration()
{
    global $pdo;
    // Handle user registration logic here
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST["password"];
    $repeat_password = $_POST["repeat_password"];

    // Check if fields are not empty
    if (!empty($username) && !empty($email) && !empty($password) && !empty($repeat_password)) {
        // Check if passwords match
        if ($password !== $repeat_password) {
            echo "Passwords do not match.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $date_Now = (new DateTime())->format('Y-m-d H:i:s');

            // Assuming $profile_pictureSrc is the file path on the server
            $profile_pictureSrc = '../images/user.png';

            // Read the file content
            $profile_picture_content = file_get_contents($profile_pictureSrc);

            // Insert data into the 'Accounts' table
            $sql = "INSERT INTO Accounts (username, email, password, created_at, profile_picture) VALUES (:username, :email, :hashed_password, :date_Now, :profile_picture)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':hashed_password', $hashed_password);
            $stmt->bindParam(':date_Now', $date_Now);
            $stmt->bindParam(':profile_picture', $profile_picture_content, PDO::PARAM_LOB);

            $stmt->execute();

            // Get the ID of the newly inserted record
            $account_id = $pdo->lastInsertId();

            // Start the session
            session_start();

            // Set session variables
            $_SESSION['account_id'] = $account_id;
            $_SESSION['logged_in'] = true;

            header('Location: Main.php');
            exit();
        }
    } else {
        echo "All fields are required.";
    }
}

function handleVideoUpload()
{
    global $pdo;
    // Handle video upload logic here

    // gebruiker kan niet vaker uploaden binnen 10 minuten
    $minTimeBetweenUploads = 600;
    if (isset($_SESSION['last_upload_time']) && time() - $_SESSION['last_upload_time'] < $minTimeBetweenUploads) {
        echo "Error: Please wait at least 10 minutes before uploading another video.";
        exit();
    }

    $videoName = sanitize($_POST['videoName']);
    $videoThumbnailFile = $_FILES['videoThumbnail'];
    $videoFile = $_FILES['video'];
    $videoDescription = sanitize($_POST['videoDescription']);
    $currentDate = (new DateTime())->format('Y-m-d H:i:s');

    // process the files uit de post
    $videoThumbnailData = file_get_contents($videoThumbnailFile['tmp_name']);
    $videoData = file_get_contents($videoFile['tmp_name']);
    $dir = "../Usercontent/" . $_SESSION['account_id'];

    if (!file_exists($dir) || !is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // check files grootte
    $maxFileSize = 50 * 1024 * 1024; // 50 MB in bytes
    if ($_FILES['video']['size'] > $maxFileSize) {
        echo "Error: The video file size exceeds the maximum limit of 50 MB.";
        exit();
    }

    $sqlVideoInsert = "INSERT INTO videos (account_id, video_name, video_description, views, thumbnail_image, created_at) VALUES (:account_id, :video_name, :video_description, 0, :thumbnail_image, :created_at)";
    $stmtVideoInsert = $pdo->prepare($sqlVideoInsert);
    $stmtVideoInsert->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
    $stmtVideoInsert->bindParam(':video_name', $videoName, PDO::PARAM_STR);
    $stmtVideoInsert->bindParam(':video_description', $videoDescription, PDO::PARAM_STR);
    $stmtVideoInsert->bindParam(':thumbnail_image', $videoThumbnailData, PDO::PARAM_LOB);
    $stmtVideoInsert->bindParam(':created_at', $currentDate, PDO::PARAM_STR);

    if ($stmtVideoInsert->execute()) {
        // Update the last upload time
        $_SESSION['last_upload_time'] = time();

        // Get the last inserted ID
        $lastInsertedID = $pdo->lastInsertId();

        // Rename the video file
        $newFileName = $lastInsertedID . '.mp4'; // Change the file extension based on the actual file type
        $destination = $dir . '/' . $newFileName;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($_FILES['video']['tmp_name'], $destination)) {
            echo "File uploaded successfully!";
        } else {
            echo "Error uploading file.";
        }

        Header("Location: Account.php?=" . $_SESSION['account_id']);
        exit();
    } else {
        echo "Error inserting video information: " . print_r($stmtVideoInsert->errorInfo(), true);
    }
}

function handleLogin()
{
    global $pdo;
    // Handle user login logic here
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //check of de post variabelen bestaan
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];

            //zoek de user op basis van email
            $sql = "SELECT id, email, password FROM Accounts WHERE email = :email";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            //activeert als de email in de database staat
            if ($statement->rowCount() > 0) {
                $account = $statement->fetch(PDO::FETCH_ASSOC);

                // checkt het wachtwoord
                if (password_verify($password, $account['password'])) {
                    session_start();
                    $_SESSION['account_id'] = $account['id'];
                    $_SESSION['logged_in'] = true;
                    header("Location: main.php");
                    exit();
                } else {
                    echo "Incorrect password. Please try again.";
                }
            } else {
                echo "Account not found. Please check your email or sign up.";
            }
        } else {
            echo "Email or password not provided.";
        }
    } else {
        echo "Invalid request method.";
    }
}

function handleSearch()
{
    if (empty($_POST['searchQuery'])) {
        header("Location: main.php");
    } else {
        $searchValue = $_POST['searchQuery'];
        header("Location: main.php?search=" . urlencode($searchValue));
        exit;
    }
}

function handleAddComment()
{
    global $pdo;
    if (!empty($_POST['newCommentText'])) {
        $videoId = $_POST['videoId'];
        $commenterID = $_SESSION['account_id'];
        $commentText = sanitize($_POST['newCommentText']);
        $date_Now = (new DateTime())->format('Y-m-d H:i:s');

        $sqlAddComment = "INSERT INTO comments (video_id, account_id, comment_text, created_at) VALUES (:video_id, :account_id, :comment_text, :created_at)";
        $stmtAddComment = $pdo->prepare($sqlAddComment);
        $stmtAddComment->bindParam(':video_id', $videoId, PDO::PARAM_INT);
        $stmtAddComment->bindParam(':account_id', $commenterID, PDO::PARAM_INT);
        $stmtAddComment->bindParam(':comment_text', $commentText, PDO::PARAM_STR); // <-- Corrected the variable name
        $stmtAddComment->bindParam(':created_at', $date_Now, PDO::PARAM_STR);

        $stmtAddComment->execute();

        header('Location: Video.php?id=' . $videoId);
        exit();
    } else {
        echo 'Something went wrong<br>please try again later';
    }
}

function ChangeCreatorSettings()
{
    global $pdo;
    $newUsername = isset($_POST['newUsername']) ? htmlspecialchars($_POST['newUsername']) : '';
    $newDescription = isset($_POST['newDescription']) ? htmlspecialchars($_POST['newDescription']) : '';

    // Update username if provided
    if (!empty($newUsername)) {
        $sqlUpdateUsername = "UPDATE accounts SET username = :new_username WHERE id = :account_id";
        $stmt = $pdo->prepare($sqlUpdateUsername);
        $stmt->bindParam(':new_username', $newUsername, PDO::PARAM_STR);
        $stmt->bindParam(':account_id', $_SESSION['account_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Update description if provided
    if (!empty($newDescription)) {
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

    // Redirect or send a response back as needed
    header("Location: main.php");
    exit();
}

function handle_like()
{
    global $pdo;

    $videoId = $_POST['videoId'];
    $accountId = $_POST['accountId'];
    $likeStatus = $_POST['likeStatus'];

    // Check if the user has liked or disliked the video
    $sqlCheckLike = "SELECT dislike FROM likes WHERE video_id = :videoId AND account_id = :accountId";
    $checkLike = $pdo->prepare($sqlCheckLike);
    $checkLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
    $checkLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $checkLike->execute();
    $userLiked = $checkLike->fetchColumn();

    $response = array();

        switch ($likeStatus) {
            case 'Like_status':
                if($userLiked === false){
                    // add_like
                    $sqlInsert = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 0)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtInsert->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Like added successfully';

                }else if($userLiked === 0){
                    // remove_like
                    $sqlDelete = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 0";
                    $stmtDelete = $pdo->prepare($sqlDelete);
                    $stmtDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtDelete->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtDelete->execute();

                    
                    $response['status'] = 'success';
                    $response['message'] = 'Like removed successfully';

                }else if($userLiked === 1){
                    // remove_dislike_add_like
                    $sqlRemoveDislike = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 1";
                    $stmtRemoveDislike = $pdo->prepare($sqlRemoveDislike);
                    $stmtRemoveDislike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtRemoveDislike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtRemoveDislike->execute();
        
                    $sqlAddLike = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 0)";
                    $stmtAddLike = $pdo->prepare($sqlAddLike);
                    $stmtAddLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtAddLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtAddLike->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Dislike removed and like added successfully';
                }else{
                    $response['status'] = 'error';
                    $response['message'] = 'Invalid like status';
                }
                break;
            case 'Dislike_status':
                if($userLiked === false){
                    // add_dislike
                    $sqlInsert = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 1)";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtInsert->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Dislike added successfully';

                }else if($userLiked === 1){
                    // remove_dislike
                    $sqlDelete = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 1";
                    $stmtDelete = $pdo->prepare($sqlDelete);
                    $stmtDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtDelete->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtDelete->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Dislike removed successfully';
                    
                }else if($userLiked === 0){
                    // remove_like_add_dislike
                    $sqlRemoveLike = "DELETE FROM likes WHERE account_id = :accountId AND video_id = :videoId AND dislike = 0";
                    $stmtRemoveLike = $pdo->prepare($sqlRemoveLike);
                    $stmtRemoveLike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtRemoveLike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtRemoveLike->execute();
        
                    $sqlAddDislike = "INSERT INTO likes (account_id, video_id, dislike) VALUES (:accountId, :videoId, 1)";
                    $stmtAddDislike = $pdo->prepare($sqlAddDislike);
                    $stmtAddDislike->bindParam(':accountId', $accountId, PDO::PARAM_INT);
                    $stmtAddDislike->bindParam(':videoId', $videoId, PDO::PARAM_INT);
                    $stmtAddDislike->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Like removed and dislike added successfully';
                }else{
                    $response['status'] = 'error';
                    $response['message'] = 'Invalid dislike status';
                }
                break;
            default:
                echo'invalid like status';
                $response['status'] = 'error';
                $response['message'] = 'Default invalid like status';
            break;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
}

function addView($videoId)
{
    global $pdo;
    var_dump($_POST);
    $sqlUpdateViews = "UPDATE videos SET views = views + 1 WHERE id = :videoId";
    $updateViews = $pdo->prepare($sqlUpdateViews);
    $updateViews->bindParam(':videoId', $videoId, PDO::PARAM_INT);
    $updateViews->execute();
}

function deleteAccount($accountId)
{
    global $pdo;

    $sqlVideoDelete = "DELETE FROM videos WHERE account_id = :accountId";
    $stmtVideoDelete = $pdo->prepare($sqlVideoDelete);
    $stmtVideoDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $stmtVideoDelete->execute();

    $sqlCommentDelete = "DELETE FROM comments WHERE account_id = :accountId";
    $stmtCommentDelete = $pdo->prepare($sqlCommentDelete);
    $stmtCommentDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $stmtCommentDelete->execute();

    $sqlLikeDelete = "DELETE FROM likes WHERE account_id = :accountId";
    $stmtLikeDelete = $pdo->prepare($sqlLikeDelete);
    $stmtLikeDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $stmtLikeDelete->execute();

    $sqlAccountDelete = "DELETE FROM accounts WHERE id = :accountId";
    $stmtAccountDelete = $pdo->prepare($sqlAccountDelete);
    $stmtAccountDelete->bindParam(':accountId', $accountId, PDO::PARAM_INT);
    $stmtAccountDelete->execute();

    require('logout.php');
}

function sanitize($dirty)
{
    $clean = htmlspecialchars($dirty, ENT_QUOTES, 'UTF-8');
    return $clean;
}
