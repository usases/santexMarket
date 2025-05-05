</main>
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-6 md:mb-0">
                    <h3 class="text-xl font-bold mb-4">Магазин SantexMarket</h3>
                    <p>Лучшие товары по лучшим ценам</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Меню</h4>
                        <ul class="space-y-2">
                            <li><a href="/" class="hover:text-indigo-400">Главная</a></li>
                            <li><a href="/products.php" class="hover:text-indigo-400">Товары</a></li>
                            <li><a href="/about.php" class="hover:text-indigo-400">О нас</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Помощь</h4>
                        <ul class="space-y-2">
                            <li><a href="/contact.php" class="hover:text-indigo-400">Контакты</a></li>
                            <li><a href="/faq.php" class="hover:text-indigo-400">FAQ</a></li>
                            <li><a href="/terms.php" class="hover:text-indigo-400">Условия</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Контакты</h4>
                        <address>
                            <p>г. Воронеж, ул. Театральная, д. 1</p>
                            <p>Email: support@example.com</p>
                            <p>Телефон: +7 (999) 999-99-99</p>
                        </address>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p>&copy; <?= date('Y') ?> Все права защищены.</p>
            </div>
        </div>
    </footer>
</body>
</html>