<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../design/css/style.css">
    <link rel="stylesheet" href="../../design/css/auth/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Авторизация</title>
    <style>
        
    </style>
</head>
<body>
    <?php include "../../header.php"; ?>
    <div class="auto-main">
        <h2>Авторизация</h2>
        <form action="../../../database/auth_db.php" method="post" class="form-auto">
            <input required type="email" name="email" placeholder="Email" class="form-control">
            <input required type="password" name="password" placeholder="Пароль" class="form-control">
            <button type="submit" class="entrance">Авторизация</button>
            <p>Нет аккаунта ? <a href="reg.php">Регистрация</a></p>
        </form>
    </div>
</body>
</html>
