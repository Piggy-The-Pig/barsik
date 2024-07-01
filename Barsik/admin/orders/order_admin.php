<?php
session_start();
include '../../database/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['new_status'];

        // Подготовка запроса на обновление статуса
        $stmt = $mysqli->prepare("UPDATE Orders SET Status = ? WHERE Id_order = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();
        $stmt->close();
        // Перезагрузка страницы для отображения изменений
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['delete_order'])) {
        $orderId = $_POST['order_id'];

        // Подготовка запроса на удаление заказа
        $stmt = $mysqli->prepare("DELETE FROM Orders WHERE Id_order = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();
        // Перезагрузка страницы для отображения изменений
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Запрос к базе данных для получения информации о заказах
$query = "SELECT o.Id_order, o.Date_of_order, o.Status, o.Total_price, o.Used_bonuses, o.Accrued_bonuses, u.name AS UserName, GROUP_CONCAT(p.Name SEPARATOR ', ') AS ProductNames
          FROM Orders o
          JOIN Order_Product op ON o.Id_order = op.Id_order
          JOIN Product p ON op.Id_product = p.Id_product
          JOIN users u ON o.User_id = u.User_id
          GROUP BY o.Id_order
          ORDER BY o.Date_of_order DESC";
$result = $mysqli->query($query);

if (!$result) {
    // Обработка ошибки выполнения запроса
    die("Ошибка при получении данных о заказах: " . $mysqli->error);
}

// Запрос для получения возможных статусов из ENUM
$statusQuery = "SHOW COLUMNS FROM Orders WHERE Field = 'Status'";
$statusResult = $mysqli->query($statusQuery);
$statusRow = $statusResult->fetch_assoc();
$statusTypes = $statusRow['Type'];  // Получает строку вида "enum('Обработка','Доставляется','Отменен')"

// Извлечение индивидуальных значений из строки ENUM
preg_match("/^enum\(\'(.*)\'\)$/", $statusTypes, $matches);
$statuses = explode("','", $matches[1]);


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .btn-edit {
            color: #fff;
            background-color: #0d6efd; /* Синий цвет для кнопки Изменить */
            border-color: #0d6efd;
            margin-bottom: 10px;
        }
        .btn-delete {
            color: #fff;
            background-color: #dc3545; /* Красный цвет для кнопки Удалить */
            border-color: #dc3545;
        }
    </style>
</head>
<body>
<?php include '../nav/nav_admin.php'; ?>
    <div class="container mt-4">
        <h2>Управление заказами</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Номер Заказа</th>
                    <th scope="col">Имя Пользователя</th>
                    <th scope="col">Дата Заказа</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Общая Стоимость</th>
                    <th scope="col">Использованные Бонусы</th>
                    <th scope="col">Начисленные Бонусы</th>
                    <th scope="col">Название Продукта</th>
                    <th scope="col">Действия</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Id_order']) ?></td>
            <td><?= htmlspecialchars($row['UserName']) ?></td>
            <td><?= htmlspecialchars(date('d.m.Y', strtotime($row['Date_of_order']))) ?></td>
            <td>
                <span class="badge <?= $row['Status'] === 'Отменен' ? 'bg-danger' : 'bg-success' ?>">
                    <?= htmlspecialchars($row['Status']) ?>
                </span>
            </td>
            <td><?= htmlspecialchars(number_format($row['Total_price'], 2, '.', ' ')) ?> р</td>
            <td><?= htmlspecialchars($row['Used_bonuses']) ?></td>
            <td><?= htmlspecialchars($row['Accrued_bonuses']) ?></td>
            <td><?= htmlspecialchars($row['ProductNames']) ?></td>
            <td>
                <button class='btn btn-edit' data-bs-toggle='modal' data-bs-target='#editOrderModal-<?= $row['Id_order'] ?>'>Изменить</button>
                <form method="POST" action="" style="display: inline-block;">
                                <input type="hidden" name="order_id" value="<?= $row['Id_order'] ?>">
                                <button type="submit" name="delete_order" class="btn btn-delete">Удалить</button>
                </form>

                <!-- Модальное окно для редактирования заказа -->
                <div class="modal fade" id="editOrderModal-<?= $row['Id_order'] ?>" tabindex="-1" aria-labelledby="editOrderModalLabel-<?= $row['Id_order'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editOrderModalLabel-<?= $row['Id_order'] ?>">Редактирование заказа #<?= $row['Id_order'] ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?= $row['Id_order'] ?>">
                                    <div class="mb-3">
                                        <label for="orderStatus-<?= $row['Id_order'] ?>" class="form-label">Статус заказа</label>
                                        <select class="form-control" id="orderStatus-<?= $row['Id_order'] ?>" name="new_status">
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?= $status ?>" <?= $row['Status'] == $status ? 'selected' : '' ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                        <button type="submit" class="btn btn-primary" name="edit_status">Сохранить изменения</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>

    

</tbody>

</body>
</html>
