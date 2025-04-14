<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

// Obtener los datos actuales del usuario
$email = $_SESSION['email'];
try {
    $sql = "SELECT nombre, email FROM transfer_viajeros WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Datos - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <!-- Título -->
    <div class="text-center py-2">
        <h2>Modifica tus datos</h2>
    </div>
    <br>

    <!-- Formulario centrado en el contenedor -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="row g-3 needs-validation" action="procesarModificarDatos.php" method="POST" novalidate>
                <div class="col-md-12">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    <div class="valid-feedback">¡Correcto!</div>
                    <div class="invalid-feedback">Por favor, introduce tu nombre.</div>
                </div>
                <div class="col-md-12">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        <div class="invalid-feedback">Por favor, introduce un correo válido.</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                    <div class="valid-feedback">¡Correcto!</div>
                </div>
                <div class="col-md-12">
                    <input type="submit" name="enviar" id="enviar" class="btn btn-primary w-100" value="Guardar cambios">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archivos JavaScript de Bootstrap (incluye Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>