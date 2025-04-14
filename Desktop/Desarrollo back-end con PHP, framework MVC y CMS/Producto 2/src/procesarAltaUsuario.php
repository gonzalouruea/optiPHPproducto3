<?php 
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $codPostal = $_POST['codPostal'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $password = $_POST['password'];
    
    if(empty($nombre)|| empty($apellido1) || empty($apellido2) || empty($email) || empty($direccion) || empty($codPostal) || empty($ciudad) || empty($pais) || empty($password)){
        echo "Debes rellenar todos los campos";
    } else {
        try {
            // Verificar si el email ya existe
            $sqlCheck = "SELECT COUNT(*) FROM transfer_viajeros WHERE email = :email";
            $stmtCheck = $db->prepare($sqlCheck);
            $stmtCheck->bindParam(':email', $email);
            $stmtCheck->execute();
            
            if ($stmtCheck->fetchColumn() > 0) {
                $error = "El email ya está registrado. Por favor usa otro email.";
            } else {
                // Insertar nuevo usuario
                $sql = "INSERT INTO transfer_viajeros(nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email, password, rol)
                        VALUES (:nombre, :apellido1, :apellido2, :direccion, :codigoPostal, :ciudad, :pais, :email, :password, 'usuario')";

                $stmt = $db->prepare($sql);

                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido1', $apellido1);
                $stmt->bindParam(':apellido2', $apellido2);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':codigoPostal', $codPostal);
                $stmt->bindParam(':ciudad', $ciudad);
                $stmt->bindParam(':pais', $pais);
                $stmt->bindParam(':email', $email);

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $hashedPassword);

                $stmt->execute();

                $exito = "El usuario ha sido creado con éxito";
            }
        } catch(PDOException $e) {
            $error = "Error al crear el usuario: " . $e->getMessage();
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
    <title>Document</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <h2 id="exito" class="alert alert-<?php echo isset($exito) ? 'success' : 'danger'; ?>">
                <?php echo $exito ?? $error; ?>
            </h2>
            <?php if(isset($error)): ?>
                <a href="altaUsuario.php" class="btn btn-primary">Volver al registro</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>