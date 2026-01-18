<?php
session_start();
require_once 'core/db.php';

$products = [];
try {
    $stmt = $pdo->query("SELECT id, name, description, price, image_url FROM products WHERE is_active = 1 ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    //
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر الأجهزة - TEKNATON OS</title>
    <style>
        /* Styles from previous step */
    </style>
</head>
<body>

    <div class="store-header">
        <h1>متجر الأجهزة</h1>
        <p id="feedback-message" style="display:none;"></p>
        <a href="cart.php" style="font-size: 24px;">🛒 عرض السلة</a>
    </div>

    <div class="products-grid" id="products-grid-container">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/250x150'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                    <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">أضف إلى السلة</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>لا توجد منتجات متاحة حاليًا.</p>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const productsGrid = document.getElementById('products-grid-container');
        const feedbackMessage = document.getElementById('feedback-message');

        productsGrid.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn')) {
                const button = e.target;
                const productId = button.dataset.productId;

                button.disabled = true;
                button.textContent = '...جارٍ الإضافة';

                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', productId);

                fetch('api/cart_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    feedbackMessage.textContent = data.message;
                    feedbackMessage.style.color = data.success ? 'green' : 'red';
                    feedbackMessage.style.display = 'block';

                    // Re-enable button after a short delay
                    setTimeout(() => {
                        button.disabled = false;
                        button.textContent = 'أضف إلى السلة';
                    }, 1000);
                })
                .catch(error => {
                    feedbackMessage.textContent = 'حدث خطأ في الشبكة.';
                    feedbackMessage.style.color = 'red';
                    feedbackMessage.style.display = 'block';
                    button.disabled = false;
                    button.textContent = 'أضف إلى السلة';
                });
            }
        });
    });
    </script>
</body>
</html>
