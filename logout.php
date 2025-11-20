<?php
require_once 'config/session.php';

// Logout user and redirect to login page
logoutUser();
header("Location: login.php?message=logged_out");
exit();
?>

