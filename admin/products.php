<?php
require_once __DIR__ . '/../includes/functions.php';


if (!isAdmin()) {
    header("Location: /");
    exit;
}

global $pdo;
// Обработка добавления новой категории
if (isset($_GET['action']) && $_GET['action'] === 'add_category' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['category_name']);
    if (!empty($categoryName)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$categoryName]);
            $_SESSION['flash_message'] = "Категория успешно добавлена";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Ошибка при добавлении категории: " . $e->getMessage();
        }
    } else {
        $_SESSION['flash_error'] = "Название категории не может быть пустым";
    }
    header("Location: /admin/products.php");
    exit;
}

// Обработка удаления категории
if (isset($_GET['action']) && $_GET['action'] === 'delete_category' && isset($_GET['id'])) {
    $categoryId = (int) $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $_SESSION['flash_message'] = "Категория успешно удалена";
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Ошибка при удалении категории: " . $e->getMessage();
    }
    header("Location: /admin/products.php");
    exit;
}


// Обработка добавления/редактирования товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryId = $_POST['category_id'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    try {
        if ($id) {
            // Редактирование существующего товара
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $categoryId, $stock, $image, $id]);
            $message = "Товар успешно обновлен";
        } else {
            // Добавление нового товара
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $categoryId, $stock, $image]);
            $message = "Товар успешно добавлен";
        }

        $_SESSION['flash_message'] = $message;
        header("Location: /admin/products.php");
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка при сохранении товара: " . $e->getMessage();
    }
}

// Обработка удаления товара
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $productId = (int) $_GET['id']; // Безопаснее привести к целому числу

        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$productId]);

            $_SESSION['flash_message'] = "Товар успешно удален";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Ошибка при удалении товара: " . $e->getMessage();
        }

        header("Location: /admin/products.php");
        exit;
    }
}

// Получение списка товаров
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($category) && $category !== 'all') {
    $sql .= " AND p.category_id = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение списка категорий
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
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

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold">Управление товарами</h1>

        <div class="mt-4 md:mt-0">
            <a href="/admin/products.php?action=add"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Добавить товар
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Поиск</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Название или описание" class="w-full px-4 py-2 border rounded">
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Категория</label>
                <select name="category" class="w-full px-4 py-2 border rounded">
                    <option value="all" <?= empty($category) || $category === 'all' ? 'selected' : '' ?>>Все категории
                    </option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 mr-2">
                    Применить
                </button>
                <a href="/admin/products.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    Сбросить
                </a>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
        <?php
        $product = null;
        if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-bold">
                    <?= $_GET['action'] === 'add' ? 'Добавление товара' : 'Редактирование товара' ?>
                </h2>
                <a href="/admin/products.php" class="text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <form method="post">
                <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Название товара</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Описание</label>
                            <textarea name="description" rows="4" required
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Категория</label>
                            <select name="category_id" required
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= isset($product['category_id']) && $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Цена (₽)</label>
                            <input type="number" name="price" step="0.01" min="0" value="<?= $product['price'] ?? '' ?>"
                                required
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Количество на складе</label>
                            <input type="number" name="stock" min="0" value="<?= $product['stock'] ?? 0 ?>"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Изображение (URL)</label>
                            <input type="url" name="image" value="<?= htmlspecialchars($product['image'] ?? '') ?>"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <?php if (!empty($product['image'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Предпросмотр"
                                        class="h-32 object-contain">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                        <?= $_GET['action'] === 'add' ? 'Добавить товар' : 'Сохранить изменения' ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Категории</h2>
            <button onclick="document.getElementById('addCategoryForm').classList.toggle('hidden')"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Добавить категорию
            </button>
        </div>

        <div id="addCategoryForm" class="hidden mb-6">
            <form method="post" action="/admin/products.php?action=add_category" class="flex items-center space-x-4">
                <input type="text" name="category_name" required placeholder="Название категории"
                    class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Сохранить
                </button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= htmlspecialchars($cat['name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="/admin/products.php?action=delete_category&id=<?= $cat['id'] ?>"
                                onclick="return confirm('Удалить категорию? Будьте осторожны: товары в ней останутся без категории.')"
                                class="text-red-600 hover:text-red-900">
                                Удалить
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Товар
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Категория</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">На
                            складе</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Товары не найдены
                            </td>
                        </tr>
                    <?php else: ?>
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
                                                <?= htmlspecialchars($product['name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= substr(htmlspecialchars($product['description']), 0, 50) ?>...
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $product['stock'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/admin/products.php?action=edit&id=<?= $product['id'] ?>"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Изменить</a>
                                    <a href="/admin/products.php?action=delete&id=<?= $product['id'] ?>"
                                        onclick="return confirm('Вы уверены, что хотите удалить этот товар?')"
                                        class="text-red-600 hover:text-red-900">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>