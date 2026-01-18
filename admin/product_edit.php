<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}
require_once '../core/db.php';
require_once '../core/csrf.php';

$product_id = $_GET['id'] ?? null;
if ($product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('Location: products_manage.php');
        exit;
    }
    $page_title = "تعديل المنتج: " . htmlspecialchars($product['name']);
} else {
    // Default values for a new product
    $product = [
        'id' => null, 'name' => '', 'description' => '', 'category' => '', 'price' => 0, 'image_url' => '', 'is_active' => 1
    ];
    $page_title = "إضافة منتج جديد";
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - TEKNATON OS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-actions button { padding: 10px 20px; border: none; border-radius: 5px; background-color: #3498db; color: white; cursor: pointer; }
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
            <h1><?php echo $page_title; ?></h1>
            <div class="form-container">
                <form action="product_handler.php" method="POST">
                    <?php csrf_input_field(); ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">

                    <div class="form-group">
                        <label for="name">اسم المنتج</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">الوصف</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">الفئة</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">السعر</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="image_url">رابط الصورة</label>
                        <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="is_active">الحالة</label>
                        <select id="is_active" name="is_active">
                            <option value="1" <?php echo ($product['is_active'] == 1) ? 'selected' : ''; ?>>نشط</option>
                            <option value="0" <?php echo ($product['is_active'] == 0) ? 'selected' : ''; ?>>معطل</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="save_product">حفظ المنتج</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
