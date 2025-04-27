<?php
require_once __DIR__ . '/../includes/functions.php';

// Проверка авторизации и прав администратора
if (!isAdmin()) {
    header("HTTP/1.1 403 Forbidden");
    exit('Доступ запрещен');
}

global $pdo;

// Подготовка данных для отчета
$reportTypes = [
    'sales' => 'Продажи',
    'products' => 'Товары',
    'customers' => 'Клиенты',
    'orders' => 'Заказы'
];

// Установка значений по умолчанию
$defaultType = 'products';
$defaultStartDate = date('Y-m-01');
$defaultEndDate = date('Y-m-d');
$defaultLimit = 10;

// Обработка параметров
try {
    // Получение и валидация типа отчета
    $reportType = $_GET['type'] ?? $defaultType;
    if (!array_key_exists($reportType, $reportTypes)) {
        $reportType = $defaultType;
        $_SESSION['flash_warning'] = "Выбран неверный тип отчета. Установлен отчет по продажам по умолчанию.";
    }

    // Получение и валидация дат
    $startDate = $_GET['start_date'] ?? $defaultStartDate;
    $endDate = $_GET['end_date'] ?? $defaultEndDate;

    if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
        throw new Exception("Неверный формат даты. Используйте формат ГГГГ-ММ-ДД.");
    }

    if ($startDate > $endDate) {
        throw new Exception("Начальная дата не может быть больше конечной.");
    }

    // Получение и валидация лимита
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit;
    $limit = max(1, min(100, $limit));

} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: /admin/reports.php");
    exit;
}

// Генерация данных отчета
$reportData = [];
$reportTitle = $reportTypes[$reportType];

try {
    switch ($reportType) {
        case 'sales':
            // Статистика продаж
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as orders_count,
                    SUM(total) as total_sales,
                    AVG(total) as avg_order
                FROM orders 
                WHERE status = 'completed' 
                AND created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $salesStats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Продажи по дням
            $stmt = $pdo->prepare("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as orders_count,
                    SUM(total) as total_sales
                FROM orders
                WHERE status = 'completed'
                AND created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at)
            ");
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $salesByDate = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reportData = [
                'stats' => $salesStats,
                'by_date' => $salesByDate
            ];
            break;

        case 'products':
            // Топ товаров из поля products
            $stmt = $pdo->prepare("
                    SELECT products
                    FROM orders
                    WHERE status = 'completed'
                    AND created_at BETWEEN ? AND ?
                ");
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $productSales = [];

            foreach ($orders as $order) {
                $products = json_decode($order['products'], true);

                if (is_array($products)) {
                    foreach ($products as $product) {
                        $productId = $product['id'];
                        if (!isset($productSales[$productId])) {
                            $productSales[$productId] = [
                                'id' => $productId,
                                'name' => $product['name'] ?? '',
                                'price' => $product['price'] ?? 0,
                                'total_quantity' => 0,
                                'total_sales' => 0,
                            ];
                        }
                        $productSales[$productId]['total_quantity'] += $product['quantity'];
                        $productSales[$productId]['total_sales'] += ($product['quantity'] * $product['price']);
                    }
                }
            }

            // Сортируем по количеству продаж
            usort($productSales, function ($a, $b) {
                return $b['total_quantity'] <=> $a['total_quantity'];
            });

            // Обрезаем до лимита
            $reportData = ['top_products' => array_slice($productSales, 0, $limit)];
            break;


        case 'customers':
            // Топ клиентов
            $sql = "
                    SELECT 
                        u.id,
                        u.name,
                        u.email,
                        COUNT(o.order_id) as orders_count,
                        SUM(o.total) as total_spent
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    WHERE o.status = 'completed'
                    AND o.created_at BETWEEN ? AND ?
                    GROUP BY u.id
                    ORDER BY total_spent DESC
                    LIMIT $limit
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reportData = ['top_customers' => $topCustomers];
            break;

        case 'orders':
            // Последние заказы
            $sql = "
                SELECT 
                    o.order_id  ,
                    o.total,
                    o.status,
                    o.created_at,
                    u.name as customer_name
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.created_at BETWEEN ? AND ?
                ORDER BY o.created_at DESC
                LIMIT $limit
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Статистика по статусам
            $stmt = $pdo->prepare("
                SELECT 
                    status,
                    COUNT(*) as orders_count,
                    SUM(total) as total_sales
                FROM orders
                WHERE created_at BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$startDate, $endDate . ' 23:59:59']);
            $ordersByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $reportData = [
                'recent_orders' => $recentOrders,
                'by_status' => $ordersByStatus
            ];
            break;
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "Ошибка БД: " . $e->getMessage();
    exit;
}


// Экспорт в CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="report_' . $reportType . '_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // Добавляем BOM для корректного отображения кириллицы в Excel
    fwrite($output, "\xEF\xBB\xBF");

    try {
        switch ($reportType) {
            case 'sales':
                fputcsv($output, ['Дата', 'Количество заказов', 'Сумма продаж'], ';');
                foreach ($reportData['by_date'] as $row) {
                    fputcsv($output, [
                        $row['date'],
                        $row['orders_count'],
                        $row['total_sales']
                    ], ';');
                }
                break;

            case 'products':
                fputcsv($output, ['ID', 'Название', 'Цена', 'Количество продаж', 'Сумма продаж'], ';');
                foreach ($reportData['top_products'] as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['name'],
                        $row['price'],
                        $row['total_quantity'],
                        $row['total_sales']
                    ], ';');
                }
                break;

            case 'customers':
                fputcsv($output, ['ID', 'Имя', 'Email', 'Количество заказов', 'Сумма покупок'], ';');
                foreach ($reportData['top_customers'] as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['name'],
                        $row['email'],
                        $row['orders_count'],
                        $row['total_spent']
                    ], ';');
                }
                break;

            case 'orders':
                fputcsv($output, ['ID', 'Клиент', 'Сумма', 'Статус', 'Дата заказа'], ';');
                foreach ($reportData['recent_orders'] as $row) {
                    fputcsv($output, [
                        $row['id'],
                        $row['customer_name'],
                        $row['total'],
                        $row['status'],
                        $row['created_at']
                    ], ';');
                }
                break;
        }
    } catch (Exception $e) {
        fclose($output);
        $_SESSION['flash_error'] = "Ошибка при экспорте данных";
        header("Location: /admin/reports.php");
        exit;
    }

    fclose($output);
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>



