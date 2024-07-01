<?php 
include '..\..\database\connectdb.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление категориями</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-edit {
            background-color: #0abab5; /* Цвет тифани */
            color: white;
            border-color: #0abab5; /* Ensure the border is the same */
        }
        .btn-edit:hover {
            background-color: #08a0a0; /* Slightly darker on hover */
            color: white;
            border-color: #08a0a0;
        }
        .btn-secondary {
            background-color: #e02f2f;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #bf2424;
            color: white;
        }

        .add-product-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .add-product-btn:hover {
            background-color: #45a049;
            color: white;
            transform: scale(1.05);
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include '../nav/nav_admin.php'; ?>
    <div class="container mt-4">
        <h2>Управление категориями товаров</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Название категории</th>
                    <th scope="col">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $query = "SELECT * FROM Category";
                $result = $mysqli->query($query);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['Category_id']}</td>
                            <td>{$row['Name']}</td>
                            <td>
                                <button class='btn btn-edit' data-bs-toggle='modal' data-bs-target='#editCategoryModal{$row['Category_id']}'>Редактировать</button>
                                <!-- Форма удаления -->
                                <form action='crud/delete_category.php' method='post' style='display: inline;'>
                                    <input type='hidden' name='id' value='{$row['Category_id']}'>
                                    <button type='submit' class='btn btn-danger' onclick='return confirm(\"Вы уверены, что хотите удалить эту категорию?\");'>Удалить</button>
                                </form>
                            </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Произошла ошибка при загрузке данных</td></tr>";
                }
                ?>
            </tbody>

        </table>

        <button type="button" class="btn add-product-btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            Добавить категорию
        </button>

        <!-- Модальное окно для добавления категории -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Добавление новой категории</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="crud/add_category.php" method="post">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Название категории</label>
                                <input type="text" class="form-control" id="categoryName" name="name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                <button type="submit" class="btn btn-primary">Сохранить категорию</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальные окна для редактирования каждой категории -->
        <?php
        if ($result) {
            $result->data_seek(0); // Перезагружаем результаты запроса
            while ($row = $result->fetch_assoc()) {
                echo "<div class='modal fade' id='editCategoryModal{$row['Category_id']}' tabindex='-1' aria-labelledby='editCategoryModalLabel{$row['Category_id']}' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='editCategoryModalLabel{$row['Category_id']}'>Редактирование категории</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form action='crud/edit_category.php' method='post'>
                                        <input type='hidden' name='id' value='{$row['Category_id']}'>
                                        <div class='mb-3'>
                                            <label for='categoryName{$row['Category_id']}' class='form-label'>Название категории</label>
                                            <input type='text' class='form-control' id='categoryName{$row['Category_id']}' name='name' value='{$row['Name']}'>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Закрыть</button>
                                            <button type='submit' class='btn btn-primary'>Сохранить изменения</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
        }
        ?>

    </div>
</body>
</html>
