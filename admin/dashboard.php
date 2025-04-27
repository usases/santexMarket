<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

if (!isAdmin()) {
    header("Location: /");
    exit;
}

// Получение статистики
global $pdo;

// Количество заказов
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$ordersCount = $stmt->fetchColumn();

// Количество пользователей
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$usersCount = $stmt->fetchColumn();

// Количество товаров
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$productsCount = $stmt->fetchColumn();

// Доход
$stmt = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'");
$revenue = $stmt->fetchColumn();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Админ-панель</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Всего заказов</h3>
            <p class="text-2xl font-bold mt-2"><?= $ordersCount ?></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Пользователи</h3>
            <p class="text-2xl font-bold mt-2"><?= $usersCount ?></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Товары</h3>
            <p class="text-2xl font-bold mt-2"><?= $productsCount ?></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Доход</h3>
                <p class="text-2xl font-bold mt-2"><?= number_format($revenue ?: 0, 2) ?> ₽</p>
            </div>
            <a href="/admin/reports.php" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                Отчетность
            </a>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Последние заказы</h2>
            <?php
            $stmt = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY created_at DESC LIMIT 5");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Сумма</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Статус</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="/admin/orders.php?action=edit&id=<?= $order['order_id'] ?>"
                                        class="text-indigo-600 hover:underline">
                                        #<?= $order['order_id'] ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($order['user_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($order['total'], 2) ?> ₽
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                            ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="/admin/orders.php" class="text-indigo-600 hover:underline">Все заказы →</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Недавние товары</h2>
            <?php
            $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Категория</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Цена</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="<?= htmlspecialchars($product['image']) ?>"
                                                alt="<?= htmlspecialchars($product['name']) ?>">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="/admin/products.php?action=edit&id=<?= $product['id'] ?>"
                                                    class="text-indigo-600 hover:underline">
                                                    <?= htmlspecialchars($product['name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($product['price'], 2) ?> ₽
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="/admin/products.php" class="text-indigo-600 hover:underline">Все товары →</a>
            </div>
        </div>
    </div>
</div>