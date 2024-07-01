<?php
include "connectdb.php";

$email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES);
$password = htmlspecialchars(trim($_POST['password']), ENT_QUOTES);

// Проверка длины пароля
if (mb_strlen($password) < 5 || mb_strlen($password) > 100) {
    echo "Недопустимая длина пароля";
    exit();
}

// Подготовка и выполнение запроса для поиска почты
$stmt = $mysqli->prepare("SELECT * FROM `users` WHERE `email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "Данная почта уже используется!";
    exit();
}

// Хеширование пароля
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Вставка нового пользователя
$insert_stmt = $mysqli->prepare("INSERT INTO `users` (`email`, `password_hash`, `Bonus_points`) VALUES (?, ?, '1')");
$insert_stmt->bind_param("ss", $email, $hashed_password);
if ($insert_stmt->execute()) {
    $_SESSION["user_id"] = $mysqli->insert_id;  // Сохраняем ID нового пользователя в сессию
    header('Location: ../../user/personal_cab.php');
} else {
    echo "Ошибка при регистрации пользователя";
}

$insert_stmt->close();
$stmt->close();
$mysqli->close();
?>
