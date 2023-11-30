<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
    <link rel="stylesheet" href="CreateAccount.css">
</head>
<body>
    <?php
        require("Connection.php")
    ?>
    <div id="center">
    <form action="processing.php" method="post" id="form1">
        <input type="text" id="username" name="username" placeholder="Your username" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="password" id="repeat_password" name="repeat_password" placeholder="Repeat password" required>
        <input type="submit" id="submit" value="Create Account">
    </form>       
    </div>
</body>
</html>
