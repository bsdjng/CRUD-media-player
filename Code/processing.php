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

        // Insert data into the 'Accounts' table
        $sql = "INSERT INTO Accounts (username, email, password, created_at) VALUES (:username, :email, :hashed_password, :date_Now)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashed_password, $date_Now]);

        header('Location: Main.php');
    }
} else {
    echo "All fields are required.";
}
