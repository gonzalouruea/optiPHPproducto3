<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['email'])) {
        header("Location: /login.php");
        exit;
    }
}

function requireRole($role) {
    require_once 'conexion.php';
    global $db;
    
    $email = $_SESSION['email'];
    $sql = "SELECT rol FROM transfer_viajeros WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario || $usuario['rol'] !== $role) {
        header("Location: /index.php");
        exit;
    }
}