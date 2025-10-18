<?php
// Start session and check admin permissions
session_start();
require_once '../db.php';
require_once '../functions.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Get form data
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle main image upload
    $main_image = 'default-product.jpg';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $main_image = uploadProductImage($_FILES['main_image']);
    }

    // Handle additional images
    $additional_images = [];
    if (!empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                $additional_images[] = uploadProductImage([
                    'name' => $_FILES['additional_images']['name'][$key],
                    'type' => $_FILES['additional_images']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['additional_images']['error'][$key],
                    'size' => $_FILES['additional_images']['size'][$key]
                ]);
            }
        }
    }
    
    // Convert array to comma-separated string for database
    $additional_images_str = implode(',', $additional_images);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products 
                          (name, description, price, category_id, stock, featured, image, additional_images) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiiss", 
        $name, 
        $description, 
        $price, 
        $category_id, 
        $stock, 
        $featured, 
        $main_image, 
        $additional_images_str
    );
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
        header("Location: products.php");
        exit();
    } else {
        $error = "Error adding product: " . $conn->error;
    }
}

// Include your header and form HTML
include 'admin_header.php';
?>

<!-- HTML form for adding products -->
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
    
    <div class="form-group">
        <label>Product Name</label>
        <input type="text" name="name" required>
    </div>
    
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" required></textarea>
    </div>
    
    <div class="form-group">
        <label>Price</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" required>
            <?php foreach (getAllCategories() as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Stock Quantity</label>
        <input type="number" name="stock" required>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" name="featured"> Featured Product
        </label>
    </div>
    
    <div class="form-group">
        <label>Main Image</label>
        <input type="file" name="main_image" accept="image/*" required>
    </div>
    
    <div class="form-group">
        <label>Additional Images</label>
        <input type="file" name="additional_images[]" multiple accept="image/*">
    </div>
    
    <button type="submit">Add Product</button>
</form>

<?php include 'admin_footer.php'; ?>