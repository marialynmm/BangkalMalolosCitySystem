<?php
session_start();
session_destroy(); // Clear the session
header("Location: ../Frontend/index.php"); // Redirect to the login page
exit();
?>
