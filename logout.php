<?php
session_start();

// Destroy all session data
$_SESSION = array();
session_destroy();

// Redirect to login page
header("Location: login.html");
exit();
?>