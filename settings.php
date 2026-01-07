<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
require_once 'core/db.php';
require_once 'core/csrf.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token($_POST['csrf_token'])) {
        $theme = trim($_POST['theme']);
        $wallpaper = trim($_POST['wallpaper']);

        $sql = "UPDATE users SET theme = ?, wallpaper = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$theme, $wallpaper, $user_id])) {
            $message = "تم حفظ الإعدادات بنجاح!";
        } else {
            $message = "حدث خطأ أثناء حفظ الإعدادات.";
        }
    } else {
        $message = "خطأ في التحقق من CSRF.";
    }
}

// Fetch current settings
$stmt = $pdo->prepare("SELECT theme, wallpaper FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإعدادات - TEKNATON OS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body { padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 10px 15px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-bottom: 15px; padding: 10px; background-color: #e2f0e4; border: 1px solid #b2d6b7; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>الإعدادات</h1>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <?php csrf_input_field(); ?>
            <div class="form-group">
                <label for="theme">المظهر (Theme)</label>
                <select id="theme" name="theme">
                    <option value="default" <?php echo ($user['theme'] == 'default') ? 'selected' : ''; ?>>الافتراضي</option>
                    <option value="dark" <?php echo ($user['theme'] == 'dark') ? 'selected' : ''; ?>>مظلم</option>
                </select>
            </div>
            <div class="form-group">
                <label for="wallpaper">رابط الخلفية</label>
                <input type="url" id="wallpaper" name="wallpaper" value="<?php echo htmlspecialchars($user['wallpaper'] ?: ''); ?>" placeholder="https://example.com/wallpaper.jpg">
            </div>
            <button type="submit">حفظ الإعدادات</button>
        </form>
    </div>
</body>
</html>
