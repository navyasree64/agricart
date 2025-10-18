<?php
session_start();
require_once 'functions.php';
require_once 'db.php'; // Add database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid request";
    header('Location: profile.php');
    exit;
}

// Get form data
$addressData = [
    'full_name' => $_POST['full_name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'address_line1' => $_POST['address_line1'] ?? '',
    'address_line2' => $_POST['address_line2'] ?? '',
    'city' => $_POST['city'] ?? '',
    'state' => $_POST['state'] ?? '',
    'postal_code' => $_POST['postal_code'] ?? '',
    'is_default' => isset($_POST['is_default']) ? 1 : 0
];

// Validate required fields
$requiredFields = ['full_name', 'phone', 'address_line1', 'city', 'state', 'postal_code'];
foreach ($requiredFields as $field) {
    if (empty($addressData[$field])) {
        $_SESSION['error'] = "Please fill in all required fields";
        header('Location: profile.php#addresses');
        exit;
    }
}

// If this is a new default address, unset other default addresses
if ($addressData['is_default']) {
    $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

// Check if this is an edit or new address
if (!empty($_POST['address_id'])) {
    // Update existing address
    $success = updateUserAddress($_POST['address_id'], $_SESSION['user_id'], $addressData);
    $message = $success ? "Address updated successfully" : "Failed to update address";
} else {
    // Add new address
    $success = addUserAddress($_SESSION['user_id'], $addressData);
    $message = $success ? "Address added successfully" : "Failed to add address";
}

if ($success) {
    $_SESSION['success'] = $message;
} else {
    $_SESSION['error'] = $message;
}

header('Location: profile.php#addresses');
exit;
?> 