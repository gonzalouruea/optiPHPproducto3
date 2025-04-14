<?php
session_start();
require_once 'conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Obtener hoteles y vehículos
$hoteles = $db->query("SELECT * FROM transfer_hotel")->fetchAll();
$vehiculos = $db->query("SELECT * FROM transfer_vehiculo")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_reserva = $_POST['tipo_reserva'];
    $email_cliente = $_POST['email_cliente'];
    $id_hotel = $_POST['hotel'];
    $num_viajeros = $_POST['num_viajeros'];
    $id_vehiculo = $_POST['vehiculo'];
    
    // Generar localizador único
    $localizador = strtoupper(uniqid('RES-'));
    
    // Datos específicos por tipo de reserva
    if ($tipo_reserva == 'aeropuerto-hotel' || $tipo_reserva == 'ida-vuelta') {
        $fecha_entrada = $_POST['fecha_entrada'];
        $hora_entrada = $_POST['hora_entrada'];
        $numero_vuelo_entrada = $_POST['numero_vuelo_entrada'];
        $origen_vuelo_entrada = $_POST['origen_vuelo_entrada'];
    }
    
    if ($tipo_reserva == 'hotel-aeropuerto' || $tipo_reserva == 'ida-vuelta') {
        $fecha_vuelo_salida = $_POST['fecha_vuelo_salida'];
        $hora_vuelo_salida = $_POST['hora_vuelo_salida'];
        $hora_recogida = $_POST['hora_recogida'];
        $numero_vuelo_salida = $_POST['numero_vuelo_salida'];
    }
    
    try {
        $sql = "INSERT INTO transfer_reservas (localizador, id_tipo_reserva, email_cliente, id_hotel, num_viajeros, id_vehiculo, 
                fecha_entrada, hora_entrada, numero_vuelo_entrada, origen_vuelo_entrada,
                fecha_vuelo_salida, hora_vuelo_salida, hora_recogida, numero_vuelo_salida)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $localizador,
            ($tipo_reserva == 'ida-vuelta') ? 3 : ($tipo_reserva == 'aeropuerto-hotel' ? 1 : 2),
            $email_cliente,
            $id_hotel,
            $num_viajeros,
            $id_vehiculo,
            $fecha_entrada ?? null,
            $hora_entrada ?? null,
            $numero_vuelo_entrada ?? null,
            $origen_vuelo_entrada ?? null,
            $fecha_vuelo_salida ?? null,
            $hora_vuelo_salida ?? null,
            $hora_recogida ?? null,
            $numero_vuelo_salida ?? null
        ]);
        
        // Aquí iría el código para enviar el email al cliente
        
        $_SESSION['exito'] = "Reserva creada con éxito. Localizador: $localizador";
        header("Location: ver_reservas.php");
        exit;
        
    } catch (PDOException $e) {
        $error = "Error al crear la reserva: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4">
    <h2>Nueva Reserva</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Tipo de Reserva</label>
                <select class="form-select" name="tipo_reserva" id="tipo_reserva" required>
                    <option value="">Seleccione...</option>
                    <option value="aeropuerto-hotel">Aeropuerto → Hotel</option>
                    <option value="hotel-aeropuerto">Hotel → Aeropuerto</option>
                    <option value="ida-vuelta">Ida y Vuelta</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email del Cliente</label>
                <input type="email" class="form-control" name="email_cliente" required>
            </div>
        </div>

        <!-- Sección Aeropuerto → Hotel -->
        <div id="seccion-aeropuerto-hotel" style="display:none;">
            <h4 class="mt-4">Datos de Llegada</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha de Llegada</label>
                    <input type="date" class="form-control" name="fecha_entrada">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de Llegada</label>
                    <input type="time" class="form-control" name="hora_entrada">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Número de Vuelo</label>
                    <input type="text" class="form-control" name="numero_vuelo_entrada">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Aeropuerto de Origen</label>
                    <input type="text" class="form-control" name="origen_vuelo_entrada">
                </div>
            </div>
        </div>

        <!-- Sección Hotel → Aeropuerto -->
        <div id="seccion-hotel-aeropuerto" style="display:none;">
            <h4 class="mt-4">Datos de Salida</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha del Vuelo</label>
                    <input type="date" class="form-control" name="fecha_vuelo_salida">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora del Vuelo</label>
                    <input type="time" class="form-control" name="hora_vuelo_salida">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de Recogida</label>
                    <input type="time" class="form-control" name="hora_recogida">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número de Vuelo</label>
                    <input type="text" class="form-control" name="numero_vuelo_salida">
                </div>
            </div>
        </div>

        <!-- Datos comunes -->
        <h4 class="mt-4">Información General</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Hotel</label>
                <select class="form-select" name="hotel" required>
                    <option value="">Seleccione un hotel</option>
                    <?php foreach ($hoteles as $hotel): ?>
                        <option value="<?= $hotel['id_hotel'] ?>"><?= $hotel['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vehículo</label>
                <select class="form-select" name="vehiculo" required>
                    <option value="">Seleccione un vehículo</option>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <option value="<?= $vehiculo['id_vehiculo'] ?>"><?= $vehiculo['descripcion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Número de Viajeros</label>
                <input type="number" class="form-control" name="num_viajeros" min="1" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Crear Reserva</button>
            <a href="admin_menu.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('tipo_reserva').addEventListener('change', function() {
    const tipo = this.value;
    
    // Ocultar todas las secciones primero
    document.getElementById('seccion-aeropuerto-hotel').style.display = 'none';
    document.getElementById('seccion-hotel-aeropuerto').style.display = 'none';
    
    // Mostrar las secciones según el tipo seleccionado
    if (tipo === 'aeropuerto-hotel' || tipo === 'ida-vuelta') {
        document.getElementById('seccion-aeropuerto-hotel').style.display = 'block';
    }
    
    if (tipo === 'hotel-aeropuerto' || tipo === 'ida-vuelta') {
        document.getElementById('seccion-hotel-aeropuerto').style.display = 'block';
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>