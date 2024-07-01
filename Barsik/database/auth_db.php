<?php
include "connectdb.php";
session_start();

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if (empty($email) || empty($password)) {
    die('Email или пароль не могут быть пустыми');
}

$stmt = $mysqli->prepare("SELECT User_id, Password_hash, role FROM users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($user_id, $password_hash, $role);
    $stmt->fetch();

    echo "Debug: DB Password Hash: " . $password_hash . "<br>";  // Для диагностики
    echo "Debug: Entered Password Hash: " . password_hash($password, PASSWORD_DEFAULT) . "<br>";  // Для диагностики

    if (password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $user_id;
        if ($role === 'admin') {
            header('Location: ../../admin/index_admin.php');
        } elseif ($role === 'user') {
            header('Location: ../../user/personal_cab.php');
        }
    } else {
        die('Неверный пароль');
    }
} else {
    die('Пользователь не найден');
}

$stmt->close();
$mysqli->close();
?>
