<?php
session_start();
?>
<header content="width=device-width, initial-scale=1">
    <div class="container">
        <div class="naw-header">
            <h1 class="text">Барcик</h1>
            <img src="../../design/img/Group 8192.png" alt="" class="logo">

            <div class="naw-menu d-flex align-items-center gap-3 mt-3">
                <a href="/" class="custom-btn">Главная</a>
                <a href="#" class="custom-btn">Каталог</a>
                <a href="http://barsik/user/cart/personal_cart.php" class="custom-btn">Корзина</a>
                <a href="#footer" class="custom-btn">Контакты</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="http://barsik/user/auth/personal_cab.php" class="custom-btn-login">Профиль</a>
                <?php else: ?>
                    <a href="http://barsik/user/auth/auth.php" class="custom-btn-login">Войти</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<style>
    .custom-btn, .custom-btn-login {
        padding: 10px 20px;
        background: linear-gradient(145deg, #0abab5, #08a2a0);
        color: white;
        border: none;
        border-radius: 20px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .custom-btn:hover {
        background: linear-gradient(145deg, #08a2a0, #0abab5);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .custom-btn-login {
        background: linear-gradient(145deg, #08a2a0, #49a1fc);
    }

    .custom-btn-login:hover {
        background: linear-gradient(145deg, #49a1fc, #08a2a0);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .naw-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .naw-menu {
        display: flex;
        gap: 15px;
    }
</style>
