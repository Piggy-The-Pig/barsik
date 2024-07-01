<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../design/css/style.css">
    <link rel="stylesheet" href="../../design/css/reg/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Регистрация</title>
</head>
<body>
    <?php include "../../header.php"; ?>
    <div class="form-reg">
        <h2>Регистрация</h2>
        <form action="../../../database/reg_db.php" method="post">
            <input required type="email" name="email" placeholder="Email" class="form-control mb-3">
            <input required type="password" name="password" placeholder="Пароль" class="form-control mb-3">
            <button type="submit" class="btn btn-primary">Регистрация</button>
            <p>Есть аккаунт? <a href="auth.php">Авторизируйтесь</a></p>
        </form>
    </div>
</body>
</html>
