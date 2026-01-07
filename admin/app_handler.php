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

// Handling Add/Edit App
if (isset($_POST['save_app'])) {
    $id = $_POST['id'] ?: null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $type = $_POST['type'];
    $content = ($type === 'iframe') ? trim($_POST['url']) : trim($_POST['html_content']);
    $category = trim($_POST['category']);
    $icon = trim($_POST['icon']);
    $price_monthly = filter_var($_POST['price_monthly'], FILTER_VALIDATE_FLOAT);
    $price_yearly = filter_var($_POST['price_yearly'], FILTER_VALIDATE_FLOAT);
    $yearly_discount_percent = filter_var($_POST['yearly_discount_percent'], FILTER_VALIDATE_INT);
    $is_active = $_POST['is_active'];

    if (empty($name) || !in_array($type, ['iframe', 'html'])) {
        die('Validation failed');
    }

    if (is_null($id)) {
        $sql = "INSERT INTO apps (name, description, type, content, category, icon, price_monthly, price_yearly, yearly_discount_percent, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $type, $content, $category, $icon, $price_monthly, $price_yearly, $yearly_discount_percent, $is_active]);
    } else {
        $sql = "UPDATE apps SET name = ?, description = ?, type = ?, content = ?, category = ?, icon = ?, price_monthly = ?, price_yearly = ?, yearly_discount_percent = ?, is_active = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $type, $content, $category, $icon, $price_monthly, $price_yearly, $yearly_discount_percent, $is_active, $id]);
    }
    header('Location: apps_manage.php?status=success');
    exit;
}

// Handling Delete App
if (isset($_POST['delete_app'])) {
    $id = $_POST['id'];
    if (!empty($id)) {
        $sql = "DELETE FROM apps WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        header('Location: apps_manage.php?status=deleted');
        exit;
    }
}

header('Location: apps_manage.php');
exit;
