<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';
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

$error = '';
$exito = '';

    if (empty($id_vehiculo) || empty($id_hotel) || empty($id_destino) || empty($id_tipo_reserva) || empty($email_cliente) || empty($num_viajeros)) {
        $error = "Todos los campos obligatorios deben estar completos.";
    } elseif (!filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
        $error = "El email del cliente no es válido.";
    } elseif ($num_viajeros < 1) {
        $error = "El número de viajeros debe ser al menos 1.";
    } else {
        // Validar las 48 horas de antelación para usuarios (no aplica a administradores)
        if (!$esAdmin) {
            $fechaReferencia = null;
            if ($fecha_entrada && ($id_tipo_reserva == 1 || $id_tipo_reserva == 3)) { // aeropuerto_hotel o ida_vuelta
                $fechaReferencia = new DateTime("$fecha_entrada $hora_entrada");
            } elseif ($fecha_vuelo_salida && ($id_tipo_reserva == 2 || $id_tipo_reserva == 3)) { // hotel_aeropuerto o ida_vuelta
                $fechaReferencia = new DateTime("$fecha_vuelo_salida $hora_vuelo_salida");
            }

            if ($fechaReferencia) {
                $ahora = new DateTime();
                $intervalo = $ahora->diff($fechaReferencia);
                $horasDiferencia = ($intervalo->days * 24) + $intervalo->h;
                if ($horasDiferencia < 48) {
                    $error = "Las reservas deben realizarse con al menos 48 horas de antelación.";
                }
            } else {
                $error = "Debes especificar al menos una fecha de trayecto.";
            }
        }

        if (empty($error)) {
            try {
                // Verificar si el email_cliente existe en transfer_viajeros
                $sql = "SELECT id_viajero FROM transfer_viajeros WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$email_cliente]);
                if (!$stmt->fetch()) {
                    $error = "El email del cliente no está registrado.";
                } else {
                    // Generar localizador único
                    do {
                        $localizador = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                        $sql = "SELECT id_reserva FROM transfer_reservas WHERE localizador = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$localizador]);
                    } while ($stmt->fetch());

                    // Insertar la reserva
                    $sql = "INSERT INTO transfer_reservas (
                        localizador, id_hotel, id_destino, id_tipo_reserva, email_cliente, 
                        fecha_reserva, fecha_modificacion, fecha_entrada, hora_entrada, 
                        numero_vuelo_entrada, origen_vuelo_entrada, fecha_vuelo_salida, 
                        hora_vuelo_salida, num_viajeros, id_vehiculo
                    ) VALUES (
                        :localizador, :id_hotel, :id_destino, :id_tipo_reserva, :email_cliente, 
                        NOW(), NOW(), :fecha_entrada, :hora_entrada, 
                        :numero_vuelo_entrada, :origen_vuelo_entrada, :fecha_vuelo_salida, 
                        :hora_vuelo_salida, :num_viajeros, :id_vehiculo
                    )";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        ':localizador' => $localizador,
                        ':id_hotel' => $id_hotel ?: null,
                        ':id_destino' => $id_destino,
                        ':id_tipo_reserva' => $id_tipo_reserva,
                        ':email_cliente' => $email_cliente,
                        ':fecha_entrada' => $fecha_entrada ?: null,
                        ':hora_entrada' => $hora_entrada ?: null,
                        ':numero_vuelo_entrada' => $numero_vuelo_entrada ?: null,
                        ':origen_vuelo_entrada' => $origen_vuelo_entrada ?: null,
                        ':fecha_vuelo_salida' => $fecha_vuelo_salida ?: null,
                        ':hora_vuelo_salida' => $hora_vuelo_salida ? (new DateTime("$fecha_vuelo_salida $hora_vuelo_salida"))->format('Y-m-d H:i:s') : null,
                        ':num_viajeros' => $num_viajeros,
                        ':id_vehiculo' => $id_vehiculo
                    ]);

                    // Enviar email al cliente (simulado)
                    $to = $email_cliente;
                    $subject = "Confirmación de Reserva - Isla-Transfers";
                    $message = "Su reserva ha sido confirmada.\nLocalizador: $localizador\nHotel: $id_hotel\nNúmero de viajeros: $num_viajeros";
                    $headers = "From: no-reply@isla-transfers.com";
                    if (!mail($to, $subject, $message, $headers)) {
                        $error = "Reserva creada, pero hubo un error al enviar el email de confirmación.";
                    } else {
                        $exito = "Reserva creada con éxito. Localizador: $localizador";
                    }
                }
            } catch (PDOException $e) {
                $error = "Error al crear la reserva: " . $e->getMessage();
            }
        }
    }
</body>
</html>