<?php
session_start();
require_once 'db.php';

// Function to redirect with an error message
function redirectWithError($url, $message) {
    $_SESSION['error'] = $message;
    header("Location: $url");
    exit();
}

// Function to redirect with a success message
function redirectWithSuccess($url, $message) {
    $_SESSION['success'] = $message;
    header("Location: $url");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'register') {
        // --- Registration Logic ---
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Basic Validation
        if (empty($name) || empty($email) || empty($password)) {
            redirectWithError('../register.php', 'Please fill in all fields.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithError('../register.php', 'Invalid email format.');
        }

        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            redirectWithError('../register.php', 'Email already exists.');
        }

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $password_hash]);
            redirectWithSuccess('../login.php', 'Registration successful! Please log in.');
        } catch (PDOException $e) {
            // Log the error instead of showing it to the user in production
            redirectWithError('../register.php', 'Database error. Could not register user.');
        }

    } elseif ($_POST['action'] === 'login') {
        // --- Login Logic ---
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            redirectWithError('../login.php', 'Please fill in all fields.');
        }

        // Fetch user from the database
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['password_hash'])) {
            // Password is correct, start the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect to the desktop
            header("Location: ../desktop.php");
            exit();
        } else {
            redirectWithError('../login.php', 'Invalid email or password.');
        }
    }
}

// --- Logout Logic ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Fallback redirect if accessed directly
header("Location: ../index.php");
exit();
