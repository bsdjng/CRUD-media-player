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
                    <img id="passwordShide" src="../images/hidden.png" alt="show password" onclick="show_hide_pass()">
                </li>
                <li>
                    <input type="submit" value="Login">
                </li>
            </ul>
        </form>
        <a href=""><h3>Create account</h3></a>
        <a href=""><h3>Having Trouble Signing in?</h3></a>
    </div>
    


    <script>
        function show_hide_pass() {
            var x = document.getElementById("Password");
            var img = document.getElementById("passwordShide");
            if (x.type === "password") {
                x.type = "text";
                img.src = "../images/visible.png";
            } else {
                x.type = "password";
                img.src = "../images/hidden.png";
            }
        } 
    </script>
</body>
</html>
