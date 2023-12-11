<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="AccountLogin.css">
</head>

<body>
    <div id="CenterDiv">
        <div id="div1">
            <h1>login page</h1>
            <h2>please log into a account</h2>
        </div>
        <div id="div2">
            <form action="LoginProcessing.php" method="post" id="form1">
                <input type="hidden" name="action" value="login">
                <input type="email" name="email" placeholder="email" id="email" required><br>
                <input type="password" name="password" placeholder="Password" id="password" required>
            </form>
            <div id="div3">
                <a href="CreateAccount.php"><button class="Button">Sign Up Here</button></a>
                <button class="Button" type="submit" form="form1">log in</button>
            </div>
        </div>
    </div>
</body>

</html>