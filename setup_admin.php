<?php
include('db.php');

// Change these credentials if needed
$username = "admin";
$password = "admin123";

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the admin
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully.";
} else {
    echo "❌ Error: " . $stmt->error;
}
$stmt->close();
?>
