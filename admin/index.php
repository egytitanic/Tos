<?php
session_start();

// Check if user is logged in and is an admin.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require_once '../core/db.php';

// Fetch system-wide statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_apps = $pdo->query("SELECT COUNT(*) FROM apps")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_subscriptions = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - TEKNATON OS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>TEKNATON OS</h2>
            <ul>
                <li><a href="index.php" class="active">لوحة التحكم</a></li>
                <li><a href="apps_manage.php">إدارة التطبيقات</a></li>
                <li><a href="products_manage.php">إدارة المنتجات</a></li>
                <li><a href="notifications_manage.php">إدارة الإشعارات</a></li>
                <li><a href="subscriptions_manage.php">عرض الاشتراكات</a></li>
                <li><a href="orders_manage.php">عرض الطلبات</a></li>
                <li><a href="../index.php">العودة للنظام</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>ملخص وإحصائيات النظام</h1>

            <!-- Statistics widgets will go here -->
            <div class="widget-grid">
                <div class="widget">
                    <h3><?php echo $total_users; ?></h3>
                    <p>إجمالي المستخدمين</p>
                </div>
                <div class="widget">
                    <h3><?php echo $total_apps; ?></h3>
                    <p>إجمالي التطبيقات</p>
                </div>
                <div class="widget">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>إجمالي الطلبات</p>
                </div>
                <div class="widget">
                    <h3><?php echo $total_subscriptions; ?></h3>
                    <p>الاشتراكات الفعالة</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
