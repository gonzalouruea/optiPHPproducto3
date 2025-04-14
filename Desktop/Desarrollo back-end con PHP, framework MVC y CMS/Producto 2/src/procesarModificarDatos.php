<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

// Comprobar que el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $currentEmail = $_SESSION['email'];

    // Validar campos obligatorios
    if (empty($nombre) || empty($email)) {
        $error = "El nombre y el correo electrónico son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } else {
        try {
            // Verificar si el nuevo email ya está en uso (excepto si es el mismo del usuario actual)
            $sqlCheckEmail = "SELECT email FROM transfer_viajeros WHERE email = ? AND email != ?";
            $stmtCheck = $db->prepare($sqlCheckEmail);
            $stmtCheck->execute([$email, $currentEmail]);
            if ($stmtCheck->fetch()) {
                $error = "El correo electrónico ya está en uso.";
            } else {
                // Preparar la consulta de actualización
                $sql = "UPDATE transfer_viajeros SET nombre = :nombre, email = :email";
                $params = [
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':currentEmail' => $currentEmail
                ];

                // Si se proporcionó una contraseña, actualizarla
                if (!empty($password)) {
                    $sql .= ", password = :password";
                    $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                $sql .= " WHERE email = :currentEmail";

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // Actualizar el email en la sesión si cambió
                $_SESSION['email'] = $email;

                $exito = "Tus datos han sido actualizados con éxito.";
            }
        } catch (PDOException $e) {
            $error = "Error al actualizar los datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Resultado - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <?php if (isset($exito)): ?>
                <h2 class="alert alert-success"><?php echo $exito; ?></h2>
            <?php elseif (isset($error)): ?>
                <h2 class="alert alert-danger"><?php echo $error; ?></h2>
            <?php endif; ?>
            <a href="index.php" class="btn btn-primary mt-3">Volver al inicio</a>
        </div>
    </div>
</div>

<!-- Archivos JavaScript de Bootstrap (incluye Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>