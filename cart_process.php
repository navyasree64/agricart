<?php
// cart_process.php - Process cart actions via AJAX
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prepare response
$response = ['success' => false, 'message' => '', 'cartCount' => 0];

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add to cart action
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Minimum quantity check
            if ($quantity < 1) {
                $quantity = 1;
            }
            
            // Add or update cart item
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            // Set success response
            $response['success'] = true;
            $response['message'] = 'Product added to cart successfully';
            $response['cartCount'] = array_sum($_SESSION['cart']); // Total items in cart
        } else {
            $response['message'] = 'Invalid product ID';
        }
    } else {
        $response['message'] = 'Invalid action';
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
