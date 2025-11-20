<?php
require_once 'config/session.php';

// Redirect based on login status
if (isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>

