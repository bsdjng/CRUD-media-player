<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="AccountLogin.css">
</head>
<body>
<div class="AccountCenterDiv">
        <form action="login.php" method="post">
            <ul class="Formlist">
                <li class="ListItem">
                    <input class="inputField" type="email" name="email" placeholder="Email*" required>
                </li>
                <li class="ListItem">
                    <input type="password" class="inputField" name="password" id="Password" placeholder="Password*" required>
                    <input type="checkbox" class="showPasswordCheckbox" onclick="myFunction()">
                </li>
                <li class="ListItem">
                    <input type="submit" value="Login">
                </li>
            </ul>
        </form>
        <a href=""><h3>Create account</h3></a>
        <a href=""><h3>Having Trouble Signing in?</h3></a>
    </div>
    


    <script>
        function myFunction() {
            var x = document.getElementById("Password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        } 
    </script>
</body>
</html>
