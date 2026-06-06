<?php
// cart_process.php - Process cart actions via AJAX
session_start();
include('db.php');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prepare response
$response = ['success' => false, 'message' => '', 'cartCount' => 0];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please <a href="login.php" style="color: #ffc107; text-decoration: underline; font-weight: bold;">log in</a> to add items to the cart.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

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
            
            // Database update
            $user_id = (int)$_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            }
            $stmt->execute();
            
            // Sync session cart with database
            $stmt = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartItems = [];
            while ($row = $result->fetch_assoc()) {
                $cartItems[$row['product_id']] = $row['quantity'];
            }
            $_SESSION['cart'] = $cartItems;
            
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
