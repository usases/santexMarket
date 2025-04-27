<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mx-auto px-4 py-10">
    <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h1 class="text-4xl font-extrabold mb-6 text-indigo-700 text-center">Свяжитесь с нами</h1>

        <div class="text-gray-700 text-lg leading-relaxed space-y-6 mb-10 text-center max-w-2xl mx-auto">
            <p>У вас есть вопросы или предложения? Мы всегда рады помочь!</p>
            <p>Заполните форму ниже или свяжитесь с нами напрямую через указанные контакты.</p>
        </div>

        <div class="flex flex-col md:flex-row gap-10">
            <!-- Форма обратной связи -->
            <div class="flex-1">
                <form action="#" method="post" class="space-y-6">
                    <div>
                        <label class="block mb-2 font-semibold text-gray-600" for="name">Ваше имя</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold text-gray-600" for="email">Ваш Email</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold text-gray-600" for="message">Сообщение</label>
                        <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                        Отправить сообщение
                    </button>
                </form>
            </div>

            <!-- Контактная информация -->
            <div class="flex-1">
                <div class="space-y-6 text-gray-700 text-lg">
                    <div>
                        <h2 class="text-2xl font-bold text-indigo-600 mb-2">Контакты</h2>
                        <p><strong>Email:</strong> support@example.com</p>
                        <p><strong>Телефон:</strong> +7 (999) 999-99-99</p>
                        <p><strong>Адрес:</strong> г. Москва</p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-indigo-600 mb-2">Часы работы</h2>
                        <p>Пн - Пт: 9:00 — 18:00</p>
                        <p>Сб - Вс: Выходные</p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-indigo-600 mb-2">Мы в соцсетях</h2>
                        <div class="flex space-x-4 mt-2">
                            <a href="#" class="text-indigo-600 hover:text-indigo-800">Telegram</a>
                            <a href="#" class="text-indigo-600 hover:text-indigo-800">Instagram</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
