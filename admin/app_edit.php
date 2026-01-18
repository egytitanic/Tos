<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}
require_once '../core/db.php';
require_once '../core/csrf.php';

$app_id = $_GET['id'] ?? null;
$is_edit_mode = !is_null($app_id);

if ($is_edit_mode) {
    $stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ?");
    $stmt->execute([$app_id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$app) {
        // App not found, handle error (e.g., redirect)
        header('Location: apps_manage.php');
        exit;
    }
    $page_title = "تعديل التطبيق: " . htmlspecialchars($app['name']);
    // Separate content for form fields
    $app['url'] = ($app['type'] === 'iframe') ? $app['content'] : '';
    $app['html_content'] = ($app['type'] === 'html') ? $app['content'] : '';
} else {
    // Default values for a new app
    $app = [
        'id' => null, 'name' => '', 'description' => '', 'type' => 'iframe', 'category' => '',
        'url' => '', 'html_content' => '', 'icon' => '', 'price_monthly' => 0,
        'price_yearly' => 0, 'yearly_discount_percent' => 0, 'is_active' => 1
    ];
    $page_title = "إضافة تطبيق جديد";
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
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
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
                <li><a href="products_manage.php">إدارة المنتجات</a></li>
                <li><a href="notifications_manage.php">إدارة الإشعارات</a></li>
                <li><a href="subscriptions_manage.php">عرض الاشتراكات</a></li>
                <li><a href="orders_manage.php">عرض الطلبات</a></li>
                <li><a href="../index.php">العودة للنظام</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1><?php echo $page_title; ?></h1>
            <div class="form-container">
                <form id="appForm" action="app_handler.php" method="POST">
                    <?php csrf_input_field(); ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($app['id']); ?>">

                    <div class="form-group">
                        <label for="name">اسم التطبيق</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($app['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">الوصف</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($app['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type">نوع التطبيق</label>
                        <select id="type" name="type">
                            <option value="iframe" <?php echo ($app['type'] == 'iframe') ? 'selected' : ''; ?>>Iframe</option>
                            <option value="html" <?php echo ($app['type'] == 'html') ? 'selected' : ''; ?>>HTML</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="url">الرابط (لنوع iframe)</label>
                        <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($app['url']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="html_content">محتوى HTML (لنوع html)</label>
                        <textarea id="html_content" name="html_content" rows="6"><?php echo htmlspecialchars($app['html_content']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">الفئة</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($app['category']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="icon">رابط الأيقونة</label>
                        <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($app['icon']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price_monthly">السعر الشهري</label>
                        <input type="number" id="price_monthly" name="price_monthly" step="0.01" value="<?php echo htmlspecialchars($app['price_monthly']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price_yearly">السعر السنوي</label>
                        <input type="number" id="price_yearly" name="price_yearly" step="0.01" value="<?php echo htmlspecialchars($app['price_yearly']); ?>">
                    </div>

                     <div class="form-group">
                        <label for="yearly_discount_percent">نسبة الخصم السنوي (%)</label>
                        <input type="number" id="yearly_discount_percent" name="yearly_discount_percent" value="<?php echo htmlspecialchars($app['yearly_discount_percent']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="is_active">الحالة</label>
                        <select id="is_active" name="is_active">
                            <option value="1" <?php echo ($app['is_active'] == 1) ? 'selected' : ''; ?>>نشط</option>
                            <option value="0" <?php echo ($app['is_active'] == 0) ? 'selected' : ''; ?>>معطل</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="save_app">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
