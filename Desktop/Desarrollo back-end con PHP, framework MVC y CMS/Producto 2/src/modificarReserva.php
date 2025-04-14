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

// Obtener datos de la reserva
if (!isset($_GET['id_reserva'])) {
    header("Location: visualizarReservas.php?error=" . urlencode("ID de reserva no especificado."));
    exit;
}

$id_reserva = $_GET['id_reserva'];
$sql = "SELECT * FROM transfer_reservas WHERE id_reserva = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id_reserva]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    header("Location: visualizarReservas.php?error=" . urlencode("Reserva no encontrada."));
    exit;
}

// Obtener datos para los selectores
// Vehículos
$sql = "SELECT * FROM transfer_vehiculo";
$stmt = $db->prepare($sql);
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hoteles
$sql = "SELECT * FROM tranfer_hotel";
$stmt = $db->prepare($sql);
$stmt->execute();
$hoteles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tipos de reserva
$sql = "SELECT * FROM transfer_tipo_reserva";
$stmt = $db->prepare($sql);
$stmt->execute();
$tipos_reserva = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Modificar reserva
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_reserva'])) {
    $id_vehiculo = trim($_POST['id_vehiculo']);
    $id_hotel = trim($_POST['id_hotel']);
    $id_destino = trim($_POST['id_destino']);
    $id_tipo_reserva = trim($_POST['id_tipo_reserva']);
    $email_cliente = trim($_POST['email_cliente']);
    $num_viajeros = trim($_POST['num_viajeros']);
    $fecha_entrada = trim($_POST['fecha_entrada']) ?: null;
    $hora_entrada = trim($_POST['hora_entrada']) ?: null;
    $numero_vuelo_entrada = trim($_POST['numero_vuelo_entrada']) ?: null;
    $origen_vuelo_entrada = trim($_POST['origen_vuelo_entrada']) ?: null;
    $fecha_vuelo_salida = trim($_POST['fecha_vuelo_salida']) ?: null;
    $hora_vuelo_salida = trim($_POST['hora_vuelo_salida']) ?: null;

    if (!empty($id_vehiculo) && !empty($id_hotel) && !empty($id_destino) && !empty($id_tipo_reserva) && !empty($email_cliente) && !empty($num_viajeros)) {
        $sql = "UPDATE transfer_reservas SET 
                    id_vehiculo = ?, 
                    id_hotel = ?, 
                    id_destino = ?, 
                    id_tipo_reserva = ?, 
                    email_cliente = ?, 
                    num_viajeros = ?, 
                    fecha_entrada = ?, 
                    hora_entrada = ?, 
                    numero_vuelo_entrada = ?, 
                    origen_vuelo_entrada = ?, 
                    fecha_vuelo_salida = ?, 
                    hora_vuelo_salida = ?, 
                    fecha_modificacion = NOW() 
                WHERE id_reserva = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $id_vehiculo,
            $id_hotel ?: null,
            $id_destino,
            $id_tipo_reserva,
            $email_cliente,
            $num_viajeros,
            $fecha_entrada,
            $hora_entrada,
            $numero_vuelo_entrada,
            $origen_vuelo_entrada,
            $fecha_vuelo_salida,
            $hora_vuelo_salida ? (new DateTime("$fecha_vuelo_salida $hora_vuelo_salida"))->format('Y-m-d H:i:s') : null,
            $id_reserva
        ]);
        header("Location: visualizarReservas.php?success=" . urlencode("Reserva modificada con éxito."));
        exit;
    } else {
        $error = "Todos los campos obligatorios deben estar completos.";
    }
}

