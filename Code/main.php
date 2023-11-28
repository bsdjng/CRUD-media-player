<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Main.css">
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="DefaultVideoGrid.css">
</head>

<body>
    <?php
    require("Connection.php");
    require("Header.php");
    require("Search.php");
    if(empty($_GET["search"]) == false){
        var_dump($_GET["search"]);
    }else {
        require("DefaultVideoGrid.php");
    }
    ?>
</body>

</html>