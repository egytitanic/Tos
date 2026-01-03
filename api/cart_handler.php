<?php
header('Content-Type: application/json');
session_start();
require_once '../core/db.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$action = $_POST['action'] ?? '';
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// --- Add to Cart ---
if ($action === 'add' && $productId > 0) {
    try {
        // Fetch product details to ensure it exists and to get the price
        $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            // Check if product is already in cart
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity']++;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1
                ];
            }
            $response = ['success' => true, 'message' => 'تمت إضافة المنتج إلى السلة بنجاح!'];
        } else {
            $response['message'] = 'المنتج غير موجود.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'خطأ في قاعدة البيانات.';
    }
}

// --- Remove from Cart ---
elseif ($action === 'remove' && $productId > 0) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $response = ['success' => true, 'message' => 'تمت إزالة المنتج من السلة.'];
    } else {
        $response['message'] = 'المنتج ليس في السلة.';
    }
}

// --- Update Quantity ---
elseif ($action === 'update' && $productId > 0) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            $response = ['success' => true, 'message' => 'تم تحديث الكمية.'];
        } else {
            // If quantity is 0 or less, remove the item
            unset($_SESSION['cart'][$productId]);
            $response = ['success' => true, 'message' => 'تمت إزالة المنتج.'];
        }
    } else {
        $response['message'] = 'المنتج ليس في السلة.';
    }
}

echo json_encode($response);
