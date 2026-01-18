<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once 'core/db.php';
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Fetch stats for widgets
// 1. Installed Apps Count
$apps_stmt = $pdo->prepare("SELECT COUNT(*) FROM user_apps WHERE user_id = ?");
$apps_stmt->execute([$user_id]);
$installed_apps_count = $apps_stmt->fetchColumn();

// 2. Total Orders Count
$orders_stmt = $pdo->prepare("SELECT COUNT(*), SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) FROM orders WHERE user_id = ?");
$orders_stmt->execute([$user_id]);
list($total_orders_count, $pending_orders_count) = $orders_stmt->fetch(PDO::FETCH_NUM);

// Placeholder for active subscriptions
$active_subscriptions_count = 0; // Will be implemented later

// Fetch latest notifications (public and user-specific)
$notifications_stmt = $pdo->prepare(
    "SELECT message, created_at FROM notifications
     WHERE user_id = ? OR user_id IS NULL
     ORDER BY created_at DESC
     LIMIT 5"
);
$notifications_stmt->execute([$user_id]);
$notifications = $notifications_stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - TEKNATON OS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            background-color: #ecf0f1;
            color: #333;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: auto;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .widgets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .widget {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .widget i {
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 15px;
        }
        .widget h3 {
            margin: 0;
            font-size: 2.2em;
            color: #2c3e50;
        }
        .widget p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 1.1em;
        }
        .notifications-panel {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .notifications-panel h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .notifications-panel ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .notifications-panel li {
            padding: 15px 10px;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notifications-panel li:last-child {
            border-bottom: none;
        }
        .notifications-panel li p {
            margin: 0;
            flex-grow: 1;
        }
        .notifications-panel li span {
            color: #95a5a6;
            font-size: 0.9em;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h1>

        <div class="widgets-grid">
            <div class="widget">
                <i class="fas fa-th-large"></i>
                <h3><?php echo $installed_apps_count; ?></h3>
                <p>تطبيق مثبت</p>
            </div>
            <div class="widget">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo $active_subscriptions_count; ?></h3>
                <p>اشتراك فعال</p>
            </div>
            <div class="widget">
                <i class="fas fa-shopping-cart"></i>
                <h3><?php echo $total_orders_count; ?></h3>
                <p>إجمالي الطلبات</p>
            </div>
            <div class="widget">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pending_orders_count; ?></h3>
                <p>طلبات معلقة</p>
            </div>
        </div>

        <div class="notifications-panel">
            <h2><i class="fas fa-bell"></i> آخر الإشعارات</h2>
            <?php if (count($notifications) > 0): ?>
                <ul>
                    <?php foreach ($notifications as $notification): ?>
                        <li>
                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                            <span><?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>لا توجد إشعارات جديدة.</p>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
