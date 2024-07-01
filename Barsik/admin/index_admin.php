<?php 
// Подключаемся к базе данных
include '..\database\connectdb.php';

// Запросы к базе данных для получения данных о товарах
$productQuery = "SELECT Product.Id_product, Product.Name, Product.Category_id, Product.Description, Product.Price, Product.Image, Category.Name AS CategoryName FROM Product JOIN Category ON Product.Category_id = Category.Category_id";
$result = $mysqli->query($productQuery);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Запрос к базе данных для получения категорий
$categoryQuery = "SELECT Category_id, Name FROM Category";
$categoriesResult = $mysqli->query($categoryQuery);
$categories = [];
$categoryOptions = ''; // Инициализация строки для HTML-опций
if ($categoriesResult) {
    while ($cat = $categoriesResult->fetch_assoc()) {
        $categories[$cat['Category_id']] = $cat['Name'];
        // Создаем HTML-опцию для каждой категории
        $categoryOptions .= '<option value="' . $cat['Category_id'] . '">' . htmlspecialchars($cat['Name']) . '</option>';
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .btn-tiffany {
            background-color: #0abab5; /* Цвет тифани */
            color: white;
            border: none;
        }
        .btn-tiffany:hover {
            background-color: #08a2a0;
            color: white;
        }
        .img-thumbnail {
            height: 100px;
            object-fit: cover; /* Для лучшего отображения изображений */
        }
        .modal-content {
            background: #f8f9fa; /* Светлый фон для модальных окон */
        }
        .card-footer button, .card-footer form {
            display: inline-block;
        }
        table {
            width: 100%;
        }
        .table-card {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 20px;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ccc;
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
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<?php include 'nav/nav_admin.php'; ?>
    <div class="container mt-4">
        <table>
            <?php foreach ($products as $row): ?>
                <tr class="table-card">
                    <td><img src="<?= $row['Image'] ?>" class="img-thumbnail" alt="Изображение товара"></td>
                    <td>
                        <h5><?= $row['Name'] ?></h5>
                        <p>Категория: <?= $categories[$row['Category_id']] ?></p>
                        <p>Цена: <?= $row['Price'] ?> руб.</p>
                        <p>Описание: <?= $row['Description'] ?></p>
                        <div>
                            <button class="btn btn-tiffany" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['Id_product'] ?>">Редактировать</button>
                            <form action="products_crud/delete_product.php" method="post" style="display: inline;">
                                <input type='hidden' name='id' value='<?= $row['Id_product'] ?>'>
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <button type="button" class="add-product-btn" data-bs-toggle="modal" data-bs-target="#addProductModal">
            Добавить товар
        </button>


        <!-- Модальное окно для добавления товара -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Добавление нового товара</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="products_crud/add_product.php" method="post">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Название</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Категория</label>
                                <select class="form-control" id="productCategory" name="category_id" required>
                                    <?= $categoryOptions ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Описание</label>
                                <textarea class="form-control" id="productDescription" name="description" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Стоимость</label>
                                <input type="number" class="form-control" id="productPrice" name="price" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="productImage" class="form-label">URL изображения</label>
                                <input type="text" class="form-control" id="productImage" name="image" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно редактирования товаров -->

        <?php
            $productQuery = "SELECT Product.Id_product, Product.Name, Product.Category_id, Product.Description, Product.Price, Product.Image, Category.Name AS CategoryName FROM Product JOIN Category ON Product.Category_id = Category.Category_id";
            $result = $mysqli->query($productQuery);

            if ($result) {
                while ($row = $result->fetch_assoc()) {

                    $categoryQuery = "SELECT Category_id, Name FROM Category";
                    $categories = $mysqli->query($categoryQuery);
                    $categoriesOptions = "";
                    while ($cat = $categories->fetch_assoc()) {
                        $selected = ($row['Category_id'] == $cat['Category_id']) ? "selected" : "";
                        $categoriesOptions .= "<option value='{$cat['Category_id']}' {$selected}>{$cat['Name']}</option>";
                    }

                    $productId = $row['Id_product'];
                    $productName = $row['Name'] ?? 'No name provided';
                    $productDescription = $row['Description'] ?? 'No description provided';
                    $productPrice = $row['Price'] ?? '0';
                    $productImage = $row['Image'] ?? 'No image available';

                    echo "
                    <div class='modal fade' id='editProductModal{$productId}' tabindex='-1' aria-labelledby='editProductModalLabel{$productId}' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='editProductModalLabel{$productId}'>Редактирование товара</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form action='products_crud/edit_product.php' method='post'>
                                        <input type='hidden' name='id' value='{$productId}'>
                                        <div class='mb-3'>
                                            <label for='productName{$productId}' class='form-label'>Название</label>
                                            <input type='text' class='form-control' id='productName{$productId}' name='name' value='{$productName}'>
                                        </div>

                                        <div class='mb-3'>
                                            <label for='productCategory{$productId}' class='form-label'>Категория</label>
                                            <select class='form-control' id='productCategory{$productId}' name='category_id'>
                                                $categoriesOptions
                                            </select>
                                        </div>

                                        <div class='mb-3'>
                                            <label for='productDescription{$productId}' class='form-label'>Описание</label>
                                            <textarea class='form-control' id='productDescription{$productId}' name='description'>{$productDescription}</textarea>
                                        </div>

                                        <div class='mb-3'>
                                            <label for='productPrice{$productId}' class='form-label'>Стоимость</label>
                                            <input type='text' class='form-control' id='productPrice{$productId}' name='price' value='{$productPrice}'>
                                        </div>

                                        <div class='mb-3'>
                                            <label for='productImage{$productId}' class='form-label'>Изображение</label>
                                            <input type='text' class='form-control' id='productImage{$productId}' name='image' value='{$productImage}'>
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
