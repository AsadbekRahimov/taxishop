<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - TaxiShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="logo">
            TaxiShop <i class="fa-solid fa-taxi"></i>
        </div>
        <form action="{{ route('home') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="login" class="form-control" placeholder="Введите логин" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
            </div>
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="remember" checked> Запомнить меня
                </label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Войти в систему</button>
        </form>
    </div>
</body>
</html>
