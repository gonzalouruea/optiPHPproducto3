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

// Obtener datos del hotel
if (!isset($_GET['id_hotel'])) {
    header("Location: gestionHoteles.php?error=" . urlencode("ID de hotel no especificado."));
    exit;
}

$id_hotel = $_GET['id_hotel'];
$sql = "SELECT * FROM tranfer_hotel WHERE id_hotel = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id_hotel]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    header("Location: gestionHoteles.php?error=" . urlencode("Hotel no encontrado."));
    exit;
}

// Modificar hotel
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_zona = trim($_POST['id_zona']);
    $comision = trim($_POST['comision']);
    $usuario = trim($_POST['usuario']);
    $password = !empty(trim($_POST['password'])) ? password_hash(trim($_POST['password']), PASSWORD_BCRYPT) : $hotel['password'];

    if (!empty($id_zona) && !empty($comision) && !empty($usuario)) {
        $sql = "UPDATE tranfer_hotel SET id_zona = ?, Comision = ?, usuario = ?, password = ? WHERE id_hotel = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_zona, $comision, $usuario, $password, $id_hotel]);
        header("Location: gestionHoteles.php?success=" . urlencode("Hotel modificado con éxito."));
        exit;
    } else {
        $error = "El ID de zona, la comisión y el usuario son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Hotel - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Modificar Hotel</h2>
    </div>

    <!-- Mensajes de error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulario para modificar hotel -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Modificar Hotel ID: <?php echo $id_hotel; ?></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="id_zona" class="form-label">ID Zona</label>
                            <input type="number" class="form-control" id="id_zona" name="id_zona" value="<?php echo htmlspecialchars($hotel['id_zona']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="comision" class="form-label">Comisión (%)</label>
                            <input type="number" step="0.01" class="form-control" id="comision" name="comision" value="<?php echo htmlspecialchars($hotel['Comision']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario del Hotel</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($hotel['usuario']); ?>" required>
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
        <a href="gestionHoteles.php" class="btn btn-primary">Volver a Gestión de Hoteles</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>