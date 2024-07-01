<?php
    require_once '..\..\..\database\connectdb.php'; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получаем данные из POST-запроса
        $category_id = $_POST['id'];
        $category_name = $_POST['name'];

        // Проверяем, что данные не пусты
        if (!empty($category_id) && !empty($category_name)) {
            // Создаем подготовленный запрос для обновления данных в таблице
            $query = "UPDATE Category SET Name = ? WHERE Category_id = ?";

            // Подготавливаем SQL-запрос
            if ($stmt = $mysqli->prepare($query)) {
                // Привязываем переменные к параметрам подготовленного запроса
                $stmt->bind_param("si", $category_name, $category_id);

                // Выполняем запрос
                if ($stmt->execute()) {
                    // Если запрос успешен, перенаправляем пользователя обратно на страницу управления категориями
                    header("Location: ..\category_admin.php");
                    exit();
                } else {
                    // Если запрос не удался, выводим сообщение об ошибке
                    echo "Ошибка: " . $stmt->error;
                }

                // Закрываем запрос
                $stmt->close();
            } else {
                echo "Ошибка: не удалось подготовить запрос";
            }
        } else {
            echo "Ошибка: Все поля формы должны быть заполнены";
        }
            $mysqli->close();
    } else {
        // Если данные не были отправлены методом POST, выводим сообщение об ошибке
        echo "Ошибка: данные формы не были отправлены";
    }
?>
