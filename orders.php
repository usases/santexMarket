<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$orders = getOrderHistory($_SESSION['user_id']);
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Мои заказы</h1>
    
    <?php if (isset($_GET['order_id'])): ?>
        <?php 
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$_GET['order_id'], $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order): 
            // Декодируем данные товаров из JSON
            $items = json_decode($order['products'], true);
        ?>
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold">Заказ #<?= $order['order_id'] ?></h2>
                        <p class="text-gray-600"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                
                <div class="mb-6">
                    <h3 class="font-medium mb-2">Адрес доставки</h3>
                    <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                </div>
                
                <div class="mb-6">
                    <h3 class="font-medium mb-2">Способ оплаты</h3>
                    <p><?= $order['payment_method'] === 'cash' ? 'Наличными при получении' : 'Банковской картой' ?></p>
                </div>
                
                <div class="border-t pt-4 mb-6">
                    <h3 class="font-medium mb-4">Товары</h3>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                            <li class="py-4 flex">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img class="h-16 w-16 rounded object-cover" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="ml-4 flex-grow">
                                    <div class="flex justify-between">
                                        <div>
                                            <h4 class="text-gray-900"><?= htmlspecialchars($item['name']) ?></h4>
                                            <p class="text-gray-600 text-sm">× <?= $item['quantity'] ?></p>
                                        </div>
                                        <p class="text-gray-900"><?= number_format($item['price'] * $item['quantity'], 2) ?> ₽</p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Итого:</span>
                        <span><?= number_format($order['total'], 2) ?> ₽</span>
                    </div>
                </div>
            </div>
            
            <a href="/orders.php" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 mb-8">
                ← Вернуться к списку заказов
            </a>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h2 class="text-xl font-semibold mb-2">Заказ не найден</h2>
                <a href="/orders.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 mt-4">
                    Вернуться к списку заказов
                </a>
            </div>
        <?php endif; ?>
    <?php elseif (empty($orders)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h2 class="text-xl font-semibold mb-2">У вас пока нет заказов</h2>
            <p class="text-gray-600 mb-4">После оформления заказа вы сможете отслеживать его статус здесь</p>
            <a href="/products.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Номер заказа</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товары</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orders as $order): 
                        // Декодируем товары для подсчета их количества
                        $items = json_decode($order['products'], true);
                        $totalQuantity = array_sum(array_column($items, 'quantity'));
                    ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #<?= $order['order_id'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d.m.Y', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $totalQuantity ?> шт.
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= number_format($order['total'], 2) ?> ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/orders.php?order_id=<?= $order['order_id'] ?>" class="text-indigo-600 hover:text-indigo-900">Подробнее</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

