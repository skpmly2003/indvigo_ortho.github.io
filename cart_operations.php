<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

// Добавление в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!isset($_POST['product_id']) || !isset($_POST['size'])) {
        echo json_encode(['success' => false, 'error' => 'Недостаточно данных']);
        exit;
    }

    $productId = (int)$_POST['product_id'];
    $size = $_POST['size'];
    $sessionId = session_id();

    try {
        // Проверяем есть ли уже такой товар в корзине
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ? AND size = ?");
        $stmt->execute([$sessionId, $productId, $size]);
        $existingItem = $stmt->fetch();

        if ($existingItem) {
            // Увеличиваем количество
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$existingItem['id']]);
        } else {
            // Добавляем новый товар
            $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, size, quantity) VALUES (?, ?, ?, 1)");
            $stmt->execute([$sessionId, $productId, $size]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Получение содержимого корзины
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cart') {
    $sessionId = session_id();
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, p.name, p.price, p.image_path 
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'items' => $cartItems]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}