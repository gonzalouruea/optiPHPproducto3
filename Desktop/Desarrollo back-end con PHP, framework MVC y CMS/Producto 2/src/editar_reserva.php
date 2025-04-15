<?php
session_start();
require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Obtener rol del usuario
$usuarioActual = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$usuarioActual->execute([$_SESSION['email']]);
$rolUsuario = $usuarioActual->fetchColumn();

// Verificar si se proporcionó un ID de reserva
if (!isset($_GET['id'])) {
    header("Location: mis_reservas.php?error=No se especificó la reserva");
    exit;
}

// Obtener la reserva
$stmt = $db->prepare("SELECT r.*, 
    tr.Descripción as tipo_reserva_nombre,
    h.usuario as hotel_nombre,
    v.Descripción as vehiculo_nombre
    FROM transfer_reservas r
    LEFT JOIN transfer_tipo_reserva tr ON r.id_tipo_reserva = tr.id_tipo_reserva
    LEFT JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
    LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
    WHERE r.id_reserva = ?");
$stmt->execute([$_GET['id']]);
$reserva = $stmt->fetch();

// Verificar si la reserva existe y si el usuario tiene permiso para editarla
if (!$reserva || ($reserva['email_cliente'] !== $_SESSION['email'] && $rolUsuario !== 'admin')) {
    header("Location: mis_reservas.php?error=No tiene permiso para editar esta reserva");
    exit;
}

// Verificar restricción de 48 horas para usuarios no administradores
if ($rolUsuario !== 'admin') {
    $fechaReserva = strtotime($reserva['fecha_entrada'] ?? $reserva['fecha_vuelo_salida']);
    if ($fechaReserva - time() < 48 * 3600) {
        header("Location: mis_reservas.php?error=No se pueden modificar reservas con menos de 48 horas de antelación");
        exit;
    }
}

// Obtener listas para los selectores
$vehiculos = $db->query("SELECT * FROM transfer_vehiculo")->fetchAll();
$hoteles = $db->query("SELECT * FROM transfer_hotel")->fetchAll();

// Procesar formulario de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $stmt = $db->prepare("DELETE FROM transfer_reservas WHERE id_reserva = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: mis_reservas.php?success=Reserva cancelada correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "Error al cancelar la reserva: " . $e->getMessage();
    }
}

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $sql = "UPDATE transfer_reservas SET 
            id_hotel = :hotel,
            id_vehiculo = :vehiculo,
            fecha_entrada = :fecha_entrada,
            hora_entrada = :hora_entrada,
            numero_vuelo_entrada = :num_vuelo_entrada,
            origen_vuelo_entrada = :origen_vuelo,
            fecha_vuelo_salida = :fecha_salida,
            hora_vuelo_salida = :hora_salida,
            hora_recogida = :hora_recogida,
            num_viajeros = :num_viajeros,
            fecha_modificacion = NOW()
            WHERE id_reserva = :id_reserva";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':hotel' => $_POST['id_hotel'],
            ':vehiculo' => $_POST['id_vehiculo'],
            ':fecha_entrada' => $_POST['fecha_entrada'] ?? null,
            ':hora_entrada' => $_POST['hora_entrada'] ?? null,
            ':num_vuelo_entrada' => $_POST['numero_vuelo_entrada'] ?? null,
            ':origen_vuelo' => $_POST['origen_vuelo_entrada'] ?? null,
            ':fecha_salida' => $_POST['fecha_vuelo_salida'] ?? null,
            ':hora_salida' => $_POST['hora_vuelo_salida'] ?? null,
            ':hora_recogida' => $_POST['hora_recogida'] ?? null,
            ':num_viajeros' => $_POST['num_viajeros'],
            ':id_reserva' => $_GET['id']
        ]);

        header("Location: mis_reservas.php?success=Reserva actualizada correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "Error al actualizar la reserva: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container my-4">
        <h2>Editar Reserva <?= htmlspecialchars($reserva['localizador']) ?></h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Detalles de la Reserva</h5>
                <p class="card-text">
                    <strong>Tipo:</strong> <?= htmlspecialchars($reserva['tipo_reserva_nombre']) ?><br>
                    <strong>Cliente:</strong> <?= htmlspecialchars($reserva['email_cliente']) ?><br>
                    <strong>Fecha de reserva:</strong> <?= htmlspecialchars($reserva['fecha_reserva']) ?>
                </p>
            </div>
        </div>

        <form method="POST" id="formReserva">
            <input type="hidden" name="action" value="update">
            
            <div class="mb-3">
                <label class="form-label">Hotel</label>
                <select class="form-select" name="id_hotel" required>
                    <?php foreach ($hoteles as $hotel): ?>
                        <option value="<?= $hotel['id_hotel'] ?>" <?= $hotel['id_hotel'] == $reserva['id_hotel'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hotel['nombre'] ?? 'Hotel '.$hotel['id_hotel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Vehículo</label>
                <select class="form-select" name="id_vehiculo" required>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <option value="<?= $vehiculo['id_vehiculo'] ?>" <?= $vehiculo['id_vehiculo'] == $reserva['id_vehiculo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vehiculo['Descripción']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de Viajeros</label>
                <input type="number" class="form-control" name="num_viajeros" value="<?= htmlspecialchars($reserva['num_viajeros']) ?>" required>
            </div>

            <?php if ($reserva['id_tipo_reserva'] == 1 || $reserva['id_tipo_reserva'] == 3): ?>
            <div class="mb-3">
                <label class="form-label">Fecha de Llegada</label>
                <input type="date" class="form-control" name="fecha_entrada" value="<?= htmlspecialchars($reserva['fecha_entrada']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hora de Llegada</label>
                <input type="time" class="form-control" name="hora_entrada" value="<?= htmlspecialchars($reserva['hora_entrada']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de Vuelo (Llegada)</label>
                <input type="text" class="form-control" name="numero_vuelo_entrada" value="<?= htmlspecialchars($reserva['numero_vuelo_entrada']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Origen del Vuelo</label>
                <input type="text" class="form-control" name="origen_vuelo_entrada" value="<?= htmlspecialchars($reserva['origen_vuelo_entrada']) ?>">
            </div>
            <?php endif; ?>

            <?php if ($reserva['id_tipo_reserva'] == 2 || $reserva['id_tipo_reserva'] == 3): ?>
            <div class="mb-3">
                <label class="form-label">Fecha de Salida</label>
                <input type="date" class="form-control" name="fecha_vuelo_salida" value="<?= htmlspecialchars($reserva['fecha_vuelo_salida']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hora de Salida</label>
                <input type="time" class="form-control" name="hora_vuelo_salida" value="<?= htmlspecialchars($reserva['hora_vuelo_salida']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hora de Recogida</label>
                <input type="time" class="form-control" name="hora_recogida" value="<?= htmlspecialchars($reserva['hora_recogida']) ?>" required>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                
                <?php if ($rolUsuario === 'admin' || time() < strtotime($reserva['fecha_entrada'] ?? $reserva['fecha_vuelo_salida']) - 48*3600): ?>
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea cancelar esta reserva?')" formaction="?id=<?= $_GET['id'] ?>" name="action" value="delete">
                    Cancelar Reserva
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
