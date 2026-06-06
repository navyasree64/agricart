<?php
// Session management
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Authentication functions


function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserData($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Product management functions
function getFeaturedProducts($limit = 8) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.featured = 1 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getNewProducts($limit = 8) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBestsellers($limit = 8) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name, 
                            COALESCE(SUM(oi.quantity), 0) as total_sold 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          LEFT JOIN order_items oi ON p.id = oi.product_id 
                          GROUP BY p.id 
                          ORDER BY total_sold DESC 
                          LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        // Handle comma-separated additional images
        if (isset($product['additional_images']) && !empty($product['additional_images'])) {
            $product['additional_images'] = array_filter(
                array_map('trim', explode(',', $product['additional_images'])),
                function($img) { return !empty($img); }
            );
        } else {
            $product['additional_images'] = [];
        }
        
        // Handle specifications if needed
        $product['specifications'] = isset($product['specifications']) ? 
            (is_array($product['specifications']) ? $product['specifications'] : []) : [];
    }
    
    return $product;
}

function getProductsByCategory($categoryId, $page = 1, $perPage = 12, $sortBy = 'newest') {
    global $conn;
    
    $offset = ($page - 1) * $perPage;
    $orderClause = '';
    
    switch ($sortBy) {
        case 'price_low':
            $orderClause = 'ORDER BY p.price ASC';
            break;
        case 'price_high':
            $orderClause = 'ORDER BY p.price DESC';
            break;
        case 'popular':
            $orderClause = 'ORDER BY p.sales_count DESC';
            break;
        case 'newest':
        default:
            $orderClause = 'ORDER BY p.created_at DESC';
            break;
    }
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.category_id = ? 
              $orderClause 
              LIMIT ? OFFSET ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $categoryId, $perPage, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getRelatedProducts($category_id, $exclude_id, $limit = 4) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, price, image 
                          FROM products 
                          WHERE category_id = ? AND id != ? 
                          LIMIT ?");
    $stmt->bind_param("iii", $category_id, $exclude_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getProductReviews($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, u.name as user_name 
                          FROM product_reviews r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.product_id = ? 
                          ORDER BY r.date_added DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getProductRating($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(rating) as average, COUNT(*) as count 
                          FROM product_reviews 
                          WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return [
        'average' => round($result['average'] ?? 0, 1),
        'count' => $result['count'] ?? 0
    ];
}

function getProductAttributes($productId) {
    global $conn;
    $stmt = $conn->prepare("SELECT a.name, pa.value 
                          FROM product_attributes pa 
                          JOIN attributes a ON pa.attribute_id = a.id 
                          WHERE pa.product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    
    $attributes = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $attributes[$row['name']] = $row['value'];
    }
    
    return $attributes;
}

function getProductImages($productId) {
    global $conn;
    $stmt = $conn->prepare("SELECT image_path 
                          FROM product_images 
                          WHERE product_id = ? 
                          ORDER BY display_order");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    
    $images = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
    
    return $images;
}

// Category management functions
function getAllCategories() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetch_all(MYSQLI_ASSOC);
}

function getCategoryById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getCategoryBreadcrumb($categoryId) {
    global $conn;
    $breadcrumb = [];
    $current = getCategoryById($categoryId);
    
    if (!$current) {
        return $breadcrumb;
    }
    
    $breadcrumb[] = $current;
    
    while ($current && $current['parent_id']) {
        $current = getCategoryById($current['parent_id']);
        if ($current) {
            array_unshift($breadcrumb, $current);
        }
    }
    
    return $breadcrumb;
}

// Cart functions
function getCartItems() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }

    $cartItems = [];
    $ids = implode(',', array_keys($_SESSION['cart']));
    
    $result = $GLOBALS['conn']->query("SELECT p.*, c.name as category_name 
                                      FROM products p 
                                      LEFT JOIN categories c ON p.category_id = c.id 
                                      WHERE p.id IN ($ids)");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $pid = $row['id'];
            $quantity = $_SESSION['cart'][$pid];
            $price = isset($row['discount_price']) && $row['discount_price'] > 0 ? $row['discount_price'] : $row['price'];
            
            $cartItems[] = [
                'product' => $row,
                'quantity' => $quantity,
                'subtotal' => $quantity * $price
            ];
        }
    }
    
    return $cartItems;
}

function getCartTotals() {
    $cartItems = getCartItems();
    $subtotal = 0;
    
    foreach ($cartItems as $item) {
        $subtotal += $item['subtotal'];
    }
    
    $shipping = ($subtotal < 500) ? 50 : 0;
    $total = $subtotal + $shipping;
    
    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $total
    ];
}

function clearCart() {
    unset($_SESSION['cart']);
}

// Security functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
        return $input;
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Utility functions
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

// function formatDate($date, $format = 'M d, Y') {
//     return date($format, strtotime($date));
// }

