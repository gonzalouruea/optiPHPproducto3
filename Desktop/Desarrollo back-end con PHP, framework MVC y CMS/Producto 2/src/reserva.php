<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Obtener rol del usuario actual
$usuarioActual = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$usuarioActual->execute([$_SESSION['email']]);
$rolUsuario = $usuarioActual->fetchColumn();

$vehiculos = $db->query("SELECT * FROM transfer_vehiculo")->fetchAll();
$hoteles = $db->query("SELECT * FROM transfer_hotel")->fetchAll();

// Si es admin, obtener lista de usuarios
$usuarios = [];
if ($rolUsuario === 'admin') {
    $usuarios = $db->query("SELECT id_viajero, email, nombre FROM transfer_viajeros ORDER BY email")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .campo-dinamico { margin-bottom: 15px; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container my-5">
        <h2 class="text-center mb-4">Nueva Reserva</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="procesarReserva.php" method="POST" id="formReserva">
            <?php if ($rolUsuario === 'admin'): ?>
            <!-- Selector de Usuario (solo para admin) -->
            <div class="mb-3">
                <label class="form-label fw-bold">Usuario*</label>
                <select class="form-select" name="id_viajero" required>
                    <option value="" selected disabled>Seleccione usuario...</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_viajero'] ?>">
                            <?= htmlspecialchars($usuario['email']) ?>
                            <?php if ($usuario['nombre']): ?>
                                (<?= htmlspecialchars($usuario['nombre']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Tipo de Trayecto -->
            <div class="mb-3">
                <label class="form-label fw-bold">Tipo de Trayecto*</label>
                <select class="form-select" name="tipo_trayecto" id="tipoTrayecto" required>
                    <option value="" selected disabled>Seleccione...</option>
                    <option value="1">Aeropuerto → Hotel</option>
                    <option value="2">Hotel → Aeropuerto</option>
                    <option value="3">Ida y Vuelta</option>
                </select>
            </div>

            <!-- Campos dinámicos -->
            <div id="camposDinamicos">
                <!-- Los campos se generarán dinámicamente aquí -->
            </div>

            <!-- Campos comunes -->
            <div class="mb-3">
                <label class="form-label fw-bold">Número de Pasajeros*</label>
                <input type="number" class="form-control" name="num_viajeros" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Vehículo*</label>
                <select class="form-select" name="id_vehiculo" required>
                    <?php foreach ($vehiculos as $v): ?>
                        <option value="<?= $v['id_vehiculo'] ?>"><?= $v['Descripción'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hotel*</label>
                <select class="form-select" name="id_hotel" required>
                    <?php foreach ($hoteles as $h): ?>
                        <option value="<?= $h['id_hotel'] ?>"><?= $h['usuario'] ?? 'Hotel '.$h['id_hotel'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Confirmar Reserva</button>
        </form>
    </div>

    <script>
        // Generación dinámica de campos
        document.getElementById('tipoTrayecto').addEventListener('change', function() {
            const tipo = this.value;
            let html = '';

            // Campos para Aeropuerto→Hotel (Tipo 1 y 3)
            if (tipo == 1 || tipo == 3) {
                html += `
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Fecha Llegada*</label>
                    <input type="date" class="form-control" name="fecha_entrada" min="${new Date().toISOString().split('T')[0]}" required>
                </div>
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Hora Llegada*</label>
                    <input type="time" class="form-control" name="hora_entrada" required>
                </div>
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Número Vuelo (Llegada)</label>
                    <input type="text" class="form-control" name="numero_vuelo_entrada">
                </div>
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Origen del Vuelo</label>
                    <input type="text" class="form-control" name="origen_vuelo_entrada" placeholder="Ciudad de origen">
                </div>`;
            }

            // Campos para Hotel→Aeropuerto (Tipo 2 y 3)
            if (tipo == 2 || tipo == 3) {
                html += `
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Fecha Salida*</label>
                    <input type="date" class="form-control" name="fecha_vuelo_salida" min="${new Date().toISOString().split('T')[0]}" required>
                </div>
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Hora Salida*</label>
                    <input type="time" class="form-control" name="hora_vuelo_salida" required>
                </div>`;
            }

            // Campos para Hotel→Aeropuerto (Tipo 2 y 3)
            if (tipo == 2 || tipo == 3) {
                html += `
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Hora Recogida*</label>
                    <input type="time" class="form-control" name="hora_recogida" ${tipo == 2 ? 'required' : ''}>
                </div>
                <div class="campo-dinamico">
                    <label class="form-label fw-bold">Número Vuelo (Salida)</label>
                    <input type="text" class="form-control" name="numero_vuelo_salida">
                </div>`;
            }

            document.getElementById('camposDinamicos').innerHTML = html;
        });

        // Validación antes de enviar
        document.getElementById('formReserva').addEventListener('submit', function(e) {
            const tipo = document.getElementById('tipoTrayecto').value;
            if (!tipo) {
                e.preventDefault();
                alert('Selecciona un tipo de trayecto');
                return false;
            }
            return true;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>