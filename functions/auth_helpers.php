<?php
// File: functions/auth_helpers.php

// Bắt đầu session ở đây
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

function getRole() {
    return $_SESSION['role'] ?? 'guest';
}

function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}

function getName() {
    return $_SESSION['name'] ?? 'Guest';
}

function getUsername() {
    return $_SESSION['username'] ?? 'Guest';
}
?>