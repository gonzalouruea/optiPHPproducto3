<?php
// VERIFICAR QUE NO HAYA NADA ANTES DE ESTA LÍNEA
// Iniciar buffer inmediatamente
if (!ob_get_level()) ob_start();

// Verificar si headers ya fueron enviados
if (headers_sent($file, $line)) {
    ob_end_clean();
    die("Error: Headers enviados previamente en $file línea $line");
}

// Iniciar sesión segura
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'use_strict_mode' => true,
        'use_cookies' => 1,
        'cookie_httponly' => 1,
        'cookie_secure' => 0 // Cambiar a 1 en producción con HTTPS
    ]);
}

require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $db->prepare("SELECT id_viajero, email, nombre, password, rol FROM transfer_viajeros WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Regenerar ID de sesión para seguridad
            session_regenerate_id(true);
            
            $_SESSION = [
                'id' => $usuario['id_viajero'],
                'email' => $usuario['email'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['rol'],
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ];

            // Limpiar buffer y redirigir
            ob_end_clean();
            header("Location: " . ($usuario['rol'] === 'admin' ? 'admin_menu.php' : 'index.php'));
            exit;
        }

        // Credenciales incorrectas
        $_SESSION['login_error'] = "Credenciales inválidas";
        ob_end_clean();
        header("Location: login.php");
        exit;

    } catch (PDOException $e) {
        error_log("Error de login: " . $e->getMessage());
        $_SESSION['login_error'] = "Error del sistema";
        ob_end_clean();
        header("Location: login.php");
        exit;
    }
}

// Si no es POST, redirigir
ob_end_clean();
header("Location: login.php");
exit;