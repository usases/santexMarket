<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mx-auto px-4 py-10">
    <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h1 class="text-4xl font-extrabold mb-6 text-indigo-700 text-center">О нас</h1>

        <div class="text-gray-700 text-lg leading-relaxed space-y-6">
            <p>
                Добро пожаловать в <strong>SantexMarket</strong> — ваш надежный интернет-магазин сантехники. Мы предлагаем широкий ассортимент качественных товаров по доступным ценам.
                Наша миссия — сделать ваш выбор простым, быстрым и выгодным.
            </p>

            <p>
                Мы сотрудничаем только с проверенными поставщиками, чтобы гарантировать высокое качество продукции. Каждый товар перед добавлением в каталог проходит проверку на соответствие стандартам.
            </p>

            <p>
                Наша команда специалистов всегда готова проконсультировать вас, помочь с выбором и обеспечить высокий уровень сервиса на всех этапах покупки.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-indigo-600">Наши преимущества</h2>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Большой выбор современных моделей</li>
                        <li>Регулярные скидки и акции</li>
                        <li>Быстрая доставка по всей России</li>
                        <li>Удобные способы оплаты</li>
                        <li>Поддержка клиентов 7 дней в неделю</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-2xl font-bold mb-4 text-indigo-600">Контактная информация</h2>
                    <ul class="space-y-2">
                        <li><strong>Адрес:</strong> г. Воронеж, ул. Театральная, д. 1</li>
                        <li><strong>Телефон:</strong> <a href="tel:+7 (999) 999-99-99" class="text-indigo-600 hover:underline">+7 (999) 999-99-99</a></li>
                        <li><strong>Email:</strong> <a href="mailto:info@santexmarket.ru" class="text-indigo-600 hover:underline">support@example.com</a></li>
                        <li><strong>Время работы:</strong> Пн–Вс, 9:00–21:00</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 bg-indigo-50 border-l-4 border-indigo-400 p-6 rounded-xl">
                <h3 class="text-xl font-semibold text-indigo-700 mb-2">Нам доверяют более 10 000 клиентов</h3>
                <p class="text-gray-700">
                    Спасибо, что выбираете нас! Мы ценим каждого клиента и стремимся делать всё возможное для вашего комфорта.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
