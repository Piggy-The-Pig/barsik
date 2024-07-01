<?php
    require_once '..\..\..\database\connectdb.php'; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получаем ID категории из POST-запроса
        $category_id = $_POST['id'];

        // Проверяем, что ID не пуст
        if (!empty($category_id)) {
            // Создаем подготовленный запрос для удаления категории
            $query = "DELETE FROM Category WHERE Category_id = ?";

            // Подготавливаем SQL-запрос
            if ($stmt = $mysqli->prepare($query)) {
                // Привязываем переменную к параметру подготовленного запроса
                $stmt->bind_param("i", $category_id);

                // Выполняем запрос
                if ($stmt->execute()) {
                    // Если запрос успешен, перенаправляем пользователя обратно на страницу управления категориями
                    header("Location: ../category_admin.php");
                    exit();
                } else {
                    // Если запрос не удался, выводим сообщение об ошибке
                    echo "Ошибка при удалении категории: " . $stmt->error;
                }

                // Закрываем запрос
                $stmt->close();
            } else {
                echo "Ошибка: не удалось подготовить запрос";
            }
        } else {
            echo "Ошибка: Не указан ID категории";
        }
            $mysqli->close();
    } else {
        // Если данные не были отправлены методом POST, выводим сообщение об ошибке
        echo "Ошибка: данные формы не были отправлены";
    }
?>
