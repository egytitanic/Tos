<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userName = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سطح المكتب - TEKNATON OS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="desktop-body">

    <!-- Desktop Icons -->
    <div id="desktop-icons">
        <div class="desktop-icon" data-app-url="about.php">
            <div class="icon-placeholder">📝</div>
            <span>من نحن</span>
        </div>
        <div class="desktop-icon" data-app-url="services.php">
            <div class="icon-placeholder">💼</div>
            <span>خدماتنا</span>
        </div>
        <div class="desktop-icon" data-app-url="app_store.php">
            <div class="icon-placeholder">🛒</div>
            <span>متجر التطبيقات</span>
        </div>
        <div class="desktop-icon" data-app-url="devices_store.php">
            <div class="icon-placeholder">💻</div>
            <span>متجر الأجهزة</span>
        </div>
        <div class="desktop-icon" data-app-url="dashboard.php">
            <div class="icon-placeholder">📊</div>
            <span>لوحة التحكم</span>
        </div>
        <div class="desktop-icon" data-app-url="profile.php">
            <div class="icon-placeholder">👤</div>
            <span>ملفي الشخصي</span>
        </div>
        <!-- User-specific icons will be loaded here -->
    </div>

    <!-- Windows Container -->
    <div id="windows-container">
        <!-- Apps will open here -->
    </div>

    <!-- Taskbar -->
    <div id="taskbar">
        <button id="start-button">ابدأ</button>
        <div id="open-windows-list"></div>
        <div id="system-tray">
            <div id="notifications-icon">🔔</div>
            <div id="user-menu">
                <span><?php echo htmlspecialchars($userName); ?></span>
                <div class="user-menu-dropdown">
                    <a href="profile.php">حسابي</a>
                    <a href="settings.php">الإعدادات</a>
                    <a href="core/auth.php?action=logout">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Menu -->
    <div id="start-menu">
        <div class="start-menu-header">
            <h3>TEKNATON OS</h3>
        </div>
        <div class="start-menu-content">
            <div class="column">
                <h4>مقترحات</h4>
                <ul>
                    <li><a href="#" data-app-url="app_store.php">متجر التطبيقات</a></li>
                    <li><a href="#" data-app-url="settings.php">الإعدادات</a></li>
                </ul>
            </div>
            <div class="column">
                <h4>الأكثر استخدامًا</h4>
                <ul id="most-used-apps">
                    <!-- Populated by JS -->
                </ul>
            </div>
        </div>
        <div class="start-menu-footer">
            <a href="dashboard.php">لوحة التحكم</a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/index.php">لوحة المدير</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- HUD Widget -->
    <div id="hud-widget">
        <div id="hud-time"></div>
        <div id="hud-date"></div>
        <div class="hud-buttons">
            <button data-app-url="dashboard.php">Dashboard</button>
            <button data-app-url="app_store.php">Store</button>
        </div>
    </div>


    <script src="assets/js/desktop.js"></script>
</body>
</html>
