<!-- nav/nav_admin.php -->
<?php
include_once(__DIR__ . '/../config.php');
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $adminIndexPath; ?>">Админ-Панель</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?php echo $adminIndexPath; ?>">Управление товарами</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $categoryAdminPath; ?>">Управление категориями</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $orderAdminPath; ?>">Управление заказами</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $statistic; ?>">Статистика</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $OwnCabinet; ?>">Личный Кабинет</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $indexpath; ?>">Выйти</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
