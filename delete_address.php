<?php
session_start();
require_once 'functions.php';
require_once 'db.php'; // Add database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if address ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid request";
    header('Location: profile.php#addresses');
    exit;
}

$addressId = (int)$_GET['id'];

// Delete the address
$success = deleteUserAddress($addressId, $_SESSION['user_id']);

if ($success) {
    $_SESSION['success'] = "Address deleted successfully";
} else {
    $_SESSION['error'] = "Failed to delete address";
}

header('Location: profile.php#addresses');
exit;
?> 