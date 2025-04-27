<?php
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: /");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $pdo;
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$name, $email, $password]);
        
        $userId = $pdo->lastInsertId();
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'user';
        
        header("Location: /");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Пользователь с таким email уже существует";
        } else {
            $error = "Ошибка при регистрации: " . $e->getMessage();
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-10 bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Регистрация</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 mb-2">Имя</label>
            <input type="text" id="name" name="name" required 
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        
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
            Зарегистрироваться
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600">Уже есть аккаунт? <a href="/login.php" class="text-indigo-600 hover:underline">Войдите</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>