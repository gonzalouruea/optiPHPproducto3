<?php
ob_start();
session_start();
require 'conexion.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Email y contraseña son obligatorios.");
}

$sql = "SELECT id_viajero, email, password, rol FROM transfer_viajeros WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$email]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $usuario['password'] = trim($usuario['password']);
    
    if (password_verify($password, $usuario['password'])) {
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['id_viajero'] = $usuario['id_viajero'];
        $_SESSION['admin'] = ($usuario['rol'] == 'admin') ? 1 : 0; // Convertir a formato numérico para compatibilidad
        
        header("Location: index.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}
?>