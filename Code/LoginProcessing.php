<?php
require("Connection.php");

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
