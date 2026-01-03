<?php
session_start();
require_once 'core/db.php';

// --- Security & Validation ---

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// 2. Check if the cart is empty
$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    header('Location: devices_store.php'); // Redirect to store if cart is empty
    exit();
}

$userId = $_SESSION['user_id'];
$total = 0;

// Calculate total server-side to ensure integrity
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// --- Checkout Logic ---
try {
    // Begin database transaction
    $pdo->beginTransaction();

    // 1. Create a new order in the 'orders' table
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$userId, $total]);
    $orderId = $pdo->lastInsertId();

    // 2. Insert each cart item into the 'order_items' table
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
    }

    // 3. Create a notification for the user
    $notificationStmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
    $notificationMessage = "تم إنشاء طلبك الجديد رقم #{$orderId} بنجاح.";
    $notificationStmt->execute([$userId, 'تم إنشاء الطلب', $notificationMessage]);

    // If everything is successful, commit the transaction
    $pdo->commit();

    // 4. Clear the cart from the session
    unset($_SESSION['cart']);

    // Redirect to a success page or profile page
    // For now, redirect to profile where they can see their orders
    header('Location: profile.php?order_success=true');
    exit();

} catch (PDOException $e) {
    // If any error occurs, roll back the transaction
    $pdo->rollBack();

    // In a real application, you would log this error.
    // Redirect back to the cart with an error message
    $_SESSION['checkout_error'] = "حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى.";
    header('Location: cart.php');
    exit();
}
