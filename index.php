<?php 
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$products = getProducts();
?>

<div class="container mx-auto px-4 py-10">
    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold mb-4 text-indigo-700">Добро пожаловать!</h1>
        <p class="text-gray-600 text-xl">Качественная сантехника по отличным ценам.</p>
    </div>

    <!-- Акции -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">🔥 Акции недели 🔥</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 text-white rounded-2xl p-8 shadow-lg flex flex-col justify-between">
                <h3 class="text-2xl font-bold mb-4">Скидка 20% на душевые кабины!</h3>
                <p class="text-lg mb-6">Только до конца недели. Выбирайте лучшие модели для вашего дома!</p>
                <div id="timer1" class="text-xl font-bold mb-6">Осталось: <span id="time1">00 дней 00 ч 00 мин 00 сек</span></div>
                <a href="/products.php?promo=showers" class="bg-white text-indigo-700 font-semibold px-5 py-3 rounded-full hover:bg-gray-100 transition">
                    Смотреть товары
                </a>
            </div>

            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-2xl p-8 shadow-lg flex flex-col justify-between">
                <h3 class="text-2xl font-bold mb-4">Бесплатная доставка при заказе от 5000 ₽!</h3>
                <p class="text-lg mb-6">Закажите всё необходимое и получите бесплатную доставку прямо к двери.</p>
                <div id="timer2" class="text-xl font-bold mb-6">Осталось: <span id="time2">00 дней 00 ч 00 мин 00 сек</span></div>
                <a href="/products.php" class="bg-white text-green-700 font-semibold px-5 py-3 rounded-full hover:bg-gray-100 transition">
                    Подробнее
                </a>
            </div>

            <div class="bg-gradient-to-r from-pink-500 to-pink-700 text-white rounded-2xl p-8 shadow-lg flex flex-col justify-between">
                <h3 class="text-2xl font-bold mb-4">Подарок при покупке ванны!</h3>
                <p class="text-lg mb-6">При покупке любой ванны — стильный комплект аксессуаров в подарок.</p>
                <div id="timer3" class="text-xl font-bold mb-6">Осталось: <span id="time3">00 дней 00 ч 00 мин 00 сек</span></div>
                <a href="/products.php?promo=bath" class="bg-white text-pink-700 font-semibold px-5 py-3 rounded-full hover:bg-gray-100 transition">
                    Получить подарок
                </a>
            </div>
        </div>
    </section>

    <!-- Популярные товары -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Популярные товары</h2>
            <a href="/products.php" class="bg-indigo-600 text-white px-6 py-3 rounded-full hover:bg-indigo-700 transition">
                Смотреть всё
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($products, 0, 6) as $product): ?>
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-56 object-cover">
                    <div class="p-5 flex flex-col justify-between h-56">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-gray-500 text-sm mb-4"><?= htmlspecialchars(mb_substr($product['description'], 0, 80)) ?>...</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-600 text-lg"><?= number_format($product['price'], 2) ?> ₽</span>
                            <a href="/products.php?add_to_cart=<?= $product['id'] ?>" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm transition">
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

<script>
function formatTime(timeLeft) {
    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
    return `${days} дней ${hours} ч ${minutes} мин ${seconds} сек`;
}

const endDate1 = new Date("2025-05-01T00:00:00Z"); 
const timer1 = document.getElementById("time1");

function updateTimer1() {
    const now = new Date();
    const timeLeft = endDate1 - now;

    if (timeLeft <= 0) {
        timer1.textContent = "Акция завершена!";
        clearInterval(interval1);
    } else {
        timer1.textContent = formatTime(timeLeft);
    }
}
const interval1 = setInterval(updateTimer1, 1000);

const endDate2 = new Date("2025-05-01T00:00:00Z"); 
const timer2 = document.getElementById("time2");

function updateTimer2() {
    const now = new Date();
    const timeLeft = endDate2 - now;

    if (timeLeft <= 0) {
        timer2.textContent = "Акция завершена!";
        clearInterval(interval2);
    } else {
        timer2.textContent = formatTime(timeLeft);
    }
}
const interval2 = setInterval(updateTimer2, 1000);

const endDate3 = new Date("2025-05-01T00:00:00Z"); 
const timer3 = document.getElementById("time3");

function updateTimer3() {
    const now = new Date();
    const timeLeft = endDate3 - now;

    if (timeLeft <= 0) {
        timer3.textContent = "Акция завершена!";
        clearInterval(interval3);
    } else {
        timer3.textContent = formatTime(timeLeft);
    }
}
const interval3 = setInterval(updateTimer3, 1000);
</script>