<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold">Отчеты</h1>

        <div class="mt-4 md:mt-0">
            <a href="/admin/reports.php?type=<?= $reportType ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&export=csv"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Экспорт в CSV
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="type" value="<?= $reportType ?>">

            <div>
                <label class="block text-gray-700 mb-2">Тип отчета</label>
                <select name="type" onchange="this.form.submit()" class="w-full px-4 py-2 border rounded">
                    <?php foreach ($reportTypes as $key => $name): ?>
                        <option value="<?= $key ?>" <?= $reportType === $key ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Начальная дата</label>
                <input type="date" name="start_date" value="<?= $startDate ?>" class="w-full px-4 py-2 border rounded">
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Конечная дата</label>
                <input type="date" name="end_date" value="<?= $endDate ?>" class="w-full px-4 py-2 border rounded">
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Лимит (шт)</label>
                <input type="number" name="limit" value="<?= $limit ?>" min="1" class="w-full px-4 py-2 border rounded">
            </div>

            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Сформировать
                </button>
                <a href="/admin/reports.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    Сбросить
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-semibold mb-4"><?= $reportTitle ?>
            <span class="text-lg font-normal text-gray-600">
                (<?= date('d.m.Y', strtotime($startDate)) ?> - <?= date('d.m.Y', strtotime($endDate)) ?>)
            </span>
        </h2>

        <?php if ($reportType === 'sales'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-gray-500 text-sm font-medium">Всего заказов</h3>
                    <p class="text-2xl font-bold mt-2"><?= $reportData['stats']['orders_count'] ?? 0 ?></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-gray-500 text-sm font-medium">Общая сумма</h3>
                    <p class="text-2xl font-bold mt-2"><?= number_format($reportData['stats']['total_sales'] ?? 0, 2) ?> ₽
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-gray-500 text-sm font-medium">Средний чек</h3>
                    <p class="text-2xl font-bold mt-2"><?= number_format($reportData['stats']['avg_order'] ?? 0, 2) ?> ₽</p>
                </div>
            </div>

            <h3 class="text-xl font-semibold mb-4">Продажи по дням</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Кол-во заказов</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма
                                продаж</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($reportData['by_date'])): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Нет данных за выбранный период</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData['by_date'] as $row): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d.m.Y', strtotime($row['date'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $row['orders_count'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= number_format($row['total_sales'], 2) ?> ₽</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($reportType === 'products'): ?>
            <h3 class="text-xl font-semibold mb-4">Топ <?= $limit ?> товаров</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товар
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Кол-во продаж</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма
                                продаж</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($reportData['top_products'])): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Нет данных за выбранный период</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData['top_products'] as $product): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= number_format($product['price'], 2) ?> ₽
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $product['total_quantity'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= number_format($product['total_sales'], 2) ?> ₽
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($reportType === 'customers'): ?>
            <h3 class="text-xl font-semibold mb-4">Топ <?= $limit ?> клиентов</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Клиент</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Кол-во заказов</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма
                                покупок</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($reportData['top_customers'])): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Нет данных за выбранный период</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData['top_customers'] as $customer): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customer['name']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($customer['email']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $customer['orders_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= number_format($customer['total_spent'], 2) ?> ₽
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($reportType === 'orders'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Последние <?= $limit ?> заказов</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Клиент</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Сумма</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Статус</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($reportData['recent_orders'])): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Нет данных за выбранный
                                            период</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reportData['recent_orders'] as $order): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="/admin/orders.php?action=edit&id=<?= $order['id'] ?>"
                                                    class="text-indigo-600 hover:underline">
                                                    #<?= $order['id'] ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($order['customer_name']) ?>
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
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-4">Статистика по статусам</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Статус</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Кол-во</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Сумма</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($reportData['by_status'])): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Нет данных за выбранный
                                            период</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reportData['by_status'] as $status): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?= ucfirst($status['status']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= $status['orders_count'] ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= number_format($status['total_sales'], 2) ?> ₽
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>