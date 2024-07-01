<?php
require_once '..\..\database\connectdb.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['id'];
    $product_name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $product_price = $_POST['price'];
    $product_image = $_POST['image'];
    $product_description = $_POST['description']; // Получение описания из формы

    if (empty($product_name) || empty($category_id) || empty($product_price) || empty($product_image) || empty($product_description)) {
        echo "Все поля формы должны быть заполнены.";
    } else {
        $query = "UPDATE Product SET Name = ?, Category_id = ?, Price = ?, Image = ?, Description = ? WHERE Id_product = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("sisssi", $product_name, $category_id, $product_price, $product_image, $product_description, $product_id);

            if ($stmt->execute()) {
                echo "Данные продукта успешно обновлены.";
                header("Location: ../index_admin.php");
                exit();
            } else {
                echo "Ошибка: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Ошибка: " . $mysqli->error;
        }
    }

    $mysqli->close();
} else {
    echo "Ошибка: Неверный запрос";
}
?>
