<?php
session_start();

if (isset($_SESSION['email'])) {
    $rol = $_SESSION['rol'] ?? '';
    if ($rol === 'admin') {
        header("Location: panelAdmin.php");
    } else {
        header("Location: panelUsuario.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <h1>Bienvenido a Isla-Transfers</h1>
            <p>Por favor, inicia sesión para continuar.</p>
            <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-secondary">Registrarse</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>