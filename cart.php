<?php
session_start();
require 'db_connection.php';

// Получаем содержимое корзины
$cartItems = [];
try {
    $stmt = $pdo->prepare("
        SELECT c.*, p.name, p.price, p.image_path 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ");
    $stmt->execute([session_id()]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Обработка ошибки
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ваша корзина</title>
    <style>
        .cart-item {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .cart-item-image {
            width: 100px;
            margin-right: 20px;
        }
        .cart-item-details {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <h1>Ваш заказ</h1>
    
    <?php if (empty($cartItems)): ?>
        <p>Ваша корзина пуста</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image_path']) ?>" class="cart-item-image">
                <div class="cart-item-details">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p>Размер: <?= htmlspecialchars($item['size']) ?></p>
                    <p>Цена: <?= number_format($item['price'], 2) ?> ₽</p>
                    <p>Количество: <?= $item['quantity'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-total">
            <h3>Итого: <?= number_format(array_sum(array_map(function($item) { 
                return $item['price'] * $item['quantity']; 
            }, $cartItems)), 2) ?> ₽</h3>
            <button id="checkout-button">Оформить заказ</button>
        </div>
    <?php endif; ?>
</body>
</html>