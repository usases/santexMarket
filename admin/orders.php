<?php 
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    header("Location: /");
    exit;
}

global $pdo;

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$newStatus, $orderId]);

    $_SESSION['flash_message'] = "Статус заказа #$orderId успешно обновлен";
    header("Location: /admin/orders.php");
    exit;
}

// Обработка удаления заказа
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->execute([$orderId]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Заказ #$orderId успешно удален";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Ошибка при удалении заказа: " . $e->getMessage();
    }

    header("Location: /admin/orders.php");
    exit;
}

// Получение списка заказов
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (o.order_id = :search OR u.name LIKE :searchlike OR u.email LIKE :searchlike)";
    if (is_numeric($search)) {
        $params[':search'] = $search;
        $params[':searchlike'] = "%$search%";
    } else {
        $params[':search'] = 0;
        $params[':searchlike'] = "%$search%";
    }
}

if (!empty($status) && $status !== 'all') {
    $sql .= " AND o.status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Статусы заказов
$statuses = [
    'processing' => 'В процессе',
    'completed' => 'Завершен',
    'cancelled' => 'Отменен'
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <?php unset($_SESSION['flash_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-6">Управление заказами</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-2">Поиск</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="w-full border px-4 py-2 rounded" placeholder="ID заказа, имя или email">
            </div>
            <div>
                <label class="block mb-2">Статус</label>
                <select name="status" class="w-full border px-4 py-2 rounded">
                    <option value="all" <?= ($status === '' || $status === 'all') ? 'selected' : '' ?>>Все</option>
                    <?php foreach ($statuses as $key => $name): ?>
                        <option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 mr-2">Применить</button>
                <a href="/admin/orders.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Сбросить</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">Заказы не найдены</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4">#<?= $order['order_id'] ?></td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($order['user_name']) ?><br>
                                    <small class="text-gray-500"><?= htmlspecialchars($order['user_email']) ?></small>
                                </td>
                                <td class="px-6 py-4"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                <td class="px-6 py-4"><?= number_format($order['total'], 2) ?> ₽</td>
                                <td class="px-6 py-4">
                                    <form method="post">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                            <?php foreach ($statuses as $key => $name): ?>
                                                <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_status" class="hidden">Сохранить</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/admin/orders.php?action=view&id=<?= $order['order_id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-2">Просмотр</a>
                                    <a href="/admin/orders.php?action=edit&id=<?= $order['order_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-2">Изменить</a>
                                    <a href="/admin/orders.php?action=delete&id=<?= $order['order_id'] ?>" onclick="return confirm('Удалить заказ?')" class="text-red-600 hover:text-red-900">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    if (isset($_GET['action']) && in_array($_GET['action'], ['view', 'edit']) && isset($_GET['id'])):
        $orderId = (int)$_GET['id'];
        $stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.order_id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order):
            $products = json_decode($order['products'], true);
    ?>
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-2xl font-bold mb-4">Заказ #<?= $order['order_id'] ?></h2>
                <p><strong>Клиент:</strong> <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['user_email']) ?>)</p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($statuses[$order['status']] ?? $order['status']) ?></p>
                <p><strong>Дата заказа:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                <p><strong>Сумма заказа:</strong> <?= number_format($order['total'], 2) ?> ₽</p>
                <p><strong>Адрес доставки:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>

                <?php if (!empty($products)): ?>
                    <h3 class="text-xl font-bold mt-6 mb-2">Товары:</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Количество</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="px-6 py-4"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="px-6 py-4"><?= (int)$product['quantity'] ?></td>
                                    <td class="px-6 py-4"><?= number_format($product['price'], 2) ?> ₽</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
    <?php
        endif;
    endif;
    ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
