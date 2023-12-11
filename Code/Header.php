<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'Connection.php';

$sqlAccounts = "SELECT profile_picture FROM accounts WHERE id = :accountId";
$accountsResult = $pdo->prepare($sqlAccounts);
$accountsResult->bindParam(':accountId', $_SESSION['account_id'], PDO::PARAM_INT);
$accountsResult->execute();

// Fetch the result into an associative array
$loggedInAccount = $accountsResult->fetch(PDO::FETCH_ASSOC);
?>
<ol class="Header">
    <li class="HeaderItem"><a id="HeaderHomeLink" href="main.php"></a></li>
    <li id="SearchBar">
        <form action="main.php" method="">
            <input id="SearchBarText" name="search" type="text" placeholder="Search">
            <input id="SearchBarsubmit" type="submit" value="">
        </form>
    </li>
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) : ?>
        <?php
        $imageSrc = "data:image/png;base64," . base64_encode($loggedInAccount['profile_picture']);
        ?>
        <li class="HeaderItem" id="ProfileDropdown" style="background-image: url('<?php echo $imageSrc; ?>')">
            <div class="Dropdown_menu">
                <a class="Dropdown_link" href="Account.php">
                    <p>go to your account</p>
                </a>
                <button class="Dropdown_link" onclick="openSettingDialog()">
                    <p>Change account settings</p>
                </button>
                <button class="Dropdown_link" onclick="openCreateVideoDialog()">
                    <p>Create a new video!</p>
                </button>
                <a class="Dropdown_link" href="Logout.php">
                    <p>LogOut</p>
                </a>
                <!-- ALS ER NIEUWE LINK MOET ZIEN VOEG DIE HIER TOE -->
            </div>
        </li>
    <?php else : ?>
        <li class="HeaderItem">
            <a id="HeaderUserLink" href="AccountLogin.php">log in</a>
        </li>
    <?php endif; ?>
</ol>