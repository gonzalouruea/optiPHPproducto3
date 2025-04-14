<?php
session_start(); // Añadimos session_start() por si se necesita en nav.php

require_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido1 = trim($_POST['apellido1'] ?? '');
    $apellido2 = trim($_POST['apellido2'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $codigoPostal = trim($_POST['codigoPostal'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($apellido1) || empty($apellido2) || empty($direccion) || 
        empty($codigoPostal) || empty($ciudad) || empty($pais) || empty($email) || 
        empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } else {
        try {
            // Verificar si el email ya está registrado
            $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_viajeros WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "El email ya está registrado.";
            } else {
                // Insertar el nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO transfer_viajeros (nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email, password) 
                        VALUES (:nombre, :apellido1, :apellido2, :direccion, :codigoPostal, :ciudad, :pais, :email, :password)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':apellido1' => $apellido1,
                    ':apellido2' => $apellido2,
                    ':direccion' => $direccion,
                    ':codigoPostal' => $codigoPostal,
                    ':ciudad' => $ciudad,
                    ':pais' => $pais,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);

                // Redirigir al usuario al login después de registrarse
                header("Location: /login.php?success=Registro exitoso. Por favor, inicia sesión.");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Registrarse - Isla-Transfers</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Registrarse</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form action="/registro.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="apellido1" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" id="apellido1" name="apellido1" required value="<?php echo htmlspecialchars($apellido1 ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="apellido2" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" id="apellido2" name="apellido2" required value="<?php echo htmlspecialchars($apellido2 ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required value="<?php echo htmlspecialchars($direccion ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="codigoPostal" class="form-label">Código Postal</label>
                        <input type="text" class="form-control" id="codigoPostal" name="codigoPostal" required value="<?php echo htmlspecialchars($codigoPostal ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" required value="<?php echo htmlspecialchars($ciudad ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="pais" class="form-label">País</label>
                        <input type="text" class="form-control" id="pais" name="pais" required value="<?php echo htmlspecialchars($pais ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                <p class="text-center mt-3">¿Ya tienes cuenta? <a href="/login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>