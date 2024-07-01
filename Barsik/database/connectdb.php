<?php
$host = 'localhost'; // Адрес сервера
$username = 'root'; // Имя пользователя
$password = ''; // Пароль
$dbname = 'BarSik'; // Имя базы данных

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die('Ошибка подключения: ' . $mysqli->connect_error);
}
?>
