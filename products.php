<?php
require_once __DIR__ . '/includes/functions.php';

session_start();

// Обработка добавления товара в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'], $_POST['quantity'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }

    $productId = (int) $_POST['add_to_cart'];
    $quantity = max(1, (int) $_POST['quantity']);

    addToCart($productId, $quantity);

    header("Location: /cart.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';

// Параметры фильтрации
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? '';

$products = getProducts($search, $category, $minPrice, $maxPrice, $sort);
$categories = getCategories();
?>

<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold my-8">Каталог товаров</h1>

    <!-- Панель фильтров -->
    <form method="get" class="bg-white p-6 rounded-lg shadow mb-8 flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block mb-1 text-gray-700">Поиск</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Введите название..."
                class="w-full px-4 py-2 border rounded">
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block mb-1 text-gray-700">Категория</label>
            <select name="category" class="w-full px-4 py-2 border rounded">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex min-w-[150px] flex-col">
            <label class="block mb-1 text-gray-700">Цена от</label>
            <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" placeholder="0"
                class="px-4 py-2 border rounded">
        </div>

        <div class="flex min-w-[150px] flex-col">
            <label class="block mb-1 text-gray-700">Цена до</label>
            <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" placeholder="10000"
                class="px-4 py-2 border rounded">
        </div>

        <div class="flex min-w-[150px] flex-col">
            <label class="block mb-1 text-gray-700">Сортировка</label>
            <select name="sort" class="px-4 py-2 border rounded">
                <option value="">Без сортировки</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>По возрастанию цены</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>По убыванию цены</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>По названию (А-Я)</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>По названию (Я-А)</option>
            </select>
        </div>

        <div class="flex min-w-[300px] flex-row items-end gap-2">
            <div class="flex-1">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Применить
                </button>
            </div>
            <div class="flex-1">
                <a href=""
                    class="w-full block text-center bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                    Сбросить
                </a>
            </div>
        </div>

    </form>

    <!-- Сетка товаров -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($products)): ?>
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">Товары не найдены</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="/productCart.php?id=<?= $product['id'] ?>">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                            class="w-full h-60 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="/productCart.php?id=<?= $product['id'] ?>">
                            <h3 class="font-semibold text-lg mb-2 hover:text-indigo-600">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                        </a>
                        <p class="text-gray-600 mb-4">На складе: <?= (int)$product['stock'] ?> шт.</p>
                        <div class="font-bold text-indigo-600 mb-2">
                            <?= number_format($product['price'], 2) ?> ₽
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>