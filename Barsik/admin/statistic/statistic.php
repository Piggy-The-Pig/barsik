<?php
session_start();
include '../../database/connectdb.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barshikweb/user/auth/auth.php');
    exit;
}

// Общее количество заказов
$queryTotalOrders = "SELECT COUNT(*) as TotalOrders FROM Orders";
$resultTotalOrders = $mysqli->query($queryTotalOrders);
$totalOrders = $resultTotalOrders->fetch_assoc();

// Общая выручка
$queryTotalRevenue = "SELECT SUM(Total_price) as TotalRevenue FROM Orders";
$resultTotalRevenue = $mysqli->query($queryTotalRevenue);
$totalRevenue = $resultTotalRevenue->fetch_assoc();

// Общее количество проданных товаров
$queryTotalProductsSold = "SELECT SUM(count) as TotalProductsSold FROM Order_Product";
$resultTotalProductsSold = $mysqli->query($queryTotalProductsSold);
$totalProductsSold = $resultTotalProductsSold->fetch_assoc();

// Средний чек
$averageCheck = $totalOrders['TotalOrders'] > 0 ? $totalRevenue['TotalRevenue'] / $totalOrders['TotalOrders'] : 0;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Статистика и отчеты</title>
</head>
<body>

<?php include '../nav/nav_admin.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">Статистика и отчеты</h1>
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-primary">
                <div class="card-header">Заказы</div>
                <div class="card-body text-primary">
                    <h5 class="card-title">Общее количество заказов</h5>
                    <p class="card-text"><?= $totalOrders['TotalOrders'] ?> заказов</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-success">
                <div class="card-header">Выручка</div>
                <div class="card-body text-success">
                    <h5 class="card-title">Общая выручка</h5>
                    <p class="card-text"><?= number_format($totalRevenue['TotalRevenue'], 2, '.', ' ') ?> руб.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-info">
                <div class="card-header">Продажи</div>
                <div class="card-body text-info">
                    <h5 class="card-title">Количество проданных товаров</h5>
                    <p class="card-text"><?= $totalProductsSold['TotalProductsSold'] ?> товаров</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-warning">
                <div class="card-header">Средний чек</div>
                <div class="card-body text-warning">
                    <h5 class="card-title">Средний чек на заказ</h5>
                    <p class="card-text"><?= number_format($averageCheck, 2, '.', ' ') ?> руб.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
