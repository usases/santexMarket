<?php
require_once __DIR__ . '/includes/functions.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}


$cartItems = getCartItems();
$total = 0;

// Обработка обновления корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantity'] as $productId => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }
    header("Location: /cart.php");
    exit;
}

// Обработка удаления товара
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: /cart.php");
    exit;
}
require_once __DIR__ . '/includes/header.php';
?>


<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Корзина</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="bg-white p-8 rounded-lg shadow text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="text-xl font-semibold mb-2">Ваша корзина пуста</h2>
            <p class="text-gray-600 mb-4">Добавьте товары, чтобы продолжить</p>
            <a href="/products.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <form method="post">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товар</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Количество</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Итого</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($cartItems as $item): ?>
                            <?php $itemTotal = $item['price'] * $item['quantity']; ?>
                            <?php $total += $itemTotal; ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($item['price'], 2) ?> ₽
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="w-20 px-2 py-1 border rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= number_format($itemTotal, 2) ?> ₽
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/cart.php?remove=<?= $item['id'] ?>" class="text-red-600 hover:text-red-900">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-between items-center mb-6">
                <button type="submit" class="bg-gray-200 text-gray-800 px-6 py-2 rounded hover:bg-gray-300">Обновить корзину</button>
                <a href="/products.php" class="text-indigo-600 hover:underline">Продолжить покупки</a>
            </div>
        </form>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Итого</h2>
            <div class="space-y-2 mb-6">
                <div class="flex justify-between">
                    <span>Промежуточный итог</span>
                    <span><?= number_format($total, 2) ?> ₽</span>
                </div>
                <div class="flex justify-between">
                    <span>Доставка</span>
                    <span>Бесплатно</span>
                </div>
                <div class="flex justify-between border-t pt-2 mt-2 font-bold text-lg">
                    <span>Всего</span>
                    <span><?= number_format($total, 2) ?> ₽</span>
                </div>
            </div>
            <a href="/checkout.php" class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded hover:bg-indigo-700">Оформить заказ</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>