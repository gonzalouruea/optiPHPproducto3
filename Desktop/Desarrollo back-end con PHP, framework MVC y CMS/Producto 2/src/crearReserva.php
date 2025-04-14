<?php
session_start();

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

if (!$usuario) {
    header("Location: index.php");
    exit;
}

$esAdmin = $usuario['rol'] === 'admin';

// Obtener datos para los selectores
// Vehículos
$sql = "SELECT * FROM transfer_vehiculo";
$stmt = $db->prepare($sql);
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hoteles (usados también como destinos)
$sql = "SELECT * FROM tranfer_hotel";
$stmt = $db->prepare($sql);
$stmt->execute();
$hoteles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tipos de reserva
$sql = "SELECT * FROM transfer_tipo_reserva";
$stmt = $db->prepare($sql);
$stmt->execute();
$tipos_reserva = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Crear Reserva - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Crear Nueva Reserva</h2>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="procesarCrearReserva.php" method="POST" class="needs-validation" novalidate>
                <!-- Vehículo -->
                <div class="mb-3">
                    <label for="id_vehiculo" class="form-label">Vehículo</label>
                    <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                        <option value="">Selecciona un vehículo</option>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <option value="<?php echo $vehiculo['id_vehiculo']; ?>">
                                <?php echo htmlspecialchars($vehiculo['Descripción']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona un vehículo.</div>
                </div>

                <!-- Hotel -->
                <div class="mb-3">
                    <label for="id_hotel" class="form-label">Hotel</label>
                    <select class="form-select" id="id_hotel" name="id_hotel" required>
                        <option value="">Selecciona un hotel</option>
                        <?php foreach ($hoteles as $hotel): ?>
                            <option value="<?php echo $hotel['id_hotel']; ?>">
                                <?php echo htmlspecialchars($hotel['usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona un hotel.</div>
                </div>

                <!-- Destino (mismo valor que id_hotel por ahora) -->
                <input type="hidden" id="id_destino" name="id_destino" value="">

                <!-- Tipo de Reserva -->
                <div class="mb-3">
                    <label for="id_tipo_reserva" class="form-label">Tipo de Trayecto</label>
                    <select class="form-select" id="id_tipo_reserva" name="id_tipo_reserva" required onchange="mostrarCampos()">
                        <option value="">Selecciona un tipo de trayecto</option>
                        <?php foreach ($tipos_reserva as $tipo): ?>
                            <option value="<?php echo $tipo['id_tipo_reserva']; ?>">
                                <?php echo htmlspecialchars($tipo['Descripción']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona un tipo de trayecto.</div>
                </div>

                <!-- Email del Cliente -->
                <div class="mb-3">
                    <label for="email_cliente" class="form-label">Email del Cliente</label>
                    <input type="email" class="form-control" id="email_cliente" name="email_cliente" value="<?php echo $esAdmin ? '' : htmlspecialchars($email); ?>" <?php echo $esAdmin ? '' : 'readonly'; ?> required>
                    <div class="invalid-feedback">Por favor, introduce un email válido.</div>
                </div>

                <!-- Número de Viajeros -->
                <div class="mb-3">
                    <label for="num_viajeros" class="form-label">Número de Viajeros</label>
                    <input type="number" class="form-control" id="num_viajeros" name="num_viajeros" min="1" required>
                    <div class="invalid-feedback">Por favor, introduce el número de viajeros.</div>
                </div>

                <!-- Campos dinámicos para llegada (aeropuerto → hotel) -->
                <div id="campos_llegada" style="display: none;">
                    <div class="mb-3">
                        <label for="fecha_entrada" class="form-label">Fecha de Llegada</label>
                        <input type="date" class="form-control" id="fecha_entrada" name="fecha_entrada">
                    </div>
                    <div class="mb-3">
                        <label for="hora_entrada" class="form-label">Hora de Llegada</label>
                        <input type="time" class="form-control" id="hora_entrada" name="hora_entrada">
                    </div>
                    <div class="mb-3">
                        <label for="numero_vuelo_entrada" class="form-label">Número de Vuelo de Llegada</label>
                        <input type="text" class="form-control" id="numero_vuelo_entrada" name="numero_vuelo_entrada">
                    </div>
                    <div class="mb-3">
                        <label for="origen_vuelo_entrada" class="form-label">Origen del Vuelo de Llegada</label>
                        <input type="text" class="form-control" id="origen_vuelo_entrada" name="origen_vuelo_entrada">
                    </div>
                </div>

                <!-- Campos dinámicos para salida (hotel → aeropuerto) -->
                <div id="campos_salida" style="display: none;">
                    <div class="mb-3">
                        <label for="fecha_vuelo_salida" class="form-label">Fecha de Salida</label>
                        <input type="date" class="form-control" id="fecha_vuelo_salida" name="fecha_vuelo_salida">
                    </div>
                    <div class="mb-3">
                        <label for="hora_vuelo_salida" class="form-label">Hora de Salida</label>
                        <input type="time" class="form-control" id="hora_vuelo_salida" name="hora_vuelo_salida">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Crear Reserva</button>
            </form>
        </div>
    </div>
    <div class="text-center mt-3">
        <a href="<?php echo $esAdmin ? 'panelAdmin.php' : 'panelUsuario.php'; ?>" class="btn btn-primary">Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>