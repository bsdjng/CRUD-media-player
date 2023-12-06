<?php

require("Connection.php");
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
?>