// Eliminar reserva
if (isset($_POST['delete_reserva'])) {
    $sql = "DELETE FROM transfer_reservas WHERE id_reserva = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_reserva]);
    header("Location: visualizarReservas.php?success=" . urlencode("Reserva eliminada con éxito."));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Reserva - Isla-Transfers</title>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Modificar Reserva</h2>
    </div>

    <!-- Mensajes de error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulario para modificar reserva -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" class="needs-validation" novalidate>
                <!-- Vehículo -->
                <div class="mb-3">
                    <label for="id_vehiculo" class="form-label">Vehículo</label>
                    <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                        <option value="">Selecciona un vehículo</option>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <option value="<?php echo $vehiculo['id_vehiculo']; ?>" <?php echo $vehiculo['id_vehiculo'] == $reserva['id_vehiculo'] ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $hotel['id_hotel']; ?>" <?php echo $hotel['id_hotel'] == $reserva['id_hotel'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hotel['usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona un hotel.</div>
                </div>

                <!-- Destino (mismo valor que id_hotel por ahora) -->
                <input type="hidden" id="id_destino" name="id_destino" value="<?php echo htmlspecialchars($reserva['id_destino']); ?>">

                <!-- Tipo de Reserva -->
                <div class="mb-3">
                    <label for="id_tipo_reserva" class="form-label">Tipo de Trayecto</label>
                    <select class="form-select" id="id_tipo_reserva" name="id_tipo_reserva" required onchange="mostrarCampos()">
                        <option value="">Selecciona un tipo de trayecto</option>
                        <?php foreach ($tipos_reserva as $tipo): ?>
                            <option value="<?php echo $tipo['id_tipo_reserva']; ?>" <?php echo $tipo['id_tipo_reserva'] == $reserva['id_tipo_reserva'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo['Descripción']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Por favor, selecciona un tipo de trayecto.</div>
                </div>

                <!-- Email del Cliente -->
                <div class="mb-3">
                    <label for="email_cliente" class="form-label">Email del Cliente</label>
                    <input type="email" class="form-control" id="email_cliente" name="email_cliente" value="<?php echo htmlspecialchars($reserva['email_cliente']); ?>" required>
                    <div class="invalid-feedback">Por favor, introduce un email válido.</div>
                </div>

                <!-- Número de Viajeros -->
                <div class="mb-3">
                    <label for="num_viajeros" class="form-label">Número de Viajeros</label>
                    <input type="number" class="form-control" id="num_viajeros" name="num_viajeros" min="1" value="<?php echo htmlspecialchars($reserva['num_viajeros']); ?>" required>
                    <div class="invalid-feedback">Por favor, introduce el número de viajeros.</div>
                </div>

                <!-- Campos dinámicos para llegada (aeropuerto → hotel) -->
                <div id="campos_llegada" style="display: none;">
                    <div class="mb-3">
                        <label for="fecha_entrada" class="form-label">Fecha de Llegada</label>
                        <input type="date" class="form-control" id="fecha_entrada" name="fecha_entrada" value="<?php echo htmlspecialchars($reserva['fecha_entrada']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="hora_entrada" class="form-label">Hora de Llegada</label>
                        <input type="time" class="form-control" id="hora_entrada" name="hora_entrada" value="<?php echo htmlspecialchars($reserva['hora_entrada']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="numero_vuelo_entrada" class="form-label">Número de Vuelo de Llegada</label>
                        <input type="text" class="form-control" id="numero_vuelo_entrada" name="numero_vuelo_entrada" value="<?php echo htmlspecialchars($reserva['numero_vuelo_entrada']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="origen_vuelo_entrada" class="form-label">Origen del Vuelo de Llegada</label>
                        <input type="text" class="form-control" id="origen_vuelo_entrada" name="origen_vuelo_entrada" value="<?php echo htmlspecialchars($reserva['origen_vuelo_entrada']); ?>">
                    </div>
                </div>

                <!-- Campos dinámicos para salida (hotel → aeropuerto) -->
                <div id="campos_salida" style="display: none;">
                    <div class="mb-3">
                        <label for="fecha_vuelo_salida" class="form-label">Fecha de Salida</label>
                        <input type="date" class="form-control" id="fecha_vuelo_salida" name="fecha_vuelo_salida" value="<?php echo htmlspecialchars($reserva['fecha_vuelo_salida']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="hora_vuelo_salida" class="form-label">Hora de Salida</label>
                        <input type="time" class="form-control" id="hora_vuelo_salida" name="hora_vuelo_salida" value="<?php echo $reserva['hora_vuelo_salida'] ? (new DateTime($reserva['hora_vuelo_salida']))->format('H:i') : ''; ?>">
                    </div>
                </div>

                <button type="submit" name="update_reserva" class="btn btn-primary w-100 mb-2">Guardar Cambios</button>
                <button type="submit" name="delete_reserva" class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reserva?');">Eliminar Reserva</button>
            </form>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="visualizarReservas.php" class="btn btn-primary">Volver a Visualizar Reservas</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
// Validación del formulario con Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')

            // Copiar el valor de id_hotel a id_destino
            var idHotel = document.getElementById('id_hotel').value;
            document.getElementById('id_destino').value = idHotel;
        }, false)
    })
})();

// Mostrar u ocultar campos según el tipo de reserva
function mostrarCampos() {
    var tipoReserva = document.getElementById('id_tipo_reserva').value;
    var camposLlegada = document.getElementById('campos_llegada');
    var camposSalida = document.getElementById('campos_salida');

    camposLlegada.style.display = 'none';
    camposSalida.style.display = 'none';

    if (tipoReserva == 1 || tipoReserva == 3) { // aeropuerto_hotel o ida_vuelta
        camposLlegada.style.display = 'block';
    }
    if (tipoReserva == 2 || tipoReserva == 3) { // hotel_aeropuerto o ida_vuelta
        camposSalida.style.display = 'block';
    }
}

// Ejecutar al cargar la página para mostrar los campos correctos
mostrarCampos();
</script>
</body>
</html>