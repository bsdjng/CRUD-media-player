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
        <form action="main.php" method="post">
            <input id="SearchBarText" name="search" type="text" placeholder="Search">
            <input id="SearchBarsubmit" type="submit" value="">
        </form>
    </li>

    <?php if (isset($_SESSION['logged_in'])) : ?>
        <?php if ($_SESSION['logged_in']) : ?>
            <?php
            $imageSrc = "data:image/png;base64," . base64_encode($loggedInAccount['profile_picture']);
            ?>
            <li class="HeaderItem">
                <a id="ChangedHeaderUserLink" href="<?php echo $_SESSION['logged_in'] ? 'Account.php' : 'AccountLogin.php'; ?>" style="background-image: url('<?php echo $imageSrc; ?>')"></a>
            </li>
        <?php else : ?>
            <li class="HeaderItem"><a id="HeaderUserLink" href="<?php echo $_SESSION['logged_in'] ? 'Account.php' : 'AccountLogin.php'; ?>"></a></li>
        <?php endif; ?>
    <?php else : ?>
        <li class="HeaderItem"><a id="HeaderUserLink" href="AccountLogin.php"></a></li>
    <?php endif; ?>
</ol>