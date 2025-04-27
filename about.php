<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mx-auto px-4 py-10">
    <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h1 class="text-4xl font-extrabold mb-6 text-indigo-700 text-center">О нас</h1>

        <div class="text-gray-700 text-lg leading-relaxed space-y-6">
            <p>
                Добро пожаловать в наш магазин! Мы предлагаем широкий ассортимент товаров высокого качества по доступным ценам. 
                Наша цель — сделать процесс покупки максимально удобным, приятным и быстрым для вас.
            </p>

            <p>
                Мы сотрудничаем только с проверенными поставщиками и внимательно следим за качеством продукции. 
                Каждый товар проходит строгий контроль перед тем, как попасть в наш каталог.
            </p>

            <p>
                Наша команда всегда готова помочь вам с выбором, ответить на вопросы и обеспечить отличный сервис.
                Спасибо, что выбираете нас!
            </p>

            <div class="flex flex-col md:flex-row gap-6 mt-8">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-4 text-indigo-600">Наши преимущества</h2>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Большой выбор товаров</li>
                        <li>Доступные цены и акции</li>
                        <li>Быстрая доставка</li>
                        <li>Профессиональная поддержка</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
