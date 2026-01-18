<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحبًا بك في TEKNATON OS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="desktop-body">

    <!-- Guest Welcome Message -->
    <div id="guest-welcome" class="app-window" style="z-index: 101; top: 100px; left: 150px;">
        <div class="window-title-bar">
            <span class="title">مرحبًا بك!</span>
            <div class="window-controls">
                <button class="close">X</button>
            </div>
        </div>
        <div class="window-content" style="padding: 15px;">
            <h2>استكشف TEKNATON OS</h2>
            <p>أنت تتصفح الآن كزائر. يمكنك استكشاف الصفحات العامة مثل "من نحن" و "خدماتنا".</p>
            <p>لتثبيت التطبيقات، أو الشراء من المتجر، أو تخصيص تجربتك، يرجى <a href="login.php">تسجيل الدخول</a> أو <a href="register.php">إنشاء حساب جديد</a>.</p>
        </div>
    </div>


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
    </div>

    <!-- Windows Container -->
    <div id="windows-container"></div>

    <!-- Taskbar -->
    <div id="taskbar">
        <button id="start-button">ابدأ</button>
        <div id="open-windows-list"></div>
        <div id="system-tray">
            <div id="user-menu">
                <a href="login.php" style="color:white; text-decoration:none; padding: 0 10px;">تسجيل الدخول</a>
            </div>
        </div>
    </div>

    <!-- Start Menu -->
    <div id="start-menu">
        <div class="start-menu-header"><h3>TEKNATON OS</h3></div>
        <div class="start-menu-content">
            <p style="padding: 15px;">يرجى تسجيل الدخول للوصول إلى التطبيقات والإعدادات.</p>
        </div>
    </div>

    <!-- HUD Widget -->
    <div id="hud-widget">
        <div id="hud-time"></div>
        <div id="hud-date"></div>
    </div>

    <script src="assets/js/desktop.js"></script>
    <script>
    // Guest-specific overrides for desktop.js functionality
    document.addEventListener('DOMContentLoaded', () => {
        const guestWelcome = document.getElementById('guest-welcome');
        guestWelcome.querySelector('.close').addEventListener('click', () => {
            guestWelcome.style.display = 'none';
        });

        // Override window creation for guests on certain apps
        const originalCreateWindow = window.createWindow; // Assuming createWindow is global
        window.createWindow = (url, title) => {
            const allowedGuestPages = ['about.php', 'services.php', 'app_store.php', 'devices_store.php'];
            if (allowedGuestPages.includes(url)) {
                originalCreateWindow(url, title);
            } else {
                alert('يرجى تسجيل الدخول للوصول إلى هذه الميزة.');
            }
        };
    });
    </script>
</body>
</html>
