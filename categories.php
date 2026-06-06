<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Check if category has products
    $check_products = $conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id = ?");
    $check_products->bind_param("i", $category_id);
    $check_products->execute();
    $result = $check_products->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['product_count'] > 0) {
        $_SESSION['error'] = "Cannot delete category with existing products. Please reassign or delete the products first.";
    } else {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting category: " . $stmt->error;
        }
    }
    header("Location: categories.php");
    exit;
}

// Handle category addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Validate required fields
    if (empty($name)) {
        $_SESSION['error'] = "Category name is required";
        header("Location: categories.php");
        exit;
    }
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "assets/images/categories/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is valid
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
            header("Location: categories.php");
            exit;
        }
        
        // Check file size (2MB max)
        if ($_FILES["image"]["size"] > 2000000) {
            $_SESSION['error'] = "Sorry, your file is too large.";
            header("Location: categories.php");
            exit;
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: categories.php");
            exit;
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $image;
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: categories.php");
            exit;
        }
    }
    
    if (isset($_POST['category_id'])) {
        // Update existing category
        $category_id = (int)$_POST['category_id'];
        $sql = "UPDATE categories SET name = ?, description = ?";
        $params = [$name, $description];
        
        if (!empty($image)) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $category_id;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating category: " . $stmt->error;
        }
    } else {
        // Add new category
        $sql = "INSERT INTO categories (name, description, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $description, $image);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Error adding category: " . $stmt->error;
        }
    }
    
    header("Location: categories.php");
    exit;
}

// Get all categories with their product counts
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
        FROM categories c 
        ORDER BY c.name";
$categories = $conn->query($sql);

// Include admin header
include('admin_header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include('includes/admin_sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Manage Categories</h1>
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="showCategoryForm()">
                <i class="fas fa-plus"></i> Add New Category
            </button>
        </div>
        
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td>
                                <?php if (!empty($category['image'])): ?>
                                    <img src="assets/images/categories/<?php echo htmlspecialchars($category['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($category['name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-image" style="font-size: 24px; color: #ccc;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo $category['product_count']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-edit" onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this category?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Form Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="hideCategoryForm()">&times;</span>
        <h2 id="modalTitle">Add New Category</h2>
        <form id="categoryForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" id="category_id">
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Category Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <div id="imagePreview" style="margin-top: 10px;"></div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideCategoryForm()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 50%;
    max-width: 600px;
    position: relative;
}

.close {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

.btn-primary {
    background-color: #4a8f29;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-edit {
    background-color: #ffc107;
    color: #000;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.data-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.data-table img {
    border-radius: 4px;
}

.action-buttons {
    margin-bottom: 20px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
function showCategoryForm() {
    document.getElementById('categoryModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Add New Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id').value = '';
    document.getElementById('imagePreview').innerHTML = '';
}

function hideCategoryForm() {
    document.getElementById('categoryModal').style.display = 'none';
}

function editCategory(category) {
    document.getElementById('categoryModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('category_id').value = category.id;
    document.getElementById('name').value = category.name;
    document.getElementById('description').value = category.description;
    
    if (category.image) {
        document.getElementById('imagePreview').innerHTML = `
            <img src="assets/images/categories/${category.image}" 
                 alt="${category.name}" 
                 style="max-width: 100px; max-height: 100px;">
            <p>Current image</p>
        `;
    } else {
        document.getElementById('imagePreview').innerHTML = '';
    }
}

// Preview image before upload
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview" 
                     style="max-width: 100px; max-height: 100px;">
            `;
        }
        reader.readAsDataURL(file);
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('categoryModal');
    if (event.target == modal) {
        hideCategoryForm();
    }
}
</script>

<?php include('admin_footer.php'); ?> 