<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CRM Система') }}</title>
    <!-- Додайте ваші стилі тут, наприклад Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">CRM Система</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clients.index') }}">Клієнти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('orders.index') }}">Замовлення</a>
                    </li>
                    <!-- Додайте інші посилання тут -->
                </ul>
            </div>
        </nav>
        
        @yield('content') <!-- Тут будуть вставлятися специфічні для кожної сторінки частини -->
    </div>
</body>
</html>
