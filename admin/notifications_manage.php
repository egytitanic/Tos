<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}
require_once '../core/db.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الإشعارات - TEKNATON OS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>TEKNATON OS</h2>
            <ul>
                <li><a href="index.php">لوحة التحكم</a></li>
                <li><a href="apps_manage.php">إدارة التطبيقات</a></li>
                <li><a href="products_manage.php">إدارة المنتجات</a></li>
                <li><a href="notifications_manage.php" class="active">إدارة الإشعارات</a></li>
                <li><a href="subscriptions_manage.php">عرض الاشتراكات</a></li>
                <li><a href="orders_manage.php">عرض الطلبات</a></li>
                <li><a href="../index.php">العودة للنظام</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>إدارة الإشعارات</h1>
            <p>سيتم تنفيذ هذه الميزة قريبًا.</p>
        </div>
    </div>
</body>
</html>
