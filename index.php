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
    <div class="relative overflow-hidden rounded-xl shadow-md mb-10">
        <div class="slider-container flex transition-transform duration-500 ease-in-out" id="slider">
            <?php foreach ($sliderImages as $image): ?>
                <img src="<?= htmlspecialchars($image) ?>"
                    class="w-full flex-shrink-0 object-cover h-44 sm:h-60 md:h-72 lg:h-80 transition-all duration-300"
                    alt="Слайд">
            <?php endforeach; ?>
        </div>
        <button id="prev"
            class="absolute top-1/2 left-1 sm:left-3 transform -translate-y-1/2 bg-white bg-opacity-60 hover:bg-opacity-90 p-1.5 sm:p-2 rounded-full shadow text-base sm:text-xl">
            &#10094;
        </button>
        <button id="next"
            class="absolute top-1/2 right-1 sm:right-3 transform -translate-y-1/2 bg-white bg-opacity-60 hover:bg-opacity-90 p-1.5 sm:p-2 rounded-full shadow text-base sm:text-xl">
            &#10095;
        </button>
        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-1.5 sm:gap-2">
            <?php for ($i = 0; $i < count($sliderImages); $i++): ?>
                <div class="slider-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-gray-300 cursor-pointer"></div>
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
            </div>
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-2xl p-8 shadow-lg">
                <h3 class="text-2xl font-bold mb-2">Бесплатная доставка от 5000 ₽</h3>
                <p class="text-white text-sm mb-4">По всей России</p>
            </div>
            <div class="bg-gradient-to-r from-pink-500 to-pink-700 text-white rounded-2xl p-8 shadow-lg">
                <h3 class="text-2xl font-bold mb-2">Подарок при покупке ванны</h3>
                <p class="text-white text-sm mb-4">Набор аксессуаров бесплатно</p>
            </div>
        </div>
    </section>
    <!-- Популярные товары -->
    <section class="py-12">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-4xl font-extrabold text-gray-900">Популярные товары</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach (array_slice($products, 0, 3) as $product): ?>
                <a href="/productCart.php?id=<?= $product['id'] ?>" class="group block">
                    <div
                        class="relative bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                        <div
                            class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                            Популярное
                        </div>
                        <img src="<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="w-full h-60 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="p-6 flex flex-col justify-between min-h-48">
                            <div>
                                <h3 class="font-semibold text-xl text-gray-800 mb-2 line-clamp-1">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h3>
                                <p class="text-gray-500 text-sm line-clamp-2">
                                    <?= htmlspecialchars(mb_substr($product['description'], 0, 80)) ?>...
                                </p>
                            </div>
                            <div class="text-right mt-4">
                                <span class="text-xl font-bold text-indigo-600">
                                    <?= number_format($product['price'], 2) ?> ₽
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>