function paginationLinks($currentPage, $totalPages, $baseUrl) {
    $links = '';
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $links .= '<div class="pagination">';
    
    if ($currentPage > 1) {
        $links .= '<a href="' . $baseUrl . '&page=' . ($currentPage - 1) . '" class="page-link">&laquo; Previous</a>';
    } else {
        $links .= '<span class="page-link disabled">&laquo; Previous</span>';
    }
    
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    if ($start > 1) {
        $links .= '<a href="' . $baseUrl . '&page=1" class="page-link">1</a>';
        if ($start > 2) {
            $links .= '<span class="page-link ellipsis">...</span>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            $links .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $links .= '<a href="' . $baseUrl . '&page=' . $i . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $links .= '<span class="page-link ellipsis">...</span>';
        }
        $links .= '<a href="' . $baseUrl . '&page=' . $totalPages . '" class="page-link">' . $totalPages . '</a>';
    }
    
    if ($currentPage < $totalPages) {
        $links .= '<a href="' . $baseUrl . '&page=' . ($currentPage + 1) . '" class="page-link">Next &raquo;</a>';
    } else {
        $links .= '<span class="page-link disabled">Next &raquo;</span>';
    }
    
    $links .= '</div>';
    
    return $links;
}

function searchProducts($query, $page = 1, $perPage = 12) {
    global $conn;
    
    $searchTerm = '%' . $query . '%';
    $offset = ($page - 1) * $perPage;
    
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.name LIKE ? OR p.description LIKE ? 
                          ORDER BY p.name 
                          LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $perPage, $offset);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function countSearchResults($query) {
    global $conn;
    
    $searchTerm = '%' . $query . '%';
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count 
                          FROM products 
                          WHERE name LIKE ? OR description LIKE ?");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}
function uploadProductImage($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        throw new Exception("Invalid file type");
    }
    
    $filename = uniqid() . '.' . $ext;
    $destination = '../assets/images/products/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to upload file");
    }
    
    return $filename;
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function getProductAdditionalImages($product_id) {
    global $conn;
    $product_id = (int)$product_id;
    
    $sql = "SELECT image_path FROM product_images WHERE product_id = ? ORDER BY display_order ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    return $images;
}


// User Address Functions
function getUserAddresses($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addUserAddress($userId, $addressData) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, full_name, phone, address_line1, address_line2, city, state, postal_code, is_default) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssi", 
        $userId,
        $addressData['full_name'],
        $addressData['phone'],
        $addressData['address_line1'],
        $addressData['address_line2'],
        $addressData['city'],
        $addressData['state'],
        $addressData['postal_code'],
        $addressData['is_default']
    );
    return $stmt->execute();
}

function updateUserAddress($addressId, $userId, $addressData) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_addresses 
                          SET full_name = ?, phone = ?, address_line1 = ?, address_line2 = ?, 
                              city = ?, state = ?, postal_code = ?, is_default = ?
                          WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssssssii", 
        $addressData['full_name'],
        $addressData['phone'],
        $addressData['address_line1'],
        $addressData['address_line2'],
        $addressData['city'],
        $addressData['state'],
        $addressData['postal_code'],
        $addressData['is_default'],
        $addressId,
        $userId
    );
    return $stmt->execute();
}

function deleteUserAddress($addressId, $userId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $addressId, $userId);
    return $stmt->execute();
}

// Password Change Function
function changeUserPassword($userId, $currentPassword, $newPassword) {
    global $conn;
    
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!password_verify($currentPassword, $result['password'])) {
        return false;
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    return $stmt->execute();
}

// Order History Functions
function getUserOrders($userId) {
    global $conn;
    
    // First check if the tables exist
    $tableCheck = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($tableCheck->num_rows == 0) {
        return []; // Return empty array if tables don't exist
    }
    
    $stmt = $conn->prepare("
        SELECT o.*, 
               COUNT(oi.id) as item_count,
               SUM(oi.quantity * oi.price) as total_amount
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    
    if (!$stmt) {
        return []; // Return empty array if prepare fails
    }
    
    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        return []; // Return empty array if execute fails
    }
    
    $result = $stmt->get_result();
    $orders = [];
    
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    $stmt->close();
    return $orders;
}

function getOrderDetails($orderId, $userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT o.*, oi.*, p.name as product_name, p.image
                          FROM orders o
                          JOIN order_items oi ON o.id = oi.order_id
                          JOIN products p ON oi.product_id = p.id
                          WHERE o.id = ? AND o.user_id = ?");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Order Management Functions
function createOrder($userId, $addressId, $totalAmount, $paymentMethod = 'COD') {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, address_id, total_amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iids", $userId, $addressId, $totalAmount, $paymentMethod);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

function addOrderItem($orderId, $productId, $quantity, $price) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
    
    return $stmt->execute();
}

function getProductSpecifications($productId) {
    global $conn;
    $stmt = $conn->prepare("SELECT spec_name, spec_value FROM product_specifications WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
