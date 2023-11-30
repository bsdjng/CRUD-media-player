<?php
// LoginProcessing.php

require("Connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if 'email' and 'password' keys exist in $_POST
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Add additional validation and sanitization as needed

        // Your SQL query to check if the account exists
        $sql = "SELECT id, email, password FROM Accounts WHERE email = :email";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            // account exists, check password
            $account = $statement->fetch(PDO::FETCH_ASSOC);

            // Verify password (you may use password_hash() during registration)
            if (password_verify($password, $account['password'])) {
                session_start();
                $_SESSION['account_id'] = $account['id'];
                $_SESSION['logged_in'] = true;
                header("Location: main.php");
                exit();
            } else {
                // Incorrect password
                echo "Incorrect password. Please try again.";
                var_dump($password, $account['password']);
            }
        } else {
            // account does not exist
            echo "Account not found. Please check your email or sign up.";
        }
    } else {
        // Handle case where 'email' or 'password' keys are not set
        echo "Email or password not provided.";
    }
} else {
    // Invalid request method
    echo "Invalid request method.";
}
