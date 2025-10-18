<?php
// Database connection
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'agri_ecommerce';
    $username = 'root';
    $password = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// User authentication
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Order functions
function getOrderDetails($orderId) {
    $conn = getDbConnection();
    try {
        // First try to get order with address
        $stmt = $conn->prepare("
            SELECT o.*, a.address_line1, a.address_line2, a.city, a.state, a.postal_code, a.country 
            FROM orders o 
            LEFT JOIN addresses a ON o.address_id = a.id 
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$orderId, getCurrentUserId()]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            // If no order found, return null
            return null;
        }
        
        // If address fields are null, set them to empty strings
        $order['address_line1'] = $order['address_line1'] ?? '';
        $order['address_line2'] = $order['address_line2'] ?? '';
        $order['city'] = $order['city'] ?? '';
        $order['state'] = $order['state'] ?? '';
        $order['postal_code'] = $order['postal_code'] ?? '';
        $order['country'] = $order['country'] ?? '';
        
        return $order;
    } catch (PDOException $e) {
        // If there's an error (like table doesn't exist), try to get just the order
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, getCurrentUserId()]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            // Add empty address fields
            $order['address_line1'] = '';
            $order['address_line2'] = '';
            $order['city'] = '';
            $order['state'] = '';
            $order['postal_code'] = '';
            $order['country'] = '';
        }
        
        return $order;
    }
}

function getOrderItems($orderId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cart functions
function getCartItems() {
    if (!isset($_SESSION['cart'])) {
        return [];
    }
    
    $conn = getDbConnection();
    $productIds = array_keys($_SESSION['cart']);
    if (empty($productIds)) {
        return [];
    }
    
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $cartItems = [];
    foreach ($products as $product) {
        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $_SESSION['cart'][$product['id']],
            'image' => $product['image']
        ];
    }
    
    return $cartItems;
}

function getCartTotal() {
    $cartItems = getCartItems();
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Product functions
function getProduct($productId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductsByCategory($categoryId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Category functions
function getCategories() {
    $conn = getDbConnection();
    $stmt = $conn->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategory($categoryId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserOrders($userId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 