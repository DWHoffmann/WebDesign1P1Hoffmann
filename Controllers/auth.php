<?php
require_once '../Models/database.php';
require_once '../logger.php';

function login($username, $password) {
    //echo $username;
    $user = getUserByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        logMessage("User logged in: $username");
        return true;
    }
    logMessage("Failed login attempt for username: $username");
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        unset($_SESSION['user_id']);
        logMessage("User logged out: ID $userId");
    }
}
?>
