<?php
$host = 'db';
$dbname = 'viajes';
$username = 'user';
$password = 'user_password';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
}
?>