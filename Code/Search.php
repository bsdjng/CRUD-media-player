<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if the 'search' field is empty
        if (empty($_POST['search'])) {
            header("Location: main.php");
        } else {
            // Process the form data
            $searchValue = $_POST['search'];
    
        header("Location: main.php?search=" . urlencode($searchValue));
        exit;
    }  }
?>