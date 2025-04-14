<?php
define('DB_HOST','db');
define('DB_USER','user');
define('DB_PASS', 'user_password');
define('DB_NAME', 'viajes');

try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}