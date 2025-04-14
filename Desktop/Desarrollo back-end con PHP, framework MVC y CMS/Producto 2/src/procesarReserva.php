<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_entrada = $_POST['fecha_entrada'];
    $hora_entrada = $_POST['hora_entrada'];
    $num_viajeros = $_POST['num_viajeros'];
    $id_vehiculo = $_POST['id_vehiculo'];
    
    try {
        // Generar localizador aleatorio
        $localizador = strtoupper(substr(md5(uniqid()), 0, 8));
        
        $sql = "INSERT INTO transfer_reservas (localizador, email_cliente, fecha_reserva, fecha_modificacion, 
                fecha_entrada, hora_entrada, num_viajeros, id_vehiculo)
                VALUES (:localizador, :email_cliente, NOW(), NOW(), 
                :fecha_entrada, :hora_entrada, :num_viajeros, :id_vehiculo)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':localizador', $localizador);
        $stmt->bindParam(':email_cliente', $_SESSION['email']);
        $stmt->bindParam(':fecha_entrada', $fecha_entrada);
        $stmt->bindParam(':hora_entrada', $hora_entrada);
        $stmt->bindParam(':num_viajeros', $num_viajeros);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        
        $stmt->execute();
        
        $exito = "Reserva creada con éxito. Localizador: " . $localizador;
    } catch(PDOException $e) {
        $error = "Error al crear reserva: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Procesar Reserva</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <h2 id="exito" class="alert alert-<?php echo isset($exito) ? 'success' : 'danger'; ?>">
                <?php echo $exito ?? $error ?? ''; ?>
            </h2>
            <a href="reserva.php" class="btn btn-primary">Volver</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>