<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to submit a review.";
    header("Location: login.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: home.php");
    exit();
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';

if (!$productId || $rating < 1 || $rating > 5 || empty($review)) {
    $_SESSION['error'] = "Please provide a valid rating and review text.";
    header("Location: product.php?id=" . $productId);
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Anonymous';

// Insert the review
$stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, user_name, rating, review) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisis", $productId, $userId, $userName, $rating, $review);

if ($stmt->execute()) {
    $_SESSION['success'] = "Thank you! Your review has been submitted.";
} else {
    $_SESSION['error'] = "Failed to submit review. Please try again.";
}

header("Location: product.php?id=" . $productId . "#reviews");
exit();
?>
