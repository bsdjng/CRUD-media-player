<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'Connection.php';
require("AccountSettings.php");
require("VideoCreator.php");

$sqlAccounts = "SELECT username, profile_picture FROM accounts WHERE id = :accountId";
$accountsResult = $pdo->prepare($sqlAccounts);
$accountsResult->bindParam(':accountId', $_SESSION['account_id'], PDO::PARAM_INT);
$accountsResult->execute();

// Fetch the result into an associative array
$loggedInAccount = $accountsResult->fetch(PDO::FETCH_ASSOC);
?>
<ol class="Header">
    <li class="HeaderItem"><a id="HeaderHomeLink" href="main.php"></a></li>
    <li id="SearchBar">
        <form action="processing.php" method="post">
            <input type="hidden" name="action" value="search">
            <input id="SearchBarText" name="searchQuery" type="text" placeholder="Search">
            <input id="SearchBarsubmit" type="submit" value="">
        </form>
    </li>
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) : ?>
        <?php
        $imageSrc = "data:image/png;base64," . base64_encode($loggedInAccount['profile_picture']);
        ?>
        <li class="HeaderItem" id="ProfileDropdown" style="background-image: url('<?php echo $imageSrc; ?>')">
            <input type="checkbox" id="ProfileDropdownCheckBox">
            <div class="Dropdown_menu">
                <a href="Account.php"><button id="DropdownButtenSplit" class="Dropdown_link"><?php echo $loggedInAccount['username'] ?><br>
                        <p id="view">View your Channel</p>
                    </button></a>
                <button class="Dropdown_link" onclick="openSettingDialog()">
                    Change account settings
                </button>
                <button class="Dropdown_link" id="DropdownButtenSplit" onclick="openCreateVideoDialog()">
                    Create a new video!
                </button>
                <a href="Logout.php"><button class="Dropdown_link">LogOut</button></a>
                <!-- ALS ER NIEUWE LINK MOET ZIEN VOEG DIE HIER TOE -->
            </div>
        </li>
    <?php else : ?>
        <li class="HeaderItem">
            <a id="HeaderUserLink" href="Login.php">log in</a>
        </li>
    <?php endif; ?>
</ol>