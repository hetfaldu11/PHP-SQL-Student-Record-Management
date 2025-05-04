<?php
session_start();
// Check if user is logged in (add your authentication logic)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
session_destroy();
header("Location: index.php");
exit;
?>
