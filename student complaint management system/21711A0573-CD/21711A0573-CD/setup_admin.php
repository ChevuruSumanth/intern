<?php
// Include database configuration
include('config.php');

// Admin details
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'sumanth@123'; // Plain-text password

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// SQL query to insert admin user
$sql = "INSERT INTO users (username, password, email, role) 
        VALUES (?, ?, ?, 'admin')";

// Prepare statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Could not prepare statement');
}

// Bind parameters and execute
$stmt->bind_param('sss', $admin_username, $hashed_password, $admin_email);
$result = $stmt->execute();

// Check if query executed successfully
if ($result) {
    echo "Admin user created successfully.";
} else {
    echo "Error creating admin user: " . $conn->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>