<?php
session_start();
require_once '../Models/database.php';
require_once 'auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../Views/login.php");
    exit();
}

// Get user's current story progress
$userId = $_SESSION['user_id'];
$progress = getUserProgress($userId);
print_r($progress);
echo "!!!!!!!!!!!!!!!!!!!!!";
if ($progress === false) {
    logMessage("Error: No Progress Found. Sorry.");
}
elseif ($progress == null) {
    logMessage("error is definetly not null at all /j");
}
else {
    // Redirect to story.php with current progress
    header("Location: story.php?step=" . $progress['current_step']);
}
exit();
?>
