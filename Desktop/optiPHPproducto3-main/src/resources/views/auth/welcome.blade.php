<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isla_Transfer - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn-custom {
            margin: 1rem 0;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <h1 class="mb-4">Bienvenido a Isla_Transfer</h1>
            <p class="mb-4">Sistema de Reservas de Viajes</p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-custom">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="btn btn-secondary btn-custom">Registrarse</a>
        </div>
    </div>
</body>
</html>
