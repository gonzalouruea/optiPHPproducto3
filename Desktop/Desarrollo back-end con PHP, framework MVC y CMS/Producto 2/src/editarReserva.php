<?php
session_start();
require_once 'conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ver_reservas.php");
    exit;
}

$id_reserva = $_GET['id'];
$reserva = $db->query("
    SELECT r.*, t.Descripcion as tipo_reserva 
    FROM transfer_reservas r
    JOIN transfer_tipo_reserva t ON r.id_tipo_reserva = t.id_tipo_reserva
    WHERE r.id_reserva = $id_reserva
")->fetch();

if (!$reserva) {
    header("Location: ver_reservas.php");
    exit;
}

$hoteles = $db->query("SELECT * FROM transfer_hotel")->fetchAll();
$vehiculos = $db->query("SELECT * FROM transfer_vehiculo")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar actualización similar a nueva_reserva.php
    // ...
    $_SESSION['exito'] = "Reserva actualizada correctamente";
    header("Location: ver_reservas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4">
    <h2>Editar Reserva: <?= $reserva['localizador'] ?></h2>
    
    <form method="post">
        <!-- Formulario similar a nueva_reserva.php pero con valores precargados -->
        <!-- ... -->
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="ver_reservas.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>