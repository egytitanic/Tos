<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header('Location: /login.php');
    exit;
}
require_once 'core/db.php';
$user_id = $_SESSION['user_id'];

// Fetch user data (will be used later)
$stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch user orders
$orders_stmt = $pdo->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders_stmt->execute([$user_id]);
$orders = $orders_stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف المستخدم - TEKNATON OS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .profile-info, .section {
            margin-bottom: 30px;
        }
        .profile-info p {
            font-size: 1.1em;
            line-height: 1.6;
        }
        .profile-info strong {
            color: #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .status.pending { background-color: #f39c12; }
        .status.completed { background-color: #2ecc71; }
        .status.cancelled { background-color: #e74c3c; }
        .no-data {
            text-align: center;
            padding: 20px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ملفي الشخصي</h1>

        <div class="profile-info section">
            <h2>البيانات الأساسية</h2>
            <p><strong>الاسم:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>الدور:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <div class="section">
            <h2>سجل الطلبات</h2>
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                <td><span class="status <?php echo htmlspecialchars($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">لا يوجد طلبات لعرضها حاليًا.</p>
            <?php endif; ?>
        </div>

        <!-- Subscriptions section will be here -->

    </div>
</body>
</html>
