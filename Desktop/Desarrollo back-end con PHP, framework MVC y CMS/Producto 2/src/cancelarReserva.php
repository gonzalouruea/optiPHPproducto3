<?php
session_start();
require_once 'conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ver_reservas.php");
    exit;
}

$id_reserva = $_GET['id'];

try {
    $db->query("DELETE FROM transfer_reservas WHERE id_reserva = $id_reserva");
    $_SESSION['exito'] = "Reserva cancelada correctamente";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cancelar la reserva: " . $e->getMessage();
}

header("Location: ver_reservas.php");
exit;
?>