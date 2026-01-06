<?php
session_start();
require_once '../core/db.php';

// Security check: ensure user is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Or return a 403 Forbidden error
    die('Access Denied');
}

// Check if the form was submitted for saving (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_app'])) {

    // Sanitize and gather data from POST
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

    // Basic validation
    if (empty($name) || !in_array($type, ['iframe', 'html'])) {
        // Handle error - maybe redirect back with a message
        die('Validation failed');
    }

    if (is_null($id)) {
        // INSERT new app
        $sql = "INSERT INTO apps (name, description, type, content, category, icon, price_monthly, price_yearly, yearly_discount_percent, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name, $description, $type, $content, $category, $icon,
            $price_monthly, $price_yearly, $yearly_discount_percent, $is_active
        ]);
    } else {
        // UPDATE existing app
        $sql = "UPDATE apps SET name = ?, description = ?, type = ?, content = ?,
                category = ?, icon = ?, price_monthly = ?, price_yearly = ?,
                yearly_discount_percent = ?, is_active = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name, $description, $type, $content, $category, $icon,
            $price_monthly, $price_yearly, $yearly_discount_percent, $is_active, $id
        ]);
    }

    // Redirect after operation
    header('Location: apps_manage.php?status=success');
    exit;
}

// Check if a delete request was sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_app'])) {

    $id = $_POST['id'];
    if (!empty($id)) {
        // Before deleting from 'apps', you might want to handle related data in
        // 'user_apps', 'subscriptions', etc. For now, we'll do a simple delete.
        $sql = "DELETE FROM apps WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        // Redirect after operation
        header('Location: apps_manage.php?status=deleted');
        exit;
    }
}

// Redirect back to the manage page if accessed directly or with no action
header('Location: apps_manage.php');
exit;
