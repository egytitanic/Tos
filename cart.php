<?php
session_start();
// Initialize cart from session, or as an empty array
$cartItems = $_SESSION['cart'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سلة المشتريات - TEKNATON OS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; padding: 20px; }
        .cart-container { max-width: 900px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: right; border-bottom: 1px solid #ddd; }
        th { background-color: #f7f7f7; }
        .total-row td { font-weight: bold; font-size: 1.2em; }
        .checkout-btn { display: block; width: 200px; margin: 20px auto 0; padding: 12px; background-color: #27ae60; color: white; text-align: center; text-decoration: none; border-radius: 4px; }
        .remove-btn { color: red; text-decoration: none; }
    </style>
</head>
<body>

    <div class="cart-container">
        <h1>سلة المشتريات</h1>

        <?php if (empty($cartItems)): ?>
            <p>سلة المشتريات فارغة.</p>
            <a href="devices_store.php">العودة إلى المتجر</a>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي الفرعي</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td><a href="#" class="remove-btn" data-product-id="<?php echo $item['id']; ?>">إزالة</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3">الإجمالي النهائي</td>
                        <td colspan="2">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <a href="checkout.php" class="checkout-btn">إتمام الطلب</a>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('tbody')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;

                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('product_id', productId);

                fetch('api/cart_handler.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to reflect the change
                            window.location.reload();
                        } else {
                            alert(data.message || 'فشل إزالة المنتج.');
                        }
                    });
            }
        });
    });
    </script>
</body>
</html>
