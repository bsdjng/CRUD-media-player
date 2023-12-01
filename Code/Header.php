<?php
// session_start();
// var_dump($_SESSION);
// ?>

<ol class="Header">
    <li class="HeaderItem"><a id="HeaderHomeLink" href="main.php"></a></li>
    <li id="SearchBar">
        <form action="main.php" method="post">
            <input id="SearchBarText" name="search" type="text" placeholder="Search">
            <input id="SearchBarsubmit" type="submit" value="">
        </form>
    </li>
    <li class="HeaderItem"><a id="HeaderUserLink" href="
    <?php
    if (isset($_SESSION['logged_in'])) {
        if ($_SESSION['logged_in'] == true) {
            echo "Account.php";
        } else {
            echo "AccountLogin.php";
        }
    } else {
        echo "AccountLogin.php";
    }
    ?>"></a></li>

</ol>