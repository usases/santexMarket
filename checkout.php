<?php
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$cartItems = getCartItems();
$total = 0;
$totalQuantity = 0; 

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity']; 
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Создаем массив для товаров в формате JSON
        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'name' => $item['name'],        // Добавляем название товара
                'image' => $item['image']       // Добавляем изображение товара
            ];
        }
        
        // Конвертируем массив в JSON
        $productsJson = json_encode($orderItems);
        
        // Создание заказа с товарами в поле products
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, total_quantity, status, shipping_address, payment_method, products) VALUES (?, ?, ?, 'processing', ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $total,
            $totalQuantity,
            $_POST['shipping_address'],
            $_POST['payment_method'],
            $productsJson
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        // Очистка корзины
        unset($_SESSION['cart']);
        
        header("Location: /orders.php?order_id=$orderId");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка при оформлении заказа: " . $e->getMessage();
    }
}


require_once __DIR__ . '/includes/header.php';
?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Оформление заказа</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="bg-white p-8 rounded-lg shadow text-center">
            <h2 class="text-xl font-semibold mb-2">Ваша корзина пуста</h2>
            <a href="/products.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Детали заказа</h2>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($cartItems as $item): ?>
                            <li class="py-4 flex justify-between">
                                <div>
                                    <span class="font-medium"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="text-gray-600 text-sm">× <?= $item['quantity'] ?></span>
                                </div>
                                <span><?= number_format($item['price'] * $item['quantity'], 2) ?> ₽</span>
                            </li>
                        <?php endforeach; ?>
                        <li class="py-4 flex justify-between font-bold border-t">
                            <span>Итого</span>
                            <span><?= number_format($total, 2) ?> ₽</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div>
                <form method="post" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Данные покупателя</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Имя</label>
                        <input type="text" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" class="w-full px-4 py-2 border rounded" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" class="w-full px-4 py-2 border rounded" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Адрес доставки</label>
                        <textarea name="shipping_address" class="w-full px-4 py-2 border rounded" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Способ оплаты</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="cash" name="payment_method" value="cash" checked class="mr-2">
                                <label for="cash">Наличными при получении</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="card" name="payment_method" value="card" disabled class="mr-2">
                                <label for="card" class="text-gray-400">Банковской картой (недоступно)</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700">Подтвердить заказ</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>