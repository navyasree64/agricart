<?php
session_start();
include('db.php');

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Get user from database
    $query = "SELECT id, name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            // Password matches
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}

// Include header after all potential redirects
include('header.php');
?>

<style>
/* Login Page Styles */
.login-container {
    max-width: 450px;
    margin: 4rem auto;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-header h2 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.login-header p {
    color: #666;
    font-size: 1rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #4a8f29;
    box-shadow: 0 0 0 3px rgba(74, 143, 41, 0.1);
}

.form-control::placeholder {
    color: #999;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #4a8f29;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    width: 100%;
}

.btn:hover {
    background: #3a7020;
    transform: translateY(-1px);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.alert-danger {
    background-color: #fff5f5;
    border: 1px solid #ffd6d6;
    color: #dc3545;
}

.login-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.login-footer p {
    color: #666;
    margin-bottom: 0.5rem;
}

.login-footer a {
    color: #4a8f29;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.login-footer a:hover {
    color: #3a7020;
    text-decoration: underline;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 0.25rem;
}

.password-toggle:hover {
    color: #333;
}

@media (max-width: 576px) {
    .login-container {
        margin: 2rem 1rem;
        padding: 1.5rem;
    }
    
    .login-header h2 {
        font-size: 1.75rem;
    }
}
</style>

<div class="login-container">
    <div class="login-header">
        <h2>Welcome Back</h2>
        <p>Please login to your account</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="loginForm" class="login-form">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <button type="submit" class="btn">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="login-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="forgot-password.php">Forgot your password?</a></p>
        <p style="margin-top: 1rem; border-top: 1px dashed #ddd; padding-top: 0.8rem;">Are you an administrator? <a href="adminlogin.php" style="color: #2c3e50; font-weight: 600;">Admin Login</a></p>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert("Please fill out all fields.");
    }
});
</script>

<?php include('footer.php'); ?>
