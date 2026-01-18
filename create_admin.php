<?php
// --- TEKNATON OS Admin Creation Script ---
// WARNING: This script should be deleted after its first use for security reasons.
//
// Instructions:
// 1. Make sure your database connection is correctly configured in 'core/db.php'.
// 2. Set the desired admin credentials in the variables below.
// 3. Upload this file to the root directory of your application.
// 4. Access it from your browser (e.g., your-domain.com/create_admin.php).
// 5. DELETE THIS FILE IMMEDIATELY AFTER USE.

// --- Configuration ---
$admin_name = "admin";
$admin_email = "admin@teknaton.com";
$admin_password = "strong_password_123"; // CHANGE THIS!

// --- Script ---
require_once 'core/db.php';

echo "<h1>Admin Creation Script</h1>";

// Check if admin already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR role = 'admin'");
    $stmt->execute([$admin_email]);
    if ($stmt->fetch()) {
        die("<p style='color:red;'>An admin account already exists or the email is already in use. Please delete this script.</p>");
    }
} catch (PDOException $e) {
    die("<p style='color:red;'>Database Error: " . $e->getMessage() . ". Please check your DB connection and ensure tables are imported.</p>");
}


// Hash the password
$password_hash = password_hash($admin_password, PASSWORD_BCRYPT);

// Insert admin into the database
try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$admin_name, $admin_email, $password_hash]);

    echo "<p style='color:green;'>Admin user created successfully!</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> " . htmlspecialchars($admin_name) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($admin_email) . "</li>";
    echo "<li><strong>Password:</strong> " . htmlspecialchars($admin_password) . "</li>";
    echo "</ul>";
    echo "<h2 style='color:red;'>IMPORTANT: Please delete this file ('create_admin.php') from your server NOW.</h2>";

} catch (PDOException $e) {
    die("<p style='color:red;'>Failed to create admin user. Error: " . $e->getMessage() . "</p>");
}
