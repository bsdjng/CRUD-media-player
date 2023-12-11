<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Main.css">
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="DefaultVideoGrid.css">
    <link rel="stylesheet" href="AccountSettings.css">
</head>

<body>
    <?php
    session_start();
    require("Connection.php");
    require("Header.php");
    require("Search.php");
    require("AccountSettings.php");
    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        $searchValue = $_GET["search"];
        var_dump($searchValue);
    } else {
        require("DefaultVideoGrid.php");
    }
?>

</body>

</html>