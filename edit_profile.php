<?php
session_start();
require_once('db.php');
require_once('functions.php');

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get current user details
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF verification
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($email)) {
            $error = "Name and Email are required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // Check if email is already taken by another user
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkEmail->bind_param("si", $email, $userId);
            $checkEmail->execute();
            if ($checkEmail->get_result()->num_rows > 0) {
                $error = "This email is already in use by another account.";
            } else {
                // Update user details
                $update = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
                $update->bind_param("sssi", $name, $email, $phone, $userId);
                if ($update->execute()) {
                    $_SESSION['user_name'] = $name; // Update session name
                    $_SESSION['success'] = "Profile updated successfully!";
                    header("Location: profile.php");
                    exit;
                } else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$pageTitle = "Edit Profile";
include('header.php');
?>

<style>
.edit-profile-container {
    max-width: 600px;
    margin: 3rem auto;
    padding: 2.5rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.edit-profile-header {
    margin-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 1rem;
}

.edit-profile-header h2 {
    color: #333;
    font-size: 1.8rem;
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #555;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #4a8f29;
    box-shadow: 0 0 0 3px rgba(74, 143, 41, 0.1);
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.alert-danger {
    background-color: #fff5f5;
    border: 1px solid #ffd6d6;
    color: #dc3545;
}

.btn-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 2rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-save {
    background: #4a8f29;
    color: white;
    border: none;
    flex: 2;
}

.btn-save:hover {
    background: #3a7020;
}

.btn-cancel {
    background: transparent;
    border: 1px solid #ccc;
    color: #666;
    flex: 1;
}

.btn-cancel:hover {
    background: #f5f5f5;
    color: #333;
}
</style>

<div class="edit-profile-container">
    <div class="edit-profile-header">
        <h2>Edit Profile Information</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_profile.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-save">Save Changes</button>
            <a href="profile.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include('footer.php'); ?>
