<?php
require_once __DIR__ . '/includes/functions.php';
session_start();

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

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($productId);

if (!$product): ?>
    <div class="container mx-auto px-4 py-12 text-center">
        <h1 class="text-2xl font-bold text-gray-700">Товар не найден</h1>
    </div>
<?php else: ?>
    <div class="container mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
        <div>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                class="w-full rounded-lg shadow">
        </div>
        <div>
            <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-gray-600 mb-6"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <div class="text-2xl font-semibold text-indigo-600 mb-6">
                <?= number_format($product['price'], 2) ?> ₽
            </div>
            <form method="post" class="flex items-center gap-4">
                <input type="hidden" name="add_to_cart" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1"
                    class="w-20 px-3 py-2 border rounded text-center">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                    В корзину
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
