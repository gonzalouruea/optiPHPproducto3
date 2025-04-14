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

// Obtener todos los vehículos
$sql = "SELECT * FROM transfer_vehiculo ORDER BY id_vehiculo";
$stmt = $db->prepare($sql);
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Añadir vehículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehiculo'])) {
    $descripcion = trim($_POST['descripcion']);
    $email_conductor = trim($_POST['email_conductor']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    if (!empty($descripcion) && !empty($email_conductor) && !empty($password)) {
        $sql = "INSERT INTO transfer_vehiculo (Descripción, email_conductor, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$descripcion, $email_conductor, $password]);
        header("Location: gestionVehiculos.php?success=" . urlencode("Vehículo añadido con éxito."));
        exit;
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Eliminar vehículo
if (isset($_GET['delete_vehiculo'])) {
    $id_vehiculo = $_GET['delete_vehiculo'];
    $sql = "DELETE FROM transfer_vehiculo WHERE id_vehiculo = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_vehiculo]);
    header("Location: gestionVehiculos.php?success=" . urlencode("Vehículo eliminado con éxito."));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gestionar Vehículos - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Gestionar Vehículos</h2>
    </div>

    <!-- Mensajes de éxito o error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
    <?php endif; ?>

    <!-- Formulario para añadir vehículo -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Añadir Vehículo</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_conductor" class="form-label">Email del Conductor</label>
                            <input type="email" class="form-control" id="email_conductor" name="email_conductor" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" name="add_vehiculo" class="btn btn-primary w-100">Añadir Vehículo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de vehículos -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Lista de Vehículos</div>
                <div class="card-body">
                    <?php if (empty($vehiculos)): ?>
                        <p>No hay vehículos registrados.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehiculos as $vehiculo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($vehiculo['id_vehiculo']); ?></td>
                                        <td><?php echo htmlspecialchars($vehiculo['email_conductor']); ?></td>
                                        <td>
                                            <a href="modificarVehiculo.php?id_vehiculo=<?php echo $vehiculo['id_vehiculo']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                            <a href="gestionVehiculos.php?delete_vehiculo=<?php echo $vehiculo['id_vehiculo']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este vehículo?');">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="panelAdmin.php" class="btn btn-primary">Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>