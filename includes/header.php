<?php
require_once __DIR__ . '/functions.php';
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SantexMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-gray-100">
    <header class="bg-white shadow relative z-20">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Логотип -->
            <a href="/" class="flex items-center">
                <img src="/assets/images/logo2.jpg" alt="Логотип" class="h-10 w-10 object-cover">
            </a>

            <!-- Десктоп версия -->
            <div class="hidden md:flex items-center justify-between w-full">
                <div class="flex items-center space-x-8">
                    <!-- Название -->
                    <span class="text-2xl font-bold text-indigo-600">SantexMarket</span>

                    <!-- Каталог -->
                    <div class="relative group">
                        <button
                            class="flex items-center text-indigo-600 font-semibold px-4 py-2 rounded hover:bg-indigo-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h4M4 10h4M4 14h4M4 18h4M10 6h4M10 10h4M10 14h4M10 18h4M16 6h4M16 10h4M16 14h4M16 18h4" />
                            </svg>
                            <span>Каталог товаров</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            class="absolute top-full left-0 mt-2 w-64 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-30">
                            <ul class="py-2">
                                <li><a href="/products.php"
                                        class="block px-4 py-2 hover:bg-indigo-100 text-gray-800 font-medium">Все
                                        товары</a></li>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="/products.php?category=<?= $category['id'] ?>"
                                            class="block px-4 py-2 hover:bg-indigo-100 text-gray-700 font-medium">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Навигация и действия -->
                <nav class="flex items-center space-x-6">
                    <a href="/about.php" class="text-gray-700 hover:text-indigo-600">О нас</a>
                    <a href="/contact.php" class="text-gray-700 hover:text-indigo-600">Контакты</a>
                    <a href="/faq.php" class="text-gray-700 hover:text-indigo-600">FAQ</a>
                    <a href="/terms.php" class="text-gray-700 hover:text-indigo-600">Условия</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <!-- Поиск -->
                    <form action="/products.php" method="get" class="flex">
                        <input type="text" name="search" placeholder="Поиск..." class="px-4 py-2 border rounded-l">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-r">Найти</button>
                    </form>

                    <!-- Корзина -->
                    <a href="/cart.php" class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <span
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?= array_sum($_SESSION['cart']) ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Профиль -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative group">
                            <button class="flex items-center space-x-2">
                                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Профиль</a>
                                <a href="/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Мои заказы</a> 
                                <?php if (isAdmin()): ?>
                                    <a href="/admin/dashboard.php"
                                        class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Админ-панель</a>
                                <?php endif; ?>
                                <a href="/logout.php" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Выйти</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Мобильная версия -->
            <div class="md:hidden flex items-center space-x-4">
                <a href="/cart.php" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?= array_sum($_SESSION['cart']) ?>
                        </span>
                    <?php endif; ?>
                </a>
                <button onclick="toggleMobileMenu()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Мобильное меню -->
        <div id="mobileMenu" class="md:hidden hidden px-4 pb-4">
            <!-- Категории -->
            <div class="border-b py-2">
                <a href="/products.php" class="text-indigo-600 font-semibold block">Каталог товаров</a>
                <?php foreach ($categories as $category): ?>
                    <a href="/products.php?category=<?= $category['id'] ?>" class="text-gray-600 pl-4 block">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Страницы -->
            <div class="border-b py-2">
                <a href="/about.php" class="text-gray-700 block">О нас</a>
                <a href="/contact.php" class="text-gray-700 block">Контакты</a>
                <a href="/faq.php" class="text-gray-700 block">FAQ</a>
                <a href="/terms.php" class="text-gray-700 block">Условия</a>
            </div>

            <!-- Личный кабинет -->
            <div class="py-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/profile.php" class="text-gray-700 block">Профиль</a>
                    <?php if (isAdmin()): ?>
                        <a href="/admin/dashboard.php" class="text-gray-700 block">Админ-панель</a>
                    <?php endif; ?>
                    <a href="/logout.php" class="text-gray-700 block">Выйти</a>
                <?php else: ?>
                    <a href="/login.php" class="text-gray-700 block">Войти</a>
                    <a href="/register.php"
                        class="bg-indigo-600 text-white px-4 py-2 rounded text-center mt-2 block">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">