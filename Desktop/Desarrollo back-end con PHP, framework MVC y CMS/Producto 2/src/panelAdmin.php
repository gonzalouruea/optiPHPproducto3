<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

try {
    require_once 'conexion.php';
} catch (Exception $e) {
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit;
}

$email = $_SESSION['email'];
$sql = "SELECT rol FROM transfer_viajeros WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Panel de Admin - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Panel de Admin</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['email']); ?>!</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Gestión de Reservas</div>
                <div class="card-body">
                    <a href="crearReserva.php" class="btn btn-primary mb-2">Crear Nueva Reserva</a>
                    <a href="visualizarReservas.php" class="btn btn-info mb-2">Ver Todas las Reservas</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Gestión de Vehículos</div>
                <div class="card-body">
                    <a href="gestionVehiculos.php" class="btn btn-primary mb-2">Gestionar Vehículos</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Gestión de Destinos (Hoteles)</div>
                <div class="card-body">
                    <a href="gestionHoteles.php" class="btn btn-primary mb-2">Gestionar Hoteles</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>