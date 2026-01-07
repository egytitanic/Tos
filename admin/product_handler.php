<?php
session_start();
require_once '../core/db.php';
require_once '../core/csrf.php';

// Security check: ensure user is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die('Access Denied');
}

// CSRF Token Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }
}

// Handling Add/Edit Product
if (isset($_POST['save_product'])) {
    $id = $_POST['id'] ?: null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $image_url = trim($_POST['image_url']);
    $is_active = $_POST['is_active'];

    if (empty($name) || $price === false) {
        die('Validation failed: Name and a valid price are required.');
    }

    if ($id) {
        $sql = "UPDATE products SET name = ?, description = ?, category = ?, price = ?, image_url = ?, is_active = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $category, $price, $image_url, $is_active, $id]);
    } else {
        $sql = "INSERT INTO products (name, description, category, price, image_url, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $category, $price, $image_url, $is_active]);
    }
    header('Location: products_manage.php?status=success');
    exit;
}

// Handling Delete Product
if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        header('Location: products_manage.php?status=deleted');
        exit;
    }
}

header('Location: products_manage.php');
exit;
