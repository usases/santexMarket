<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$products = getProducts();
$sliderImages = array_slice(getSliderImages(), 0, 3); // Оставляем только 3 изображения
?>

<div class="container mx-auto px-4 py-10">
    <!-- Название магазина -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4 text-indigo-700">SantexMarket</h1>
        <p class="text-gray-600 text-xl">Интернет-магазин качественной сантехники</p>
    </div>

    <!-- Слайдер -->
    <div class="relative overflow-hidden rounded-2xl shadow-lg mb-16">
        <div class="slider-container flex transition-transform duration-500 ease-in-out" id="slider">
            <?php foreach ($sliderImages as $image): ?>
                <img src="<?= htmlspecialchars($image) ?>" class="w-full flex-shrink-0 object-cover h-96" alt="Слайд">
            <?php endforeach; ?>
        </div>
        <button id="prev"
            class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 p-3 rounded-full shadow-md">&#10094;</button>
        <button id="next"
            class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 p-3 rounded-full shadow-md">&#10095;</button>
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
            <?php for ($i = 0; $i < count($sliderImages); $i++): ?>
                <div class="slider-dot w-3 h-3 rounded-full bg-gray-300 cursor-pointer"></div>
            <?php endfor; ?>
        </div>
    </div>
    <?php renderSliderScript(); ?>
    <!-- Акции -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">🔥 Акции недели 🔥</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-2xl p-8 shadow-lg">
                <h3 class="text-2xl font-bold mb-2">Скидка 20% на душевые кабины</h3>
                <p class="text-white text-sm mb-4">До конца недели</p>
                <a href="/products.php?promo=showers"
                    class="inline-block bg-white text-indigo-700 font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition">Подробнее</a>
            </div>
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-2xl p-8 shadow-lg">
                <h3 class="text-2xl font-bold mb-2">Бесплатная доставка от 5000 ₽</h3>
                <p class="text-white text-sm mb-4">По всей России</p>
                <a href="/products.php"
                    class="inline-block bg-white text-green-700 font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition">Подробнее</a>
            </div>
            <div class="bg-gradient-to-r from-pink-500 to-pink-700 text-white rounded-2xl p-8 shadow-lg">
                <h3 class="text-2xl font-bold mb-2">Подарок при покупке ванны</h3>
                <p class="text-white text-sm mb-4">Набор аксессуаров бесплатно</p>
                <a href="/products.php?promo=bath"
                    class="inline-block bg-white text-pink-700 font-semibold px-5 py-2 rounded-full hover:bg-gray-100 transition">Подробнее</a>
            </div>
        </div>
    </section>

    <!-- Популярные товары -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Популярные товары</h2>
            <a href="/products.php"
                class="bg-indigo-600 text-white px-6 py-3 rounded-full hover:bg-indigo-700 transition">
                Смотреть всё
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($products, 0, 3) as $product): ?>
                <div class="relative bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
                    <!-- Бейдж "Популярное" -->
                    <div
                        class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                        Популярное
                    </div>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                        class="w-full h-56 object-cover">
                    <div class="p-5 flex flex-col justify-between h-56">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 mb-2"><?= htmlspecialchars($product['name']) ?>
                            </h3>
                            <p class="text-gray-500 text-sm mb-4">
                                <?= htmlspecialchars(mb_substr($product['description'], 0, 80)) ?>...</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-600 text-lg"><?= number_format($product['price'], 2) ?>
                                ₽</span>
                            <a href="/products.php?add_to_cart=<?= $product['id'] ?>"
                                class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                В корзину
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>