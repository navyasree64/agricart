<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting product: " . $stmt->error;
    }
    header("Location: productadmin.php");
    exit;
}

// Handle product addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Print POST data
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));
    
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    
    // Debug: Log the values
    error_log("Product Values - Name: $name, Price: $price, Category ID: $category_id, Stock: $stock");
    
    // Validate required fields
    if (empty($name) || empty($description) || empty($price) || empty($category_id)) {
        $_SESSION['error'] = "Please fill in all required fields";
        error_log("Validation Error: Required fields are empty");
        header("Location: productadmin.php");
        exit;
    }
    
    // Check if category exists
    $check_category = $conn->prepare("SELECT id, name FROM categories WHERE id = ?");
    $check_category->bind_param("i", $category_id);
    $check_category->execute();
    $category_result = $check_category->get_result();
    
    if ($category_result->num_rows === 0) {
        error_log("Error: Category ID $category_id does not exist");
        $_SESSION['error'] = "Selected category does not exist";
        header("Location: productadmin.php");
        exit;
    }
    
    $category = $category_result->fetch_assoc();
    error_log("Category found: " . $category['name']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "assets/images/products/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                error_log("Failed to create directory: $target_dir");
                $_SESSION['error'] = "Failed to create image directory";
                header("Location: productadmin.php");
                exit;
            }
        }
        
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is valid
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            error_log("Invalid image file");
            $_SESSION['error'] = "File is not an image.";
            header("Location: productadmin.php");
            exit;
        }
        
        // Check file size (2MB max)
        if ($_FILES["image"]["size"] > 2000000) {
            $_SESSION['error'] = "Sorry, your file is too large.";
            header("Location: productadmin.php");
            exit;
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $_SESSION['error'] = "Sorry, only JPG, JPEG & PNG files are allowed.";
            header("Location: productadmin.php");
            exit;
        }
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            error_log("Failed to move uploaded file to: $target_file");
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: productadmin.php");
            exit;
        }
        
        error_log("Image uploaded successfully: $target_file");
    }
    
    try {
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            // Update existing product
            $product_id = (int)$_POST['product_id'];
            $sql = "UPDATE products SET name=?, description=?, price=?, category_id=?, stock=?, discount_price=?" . 
                   ($image ? ", image=?" : "") . " WHERE id=?";
            $stmt = $conn->prepare($sql);
            
            if ($image) {
                $stmt->bind_param("ssdiiisi", $name, $description, $price, $category_id, $stock, $discount_price, $image, $product_id);
            } else {
                $stmt->bind_param("ssdiiii", $name, $description, $price, $category_id, $stock, $discount_price, $product_id);
            }
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Product updated successfully!";
                error_log("Product updated successfully - ID: $product_id");
            } else {
                throw new Exception("Error updating product: " . $stmt->error);
            }
        } else {
            // Add new product
            $sql = "INSERT INTO products (name, description, price, category_id, stock, discount_price, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdiiis", $name, $description, $price, $category_id, $stock, $discount_price, $image);
            
            if ($stmt->execute()) {
                $new_product_id = $stmt->insert_id;
                $_SESSION['success'] = "Product added successfully!";
                error_log("Product added successfully - ID: $new_product_id");
                
                // Verify the product was added
                $verify = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $verify->bind_param("i", $new_product_id);
                $verify->execute();
                $result = $verify->get_result();
                
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    error_log("Verified product in database: " . print_r($product, true));
                } else {
                    error_log("ERROR: Product not found in database after insertion - ID: $new_product_id");
                }
    } else {
                throw new Exception("Error adding product: " . $stmt->error);
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log("Database Error: " . $e->getMessage());
    }
    
    header("Location: productadmin.php");
    exit;
}

// Fetch all products
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$products = $conn->query($sql);

// Fetch all categories for dropdown
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

// Debug: Check if categories query was successful
if (!$categories) {
    error_log("Categories query failed: " . $conn->error);
    $_SESSION['error'] = "Error loading categories: " . $conn->error;
}

// Debug: Check number of categories
$category_count = $categories ? $categories->num_rows : 0;
error_log("Number of categories found: " . $category_count);

// Include admin header
include('admin_header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include('includes/admin_sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Manage Products</h1>
            <button class="btn btn-primary" onclick="showAddProductForm()">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="notification success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="notification error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add/Edit Product Form -->
        <div id="productForm" class="form-container" style="display: none;">
            <h2 id="formTitle">Add New Product</h2>
            <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="product_id" id="product_id">
                
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (₹)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="discount_price">Discount Price (₹)</label>
                        <input type="number" id="discount_price" name="discount_price" step="0.01">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                            <?php while($category = $categories->fetch_assoc()): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endwhile; ?>
            </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Max file size: 2MB. Allowed formats: JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <button type="button" class="btn btn-secondary" onclick="hideProductForm()">Cancel</button>
                </div>
        </form>
    </div>

    <!-- Products Table -->
        <div class="table-responsive">
            <table class="data-table">
        <thead>
            <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="product-thumbnail">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['category_name']) ?></td>
                                <td>
                                    <?php if ($product['discount_price']): ?>
                                        <span class="original-price">₹<?= number_format($product['price'], 2) ?></span>
                                        <span class="discount-price">₹<?= number_format($product['discount_price'], 2) ?></span>
                                    <?php else: ?>
                                        ₹<?= number_format($product['price'], 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= $product['stock'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-edit" onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-results">No products found.</td>
                        </tr>
                    <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</div>

<style>
/* Admin Container */
.admin-container {
    display: flex;
    min-height: 100vh;
}

.admin-sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    padding: 20px 0;
}

.admin-content {
    flex: 1;
    padding: 20px;
    background: #f5f6fa;
}

/* Header */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Form Styles */
.form-container {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.admin-form {
    max-width: 800px;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
}

input[type="text"],
input[type="number"],
textarea,
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

input[type="file"] {
    padding: 10px 0;
}

small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

/* Table Styles */
.table-responsive {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.data-table tr:hover {
    background: #f8f9fa;
}

/* Product Thumbnail */
.product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

/* Price Styles */
.original-price {
    text-decoration: line-through;
    color: #999;
    margin-right: 10px;
}

.discount-price {
    color: #e74c3c;
    font-weight: 600;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn i {
    margin-right: 5px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.btn-edit {
    background: #f39c12;
    color: white;
}

.btn-delete {
    background: #e74c3c;
    color: white;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 30px;
    color: #666;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        width: 100%;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<script>
function showAddProductForm() {
    document.getElementById('formTitle').textContent = 'Add New Product';
    document.getElementById('product_id').value = '';
    document.getElementById('productForm').style.display = 'block';
    document.querySelector('form').reset();
}

function hideProductForm() {
    document.getElementById('productForm').style.display = 'none';
}

function editProduct(product) {
    document.getElementById('formTitle').textContent = 'Edit Product';
    document.getElementById('product_id').value = product.id;
    document.getElementById('name').value = product.name;
    document.getElementById('description').value = product.description;
    document.getElementById('price').value = product.price;
    document.getElementById('discount_price').value = product.discount_price;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('stock').value = product.stock;
    document.getElementById('productForm').style.display = 'block';
}
</script>

<?php include('admin_footer.php'); ?>
