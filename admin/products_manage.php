<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require_once '../core/db.php';
require_once '../core/csrf.php';

// Fetch all products from the database
$stmt = $pdo->query("SELECT id, name, category, price, is_active FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات - TEKNATON OS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles for the manage page table, reused from apps_manage */
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
        .actions a, .delete-btn {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .actions a.delete, .delete-btn {
            color: #dc3545;
        }
        .delete-btn {
            background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline;
        }
        .add-new-btn {
            display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745;
            color: white; text-decoration: none; border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>TEKNATON OS</h2>
            <ul>
                <li><a href="index.php">لوحة التحكم</a></li>
                <li><a href="apps_manage.php">إدارة التطبيقات</a></li>
                <li><a href="products_manage.php" class="active">إدارة المنتجات</a></li>
                <li><a href="notifications_manage.php">إدارة الإشعارات</a></li>
                <li><a href="subscriptions_manage.php">عرض الاشتراكات</a></li>
                <li><a href="orders_manage.php">عرض الطلبات</a></li>
                <li><a href="../index.php">العودة للنظام</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>إدارة المنتجات</h1>

            <a href="product_edit.php" class="add-new-btn">إضافة منتج جديد</a>

            <table class="manage-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>الفئة</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['is_active'] ? 'نشط' : 'معطل'; ?></td>
                        <td class="actions">
                            <a href="product_edit.php?id=<?php echo $product['id']; ?>">تعديل</a>
                            <form action="product_handler.php" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟');">
                                <?php csrf_input_field(); ?>
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="delete_product" class="delete-btn">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
