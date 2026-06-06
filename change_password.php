<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid request";
    header('Location: profile.php#password');
    exit;
}

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Basic validations
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['error'] = "All password fields are required.";
    header('Location: profile.php#password');
    exit;
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = "New password and confirmation do not match.";
    header('Location: profile.php#password');
    exit;
}

if (strlen($newPassword) < 6) {
    $_SESSION['error'] = "New password must be at least 6 characters long.";
    header('Location: profile.php#password');
    exit;
}

// Call change password function
$success = changeUserPassword($_SESSION['user_id'], $currentPassword, $newPassword);

if ($success) {
    $_SESSION['success'] = "Password changed successfully!";
} else {
    $_SESSION['error'] = "Incorrect current password.";
}

header('Location: profile.php#password');
exit;
