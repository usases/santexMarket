<?php
require_once __DIR__ . '/includes/functions.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

global $pdo;

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    try {
        // Проверяем текущий пароль, если меняется email или пароль
        if (!empty($newPassword)) { 
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                throw new Exception("Текущий пароль введен неверно");
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception("Новый пароль и подтверждение не совпадают");
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Обновляем данные пользователя
        if (!empty($newPassword)) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $hashedPassword, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
        }

        // Обновляем данные в сессии
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        $_SESSION['flash_message'] = "Профиль успешно обновлен";
        header("Location: /profile.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

}

// Получаем текущие данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем историю заказов
$orders = getOrderHistory($_SESSION['user_id']);
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto py-8">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <?php unset($_SESSION['flash_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-8">Мой профиль</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Боковая панель -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col items-center">
                    <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>

                    <div class="mt-6 w-full">
                        <a href="/orders.php"
                            class="block text-center bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 mb-2">
                            Мои заказы
                        </a>
                        <?php if (isAdmin()): ?>
                            <a href="/admin/dashboard.php"
                                class="block text-center bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                                Админ-панель
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Основное содержимое -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Личные данные</h2>

                <form method="post">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Имя</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-3">Смена пароля</h3>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Текущий пароль</label>
                            <input type="password" name="current_password"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Новый пароль</label>
                            <input type="password" name="new_password"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Подтвердите новый пароль</label>
                            <input type="password" name="confirm_password"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" name="update_profile"
                            class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Последние заказы</h2>

                <?php if (empty($orders)): ?>
                    <p class="text-gray-600">У вас пока нет заказов</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">Заказ #<?= $order['order_id'] ?></h3>
                                        <p class="text-sm text-gray-600"><?= date('d.m.Y', strtotime($order['created_at'])) ?>
                                        </p>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm 
                                        <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                            ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                                <div class="mt-2 flex justify-between">
                                    <span><?= $order['total_quantity'] ?> товар(ов)</span>
                                    <span class="font-medium"><?= number_format($order['total'], 2) ?> ₽</span>
                                </div>
                                <div class="mt-3 text-right">
                                    <a href="/orders.php?order_id=<?= $order['order_id'] ?>"
                                        class="text-indigo-600 hover:underline text-sm">
                                        Подробнее →
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="/orders.php" class="text-indigo-600 hover:underline">
                            Все заказы →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>