<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Css/Main.css">
    <link rel="stylesheet" href="Css/Header.css">
    <link rel="stylesheet" href="Css/DefaultVideoGrid.css">
    <link rel="stylesheet" href="Css/AccountSettings.css">
    <link rel="stylesheet" href="Css/VideoCreator.css">
</head>

<body>
    <?php
    session_start();
    require("Requires/Connection.php");
    require("Requires/Header.php");
    require("Requires/Search.php");
    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        $searchValue = $_GET["search"];
        var_dump($searchValue);
    } else {
        require("Requires/DefaultVideoGrid.php");
    }
?>

</body>

</html>