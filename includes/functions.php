<?php
require_once __DIR__ . '/../config/database.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getProducts($search = '', $category = '', $minPrice = '', $maxPrice = '', $sort = '')
{
    global $pdo;

    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($category)) {
        $sql .= " AND category_id = :category";
        $params[':category'] = (int) $category;
    }

    if (!empty($minPrice)) {
        $sql .= " AND price >= :min_price";
        $params[':min_price'] = (float) $minPrice;
    }

    if (!empty($maxPrice)) {
        $sql .= " AND price <= :max_price";
        $params[':max_price'] = (float) $maxPrice;
    }

    // Сортировка
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY price DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY name DESC";
            break;
        default:
            $sql .= " ORDER BY id DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getProductById($id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCategories()
{
    global $pdo;

    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addToCart($productId, $quantity = 1)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}


function getCartItems()
{
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $cartItems = [];
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProductById($productId);
        if ($product) {
            $product['quantity'] = $quantity;
            $cartItems[] = $product;
        }
    }

    return $cartItems;
}

function getOrderHistory($userId)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}function getSliderImages()
{
    return [
        'assets/images/slider1.png',
        'assets/images/slider2.png',
        'assets/images/slider3.png',
    ];
}
//скрипт слайдера
function renderSliderScript() {
    echo <<<HTML
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.getElementById('slider');
        const slides = slider.querySelectorAll('img');
        const totalSlides = slides.length;
        let currentIndex = 0;

        const updateSlider = () => {
            slider.style.transform = 'translateX(' + (-100 * currentIndex) + '%)';
            updateDots();
        };

        document.getElementById('next').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        });

        document.getElementById('prev').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSlider();
        });

        // Автоматическая прокрутка
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        }, 5000);

        // Навигационные точки
        const dots = document.querySelectorAll('.slider-dot');
        function updateDots() {
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-indigo-600', index === currentIndex);
                dot.classList.toggle('bg-gray-300', index !== currentIndex);
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                updateSlider();
            });
        });

        updateSlider();
    });
    </script>
HTML;
}
?>