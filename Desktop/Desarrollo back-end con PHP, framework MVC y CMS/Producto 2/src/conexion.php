<?php
// NO debe haber espacios, saltos de línea o caracteres antes de <?php
define('DB_HOST','db');
define('DB_USER','user');
define('DB_PASS', 'user_password');
define('DB_NAME', 'viajes');

try {
    $db = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME, 
        DB_USER, 
        DB_PASS,
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Evita mostrar errores sensibles en producción
    error_log("Error de conexión: " . $e->getMessage());
    die("Error en el servidor. Por favor intenta más tarde.");
}
// NO debe haber espacios o saltos de línea después del cierre ?>