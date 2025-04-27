<?php
require_once __DIR__ . '/includes/functions.php';


// Если уже вошли — редиректим
if (isset($_SESSION['user_id'])) {
    header("Location: /");
    exit;
}

// Если отправлена форма — обрабатываем
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $pdo;
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Успешный логин -> редирект
        header("Location: /");
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}

require_once __DIR__ . '/includes/header.php';
?>


<div class="max-w-md mx-auto my-10 bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Вход в аккаунт</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" required 
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div class="mb-6">
            <label for="password" class="block text-gray-700 mb-2">Пароль</label>
            <input type="password" id="password" name="password" required 
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Войти
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600">Нет аккаунта? <a href="/register.php" class="text-indigo-600 hover:underline">Зарегистрируйтесь</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>