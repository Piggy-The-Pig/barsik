<?php 
session_start();

include 'database/connectdb.php';

// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Можно выполнять запросы или операции, зависящие от пользователя
}

// Получение всех категорий
$categoryQuery = "SELECT Category_id, Name FROM Category ORDER BY Name";
$categoryResult = $mysqli->query($categoryQuery);

if (!$categoryResult) {
    echo "Ошибка: " . $mysqli->error;
    $mysqli->close();
    exit;
}

// Получение всех продуктов
$productQuery = "SELECT Id_product, Name, Description, Category_id, Price, Image FROM Product ORDER BY Category_id, Name";
$productResult = $mysqli->query($productQuery);

$productsByCategory = [];
while ($product = $productResult->fetch_assoc()) {
    $productsByCategory[$product['Category_id']][] = $product;
}

$reviewQuery = "SELECT r.review_text, r.rating, p.Name AS product_name, u.name AS user_name
                FROM Reviews r
                JOIN users u ON r.user_id = u.User_id
                JOIN Product p ON r.id_product = p.Id_product
                ORDER BY r.review_date DESC 
                LIMIT 3";  // Ограничиваем запрос тремя последними записями
$reviewResult = $mysqli->query($reviewQuery);

if (!$reviewResult) {
    echo "Ошибка при получении отзывов: " . $mysqli->error;
    $mysqli->close();
    exit;
}
$reviews = $reviewResult->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design/css/style.css">
    <title>Каталог продуктов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .button-details {
            background-color: #0abab5; /* Цвет тифани */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .button-details:hover {
            background-color: #08a2a0;
            color: #ffffff;
        }
        .mb-3 {
    margin-bottom: 1rem !important;
    margin-top: 4rem !important;
            }
    </style>
</head>
<body>
<?php include "header.php"; ?>
<main class="container mt-4">
    <h2 class="mb-3">Каталог</h2>
    <div class="row g-3 mb-4">
        <?php foreach ($categoryResult->fetch_all(MYSQLI_ASSOC) as $category): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($category['Name']); ?></h5>
                        <button class="button-details" data-bs-toggle="modal" data-bs-target="#productModal" onclick="loadProducts(<?= $category['Category_id']; ?>)">Просмотреть</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Модальное окно для товаров -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Товары категории</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="modalProductsContainer">
                    <!-- Сюда будут динамически загружаться товары -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- часть с текстом -->
<div class="description">
    <div class="text-discription">
        <h3>Закажите свежие напитки прямо к себе домой!</h3>
        <p >Наша компания рада предложить вам богатый выбор освежающих напитков для тех, кто ценит уникальный вкус и комфорт.</p>
        <p>У нас в ассортименте - свежевыжатые фруктовые соки, ароматный чай и кофе, натуральные молочные продукты, а также энергетические и спортивные напитки.</p>
    </div>
    <div class="bloc-img-description">
        <img src="design\img\Group 8192.png" alt="" class="logo">
        <img src="design\img\Group 8195.png" alt="" class="img-description">
    </div>
</div>

<div class="reviews">
    <h2 class="text-reviews">Отзывы</h2>
    <div class="slider">
        <?php if (empty($reviews)): ?>
            <div class="slide">
                <p class="no-reviews">Отзывов нету</p>
            </div>
        <?php else: ?>
            <div class="slide">
                <?php foreach ($reviews as $review): ?>
                    <div class="otzv">
                        <img class="user-img" src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png" alt="">
                        <h4><?= htmlspecialchars($review['product_name']); ?></h4> <!-- Название продукта -->
                        <p><?= htmlspecialchars($review['review_text']); ?></p>
                        <p><strong>Оценка:</strong> <?= $review['rating']; ?>/5</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


</main>
    <!-- подвал -->
<footer id="footer">
    <div class="container">
        <div class="connection">
            <div class="connect">
            <p>Связь с нами</p> 
            <div class="design\img-connection">
                <img src="design\img/instagram.png" alt=""class="icon-whatsapp">
                <img src="design\img\icons8-vk-com-48.png" alt="" srcset="">
                <img src="design\img\iconfinder-social-media-applications-23whatsapp-4102606_113811.png" class="icon-whatsapp">
            </div>
            </div>
            
            </div>
        <hr> 
        <p class="copirater">©Barsik 2024.</p> 
    </div>
</footer>

<script>
function loadProducts(categoryId) {
    const products = <?= json_encode($productsByCategory); ?>;
    const container = document.getElementById('modalProductsContainer');
    container.innerHTML = ''; // Очистка предыдущих товаров

    if (products[categoryId]) {
        products[categoryId].forEach(product => {
            container.innerHTML += `
                <div class="col-md-4">
                    <div class="card">
                        <img src="${product.Image}" class="card-img-top" alt="${product.Name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.Name}</h5>
                            <p class="card-text">${product.Price}р</p>
                            <button onclick="addToCart(${product.Id_product})" class="btn btn-success">Добавить в корзину</button>
                        </div>
                    </div>
                </div>`;
        });
    } else {
        container.innerHTML = '<p>В этой категории пока нет товаров.</p>';
    }
}

function addToCart(productId) {
    const userId = <?= $_SESSION['user_id'] ?>; // Берем ID пользователя из PHP сессии
    fetch('user/cart/crud/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${userId}&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Продукт успешно добавлен в корзину!');
            // window.location.href = 'user/cart/personal_cart.php';
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Ошибка:', error));
}
</script>





</body>
</html>

<!-- <script>
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;

    function nextSlide() {
        slides[currentSlide].style.display = 'none';
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].style.display = 'flex';
    }

    setInterval(nextSlide, 9000);
</script> -->

