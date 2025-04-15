<?php
session_start();
require '../conexion.php';

// Verificar sesión y rol de administrador
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

$usuarioActual = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$usuarioActual->execute([$_SESSION['email']]);
$rolUsuario = $usuarioActual->fetchColumn();

if ($rolUsuario !== 'admin') {
    header("Location: ../index.php?error=Acceso denegado");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tipo_reserva'])) {
    try {
        // Primero verificar si hay reservas asociadas
        $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_reservas WHERE id_tipo_reserva = ?");
        $stmt->execute([$_POST['id_tipo_reserva']]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            header("Location: gestionar_tipos_reserva.php?error=No se puede eliminar el tipo de reserva porque tiene reservas asociadas");
            exit;
        }

        // Si no hay reservas asociadas, proceder con la eliminación
        $stmt = $db->prepare("DELETE FROM transfer_tipos_reserva WHERE id_tipo_reserva = ?");
        $stmt->execute([$_POST['id_tipo_reserva']]);
        
        header("Location: gestionar_tipos_reserva.php?success=Tipo de reserva eliminado correctamente");
    } catch (PDOException $e) {
        header("Location: gestionar_tipos_reserva.php?error=Error al eliminar el tipo de reserva: " . urlencode($e->getMessage()));
    }
    exit;
}

header("Location: gestionar_tipos_reserva.php");
exit;
?>
