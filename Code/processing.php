<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("Connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            

            default:
                echo "Invalid action.";
        }
    } else {
        echo "Action not provided.";
    }
} else {
    echo "Invalid request method.";
}

function handleRegistration() {
    global $pdo;
    // Handle user registration logic here
    $username = $_POST["username"];
    $email = $_POST["email"];
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

            header('Location: Main.php');
            exit();
        }
    } else {
        echo "All fields are required.";
    }
}

function handleVideoUpload() {
    global $pdo;
    // Handle video upload logic here
    
    // gebruiker kan niet vaker uploaden binnen 10 minuten
    $minTimeBetweenUploads = 600;
    if (isset($_SESSION['last_upload_time']) && time() - $_SESSION['last_upload_time'] < $minTimeBetweenUploads) {
        echo "Error: Please wait at least 10 minutes before uploading another video.";
        exit();
    }

    $videoName = $_POST['videoName'];
    $videoThumbnailFile = $_FILES['videoThumbnail'];
    $videoFile = $_FILES['video'];
    $videoDescription = $_POST['videoDescription'];
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

    $sqlVideoInsert = "INSERT INTO videos (account_id, video_name, video_description, views, likes, dislikes, thumbnail_image, created_at) VALUES (:account_id, :video_name, :video_description, 0, 0, 0, :thumbnail_image, :created_at)";
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

function handleLogin() {
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

function handleAddComment() {
    global $pdo;
    // Handle adding a comment logic here
    $videoId = $_POST['videoId'];
    $commenterID = $_SESSION['account_id'];
    $commentText = $_POST['newCommentText'];
    $commentText = htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8');
    $date_Now = (new DateTime())->format('Y-m-d H:i:s');

    // Sanitize the comment text to prevent HTML, CSS, or JavaScript injection
    $commentText = htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8');

    $sqlAddComment = "INSERT INTO comments (video_id, account_id, comment_text, created_at) VALUES (:video_id, :account_id, :comment_text, :created_at)";
    $stmtAddComment = $pdo->prepare($sqlAddComment);
    $stmtAddComment->bindParam(':video_id', $videoId, PDO::PARAM_INT);
    $stmtAddComment->bindParam(':account_id', $commenterID, PDO::PARAM_INT);
    $stmtAddComment->bindParam(':comment_text', $commentText, PDO::PARAM_STR);
    $stmtAddComment->bindParam(':created_at', $date_Now, PDO::PARAM_STR);

    $stmtAddComment->execute();

    header('Location: Video.php?id=' . $videoId);
    exit();
}

function ChangeCreatorSettings(){
    global $pdo;
}
?>
