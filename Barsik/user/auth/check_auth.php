<?php
session_start();

// Проверяем, авторизирован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизирован, перенаправляем на страницу авторизации
    header('Location: http://barsik/user/auth/auth.php');
    exit; // Важно завершить выполнение скрипта после редиректа
}

// Подключаемся к базе данных
include "../../database/connectdb.php";

// Получаем роль пользователя из базы данных
$user_id = $_SESSION['user_id']; // Получаем ID пользователя из сессии
$stmt = $mysqli->prepare("SELECT role FROM users WHERE User_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();

// Проверяем роль пользователя
if ($role === 'admin') {
    // Если пользователь - администратор, перенаправляем на страницу администратора
    header('Location: ../../admin/index_admin.php');
} elseif ($role === 'user') {
    // Если пользователь - обычный пользователь, перенаправляем на его личный кабинет
    header('Location: ../personal_cab.php');
} else {
    // В случае, если роль не определена, выводим ошибку
    die('Ошибка: Роль пользователя не определена');
}

$stmt->close();
$mysqli->close();
?>
