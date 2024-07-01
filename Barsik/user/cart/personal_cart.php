<?php
session_start();

// Проверяем, авторизован ли пользователь. Если нет, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barsik/user/auth/auth.php');
    exit;
}

include '../../database/connectdb.php';

$userId = $_SESSION['user_id'];

// Расширенный запрос для получения данных о продуктах и их количестве в корзине
$query = "
    SELECT p.Name, p.Price, p.Description, p.Image, b.count, p.Id_product
    FROM Basket b
    JOIN Product p ON b.id_product = p.Id_product
    WHERE b.User_id = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $row['Total'] = $row['Price'] * $row['count'];
    $totalPrice += $row['Total'];
    $products[] = $row;
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>Ваша корзина</h2>
    <p>Общая стоимость: <span id="totalPrice"><?= $totalPrice ?>₽</span></p>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <img src="<?= $product['Image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['Name']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['Name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($product['Description']) ?></p>
                    <p class="card-text">Цена: <?= htmlspecialchars($product['Price']) ?>₽</p>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary change-quantity" type="button" data-id="<?= $product['Id_product'] ?>" data-operation="decrease">-</button>
                        <input type="text" class="form-control text-center quantity" value="<?= $product['count'] ?>" data-id="<?= $product['Id_product'] ?>">
                        <button class="btn btn-outline-secondary change-quantity" type="button" data-id="<?= $product['Id_product'] ?>" data-operation="increase">+</button>
                    </div>
                    <p class="card-text">Итого: <span id="total-<?= $product['Id_product'] ?>"><?= $product['Total'] ?>₽</span></p>
                    <button class="btn btn-danger delete-from-cart" data-id="<?= $product['Id_product'] ?>">Удалить</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <form method="post" action="checkout.php">
        <button type="submit" class="btn btn-success">Оформить заказ</button><br><br>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.change-quantity').click(function() {
        const id = $(this).data('id');
        const operation = $(this).data('operation');
        const input = $('input[data-id="' + id + '"]');
        let quantity = parseInt(input.val());
        if (operation === 'increase') {
            quantity++;
        } else if (operation === 'decrease' && quantity > 1) {
            quantity--;
        }
        input.val(quantity);
        updateQuantity(id, quantity);
    });

    $('.quantity').keypress(function(e) {
        if (e.which === 13) {
            const id = $(this).data('id');
            const quantity = $(this).val();
            updateQuantity(id, quantity);
        }
    });

    function updateQuantity(id, quantity) {
        $.post('crud/update_cart.php', {id_product: id, count: quantity}, function(data) {
            $('#total-' + id).text(data.total + '₽');
            $('#totalPrice').text(data.totalPrice + '₽');
        }, 'json');
    }

    $('.delete-from-cart').click(function() {
        const id = $(this).data('id');
        // Добавляем подтверждение перед удалением
        if (confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
            $.post('crud/delete_from_cart.php', {id_product: id}, function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        location.reload(); // Перезагрузка страницы только если удаление было успешным
                    } else {
                        alert('Ошибка при удалении товара: ' + data.message);
                    }
                } catch (e) {
                    alert('Ошибка при обработке ответа сервера.');
                }
            });
        }
    });
});
</script>

<script>
function validateForm() {
    var deliveryMethod = document.getElementById('deliveryMethod').value;
    var deliveryAddress = document.getElementById('deliveryAddress').value;

    // Проверяем, требуется ли адрес доставки
    if ((deliveryMethod === 'courier' || deliveryMethod === 'post') && (deliveryAddress === '' || deliveryAddress === 'Данных нет.')) {
        alert('Ошибка: Укажите адрес доставки.');
        return false;
    }
    return true;
}

function toggleAddressField() {
    var deliveryMethod = document.getElementById('deliveryMethod').value;
    var addressField = document.getElementById('addressField');
    if (deliveryMethod === 'courier' || deliveryMethod === 'post') {
        addressField.style.display = 'block';
    } else {
        addressField.style.display = 'none';
    }
}
</script>


</body>
</html>
