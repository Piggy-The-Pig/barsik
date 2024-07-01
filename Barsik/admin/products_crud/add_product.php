<?php
require_once '..\..\database\connectdb.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $image = $_POST['image'];  // URL изображения
    $description = $_POST['description'];  // Получение описания из формы

    $query = "INSERT INTO Product (Name, Category_id, Price, Image, Description) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("siiss", $name, $category_id, $price, $image, $description);
        if ($stmt->execute()) {
            echo "Новый товар добавлен успешно.";
            header("Location: ../index_admin.php");
            exit();
        } else {
            echo "Ошибка: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . $mysqli->error;
    }
    $mysqli->close();
} else {
    echo "Некорректный запрос.";
}
?>
