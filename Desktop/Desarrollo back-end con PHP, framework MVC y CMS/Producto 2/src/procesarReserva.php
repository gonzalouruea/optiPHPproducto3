<?php
session_start();
require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['email']) || !isset($_SESSION['id_viajero'])) {
    header("Location: login.php");
    exit;
}

// Obtener rol del usuario actual
$usuarioActual = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$usuarioActual->execute([$_SESSION['email']]);
$rolUsuario = $usuarioActual->fetchColumn();

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: reserva.php?error=Método no permitido");
    exit;
}

// Validar campos básicos
$camposRequeridos = [
    'tipo_trayecto' => 'Tipo de trayecto',
    'num_viajeros' => 'Número de pasajeros',
    'id_vehiculo' => 'Vehículo',
    'id_hotel' => 'Hotel'
];

// Validar tiempo mínimo de 48 horas para usuarios no administradores
if ($rolUsuario !== 'admin') {
    if (isset($_POST['fecha_entrada']) && isset($_POST['hora_entrada'])) {
        $fechaHoraReserva = strtotime($_POST['fecha_entrada'] . ' ' . $_POST['hora_entrada']);
        $minTime = time() + (48 * 3600); // 48 horas en segundos
        
        if ($fechaHoraReserva < $minTime) {
            header("Location: reserva.php?error=Las reservas deben hacerse con mínimo 48 horas de antelación");
            exit;
        }
    }
}

// Validar que el vehículo exista y esté disponible
$stmt = $db->prepare("SELECT id_vehiculo FROM transfer_vehiculo WHERE id_vehiculo = ?");
$stmt->execute([$_POST['id_vehiculo']]);
if (!$stmt->fetch()) {
    header("Location: reserva.php?error=El vehículo seleccionado no está disponible");
    exit;
}

// Validar que el hotel exista
$stmt = $db->prepare("SELECT id_hotel FROM transfer_hotel WHERE id_hotel = ?");
$stmt->execute([$_POST['id_hotel']]);
if (!$stmt->fetch()) {
    header("Location: reserva.php?error=El hotel seleccionado no existe");
    exit;
}

// Añadir validaciones adicionales según el tipo de trayecto
$tipo_trayecto = $_POST['tipo_trayecto'];
if ($tipo_trayecto == 1 || $tipo_trayecto == 3) {
    $camposRequeridos['fecha_entrada'] = 'Fecha de llegada';
    $camposRequeridos['hora_entrada'] = 'Hora de llegada';
}
if ($tipo_trayecto == 2 || $tipo_trayecto == 3) {
    $camposRequeridos['fecha_vuelo_salida'] = 'Fecha de salida';
    $camposRequeridos['hora_vuelo_salida'] = 'Hora de salida';
    $camposRequeridos['hora_recogida'] = 'Hora de recogida';
}

foreach ($camposRequeridos as $campo => $nombre) {
    if (empty($_POST[$campo])) {
        header("Location: reserva.php?error=El campo $nombre es requerido");
        exit;
    }
}

// Generar localizador único
function generarLocalizador($db) {
    do {
        $localizador = strtoupper(substr(uniqid(), -8));
        $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_reservas WHERE localizador = ?");
        $stmt->execute([$localizador]);
    } while ($stmt->fetchColumn() > 0);
    return $localizador;
}

// Insertar reserva
try {
    $localizador = generarLocalizador($db);
    
    $sql = "INSERT INTO transfer_reservas (
        localizador,
        id_tipo_reserva,
        email_cliente,
        fecha_reserva,
        fecha_modificacion,
        id_hotel,
        id_destino,
        fecha_entrada,
        hora_entrada,
        numero_vuelo_entrada,
        fecha_vuelo_salida,
        hora_vuelo_salida,
        hora_recogida,
        num_viajeros,
        id_vehiculo,
        creado_por_admin
    ) VALUES (
        :localizador,
        :tipo_reserva,
        :email,
        NOW(),
        NOW(),
        :hotel,
        :destino,
        :fecha_entrada,
        :hora_entrada,
        :num_vuelo,
        :fecha_salida,
        :hora_salida,
        :hora_recogida,
        :pasajeros,
        :vehiculo,
        :creado_por_admin
    )";
    
    // Si es admin y se proporciona un id_viajero, obtener el email del viajero
    $email_cliente = $_SESSION['email'];
    if ($rolUsuario === 'admin' && !empty($_POST['id_viajero'])) {
        $stmtEmail = $db->prepare("SELECT email FROM transfer_viajeros WHERE id_viajero = ?");
        $stmtEmail->execute([$_POST['id_viajero']]);
        $email_cliente = $stmtEmail->fetchColumn();
        
        if (!$email_cliente) {
            header("Location: reserva.php?error=Usuario no encontrado");
            exit;
        }
    }
    
    // Preparar la consulta de inserción
    $stmt = $db->prepare($sql);

    $parametros = [
        ':localizador' => $localizador,
        ':tipo_reserva' => $tipo_trayecto,
        ':email' => $email_cliente,
        ':hotel' => $_POST['id_hotel'],
        ':destino' => $_POST['id_hotel'], // Usamos el hotel como destino
        ':fecha_entrada' => $_POST['fecha_entrada'] ?? null,
        ':hora_entrada' => $_POST['hora_entrada'] ?? null,
        ':num_vuelo' => $_POST['numero_vuelo_entrada'] ?? null,
        ':fecha_salida' => $_POST['fecha_vuelo_salida'] ?? null,
        ':hora_salida' => $_POST['hora_vuelo_salida'] ?? null,
        ':hora_recogida' => $_POST['hora_recogida'] ?? null,
        ':pasajeros' => $_POST['num_viajeros'],
        ':vehiculo' => $_POST['id_vehiculo'],
        ':creado_por_admin' => ($rolUsuario === 'admin' ? 1 : 0)
    ];
    
    $stmt->execute($parametros);

    // Enviar email de confirmación
    $to = $_SESSION['email'];
    $subject = "Confirmación de Reserva - Localizador: $localizador";
    $message = "Su reserva ha sido confirmada.\n";
    $message .= "Localizador: $localizador\n";
    $message .= "Tipo de trayecto: " . ($tipo_trayecto == 1 ? 'Aeropuerto → Hotel' : ($tipo_trayecto == 2 ? 'Hotel → Aeropuerto' : 'Ida y Vuelta')) . "\n";
    
    mail($to, $subject, $message);

    header("Location: mis_reservas.php?success=1");
    exit;

} catch (PDOException $e) {
    header("Location: reserva.php?error=Error al guardar: " . $e->getMessage());
    exit;
}
?>
