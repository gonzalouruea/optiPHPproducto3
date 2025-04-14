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

// Obtener datos del vehículo
if (!isset($_GET['id_vehiculo'])) {
    header("Location: gestionVehiculos.php?error=" . urlencode("ID de vehículo no especificado."));
    exit;
}

$id_vehiculo = $_GET['id_vehiculo'];
$sql = "SELECT * FROM transfer_vehiculo WHERE id_vehiculo = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id_vehiculo]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehiculo) {
    header("Location: gestionVehiculos.php?error=" . urlencode("Vehículo no encontrado."));
    exit;
}

// Modificar vehículo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = trim($_POST['descripcion']);
    $email_conductor = trim($_POST['email_conductor']);
    $password = !empty(trim($_POST['password'])) ? password_hash(trim($_POST['password']), PASSWORD_BCRYPT) : $vehiculo['password'];

    if (!empty($descripcion) && !empty($email_conductor)) {
        $sql = "UPDATE transfer_vehiculo SET Descripción = ?, email_conductor = ?, password = ? WHERE id_vehiculo = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$descripcion, $email_conductor, $password, $id_vehiculo]);
        header("Location: gestionVehiculos.php?success=" . urlencode("Vehículo modificado con éxito."));
        exit;
    } else {
        $error = "La descripción y el email del conductor son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Vehículo - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Modificar Vehículo</h2>
    </div>

    <!-- Mensajes de error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulario para modificar vehículo -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Modificar Vehículo ID: <?php echo $id_vehiculo; ?></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($vehiculo['Descripción']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_conductor" class="form-label">Email del Conductor</label>
                            <input type="email" class="form-control" id="email_conductor" name="email_conductor" value="<?php echo htmlspecialchars($vehiculo['email_conductor']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="gestionVehiculos.php" class="btn btn-primary">Volver a Gestión de Vehículos</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>