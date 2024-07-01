<?php
session_start();

include '../database/connectdb.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barsik/user/auth/auth.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Запрос на получение данных пользователя
$userQuery = "SELECT User_id, Email, Bonus_points, role, contact_info, name FROM users WHERE User_id = ?";
$userStmt = $mysqli->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$userStmt->close();

if (!$userData) {
    echo 'Данные пользователя не найдены.';
    $mysqli->close();
    exit;
}

// Запрос на получение заказов пользователя
$orderQuery = "SELECT o.Id_order, o.Date_of_order, o.Total_price, o.Status, 
                      GROUP_CONCAT(CONCAT(p.Name, ' (', p.Id_product, ')') ORDER BY p.Name SEPARATOR ', ') AS Products
               FROM Orders o
               JOIN Order_Product op ON o.Id_order = op.Id_order
               JOIN Product p ON op.Id_product = p.Id_product
               WHERE o.User_id = ?
               GROUP BY o.Id_order
               ORDER BY o.Date_of_order DESC";
$orderStmt = $mysqli->prepare($orderQuery);
$orderStmt->bind_param("i", $userId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

// Подготовка запроса на проверку существующих отзывов
$reviewCheckQuery = "SELECT id_product FROM Reviews WHERE user_id = ? AND id_product = ?";
$reviewCheckStmt = $mysqli->prepare($reviewCheckQuery);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../design\css\style-header.css">
    <link rel="stylesheet" href="../../design\css\style-personal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Личный кабинет</title>
</head>
<body>

<style>
    .table th, .table td {
        vertical-align: middle; /* Выравниваем содержимое по вертикали */
        text-align: center; /* Выравниваем содержимое по центру */
    }
    .badge {
        width: 90px; /* Фиксированная ширина для статуса */
    }
    .img-writing {
        width: 24px;
        height: 24px;
    }
    /* Настройка ширины столбцов, если нужно */
    .table .col-order { width: 10%; }
    .table .col-date { width: 15%; }
    .table .col-products { width: 35%; }
    .table .col-sum { width: 15%; }
    .table .col-status { width: 10%; }
    .table .col-feedback { width: 15%; }
</style>

<?php include "../header.php"; ?>
    <main class="py-4">
    <div class="container">
        <h2 class="text-personal-account mb-4">Личный кабинет</h2>
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png" class="img-fluid rounded-circle" alt="Профиль пользователя">
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Редактирование профиля</h5>
                        <form action="edit/update_profile.php" method="post">
                            <div class="mb-3">
                                <label for="userName" class="form-label">Имя</label>
                                <input type="text" id="userName" name="userName" class="form-control" placeholder="Введите ваше имя" value="<?= htmlspecialchars($userData['name']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" name="userEmail" class="form-control" placeholder="Введите ваш email" value="<?= htmlspecialchars($userData['Email']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="userDeliveryAddress" class="form-label">Адрес доставки</label>
                                <input type="text" id="userDeliveryAddress" name="userDeliveryAddress" class="form-control" placeholder="Введите ваш адрес доставки" value="<?= htmlspecialchars($userData['contact_info']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="userBonuses" class="form-label">Бонусы</label>
                                <input type="text" id="userBonuses" name="userBonuses" class="form-control" value="<?= $userData['Bonus_points'] ?>" readonly>
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary w-100">Изменить</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="history-zacaz">
                <h3 class="order mb-4">Заказы</h3>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Заказ</th>
                                <th scope="col">Дата</th>
                                <th scope="col">Состав заказа</th>
                                <th scope="col">Сумма</th>
                                <th scope="col">Статус</th>
                                <th scope="col">Отзыв</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $orderResult->fetch_assoc()): ?>
    <tr>
        <td>Заказ #<?= htmlspecialchars($row['Id_order']) ?></td>
        <td><?= date('d.m.Y', strtotime($row['Date_of_order'])) ?></td>
        <td>
            <?php
            $products = explode(', ', $row['Products']);
            foreach ($products as $product):
                $productDetails = explode(' (', rtrim($product, ')'));
                $productName = $productDetails[0];
                $productId = $productDetails[1] ?? 'Неизвестный ID'; 
            ?>
                <li><?= htmlspecialchars($productName) ?></li>
            <?php endforeach; ?>
        </td>
        <td><?= number_format($row['Total_price'], 2, '.', ' ') ?> р</td>
        <td>
            <?php if ($row['Status'] == 'Отменен'): ?>
                <span class="badge bg-danger"><?= htmlspecialchars($row['Status']) ?></span>
            <?php else: ?>
                <span class="badge bg-success"><?= htmlspecialchars($row['Status']) ?></span>
            <?php endif; ?>
        </td>
        <td>
            <?php 
            foreach ($products as $product):
                $productDetails = explode(' (', rtrim($product, ')'));
                $productName = $productDetails[0];
                $productId = $productDetails[1] ?? 'Неизвестный ID';

                // Проверяем, оставил ли пользователь отзыв на этот продукт
                $reviewCheckStmt->bind_param("ii", $userId, $productId);
                $reviewCheckStmt->execute();
                $reviewExists = $reviewCheckStmt->get_result()->num_rows > 0;

                if (!$reviewExists):
            ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#feedback" data-product-id="<?= $productId ?>" onclick="setProductId(this.getAttribute('data-product-id'))">
                        <img src="../../design/img/writing.png" alt="Write feedback" class="img-fluid" style="width: 24px; height: 24px;">
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <?php endwhile; ?>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer id="footer">
    <div class="container">
        <div class="connection">
            <div class="connect">
            <div class="design\img-connection">
            </div>
            </div>
            
            </div>
        <hr> 
        <p class="copirater">©Barsik 2024.</p> 
    </div>
</footer>
<div class="modal fade" id="feedback" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Оставьте отзыв</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <!-- Форма обновлена для отправки данных на сервер -->
                <form method="post" action="../user/review/send_review.php">
                    <!-- Добавлено скрытое поле для ID продукта -->
                    <input type="hidden" id="id_product" name="id_product" value="">
                    <div class="mb-3">
                        <label for="review_text" class="col-form-label">Сообщение:</label>
                        <textarea class="form-control" id="review_text" name="review_text" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="col-form-label">Оценка:</label>
                        <select class="form-control" id="rating" name="rating">
                            <option value="1">1 - Очень плохо</option>
                            <option value="2">2 - Плохо</option>
                            <option value="3">3 - Нормально</option>
                            <option value="4">4 - Хорошо</option>
                            <option value="5" selected>5 - Отлично</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <!-- Кнопка для отправки формы -->
                        <button type="submit" class="btn btn-primary">Оставить отзыв</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setProductId(productId) {
        document.getElementById('id_product').value = productId;
    }
</script>

</body>
</html>

