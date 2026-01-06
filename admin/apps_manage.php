<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require_once '../core/db.php';

// Fetch all applications from the database
$stmt = $pdo->query("SELECT id, name, type, category, price_monthly, is_active FROM apps ORDER BY id DESC");
$apps = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التطبيقات - TEKNATON OS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for the manage page table */
        .manage-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .manage-table th, .manage-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: right;
        }
        .manage-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .manage-table tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .actions a.delete {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>TEKNATON OS</h2>
            <ul>
                <li><a href="index.php">لوحة التحكم</a></li>
                <li><a href="apps_manage.php" class="active">إدارة التطبيقات</a></li>
                <li><a href="#">إدارة المنتجات</a></li>
                <li><a href="#">إدارة الإشعارات</a></li>
                <li><a href="#">عرض الاشتراكات</a></li>
                <li><a href="#">عرض الطلبات</a></li>
                <li><a href="../index.php">العودة للنظام</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>إدارة التطبيقات</h1>

            <table class="manage-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>الفئة</th>
                        <th>السعر الشهري</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apps as $app): ?>
                    <tr>
                        <td><?php echo $app['id']; ?></td>
                        <td><?php echo htmlspecialchars($app['name']); ?></td>
                        <td><?php echo htmlspecialchars($app['type']); ?></td>
                        <td><?php echo htmlspecialchars($app['category']); ?></td>
                        <td>$<?php echo number_format($app['price_monthly'], 2); ?></td>
                        <td><?php echo $app['is_active'] ? 'نشط' : 'معطل'; ?></td>
                        <td class="actions">
                            <a href="#">تعديل</a>
                            <a href="#" class="delete">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
