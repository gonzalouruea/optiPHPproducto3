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

// Obtener zonas para el selector
$zonas = $db->query("SELECT * FROM transfer_zona")->fetchAll();

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $stmt = $db->prepare("DELETE FROM transfer_hotel WHERE id_hotel = ?");
        $stmt->execute([$_POST['id_hotel']]);
        header("Location: gestionar_hoteles.php?success=Hotel eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "No se puede eliminar el hotel porque tiene reservas asociadas";
    }
}

// Procesar creación/actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['create', 'update'])) {
    try {
        if ($_POST['action'] === 'create') {
            $stmt = $db->prepare("INSERT INTO transfer_hotel (id_zona, Comision, usuario, password) VALUES (?, ?, ?, ?)");
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $_POST['id_zona'],
                $_POST['comision'],
                $_POST['usuario'],
                $password
            ]);
        } else {
            $sql = "UPDATE transfer_hotel SET id_zona = ?, Comision = ?, usuario = ?";
            $params = [
                $_POST['id_zona'],
                $_POST['comision'],
                $_POST['usuario']
            ];
            
            if (!empty($_POST['password'])) {
                $sql .= ", password = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id_hotel = ?";
            $params[] = $_POST['id_hotel'];
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }
        
        header("Location: gestionar_hoteles.php?success=Operación realizada con éxito");
        exit;
    } catch (PDOException $e) {
        $error = "Error en la operación: " . $e->getMessage();
    }
}

// Obtener lista de hoteles con información de zona
$hoteles = $db->query("
    SELECT h.*, z.descripcion as zona_nombre 
    FROM transfer_hotel h 
    LEFT JOIN transfer_zona z ON h.id_zona = z.id_zona
    ORDER BY h.id_hotel")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Hoteles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav_admin.php'; ?>
    
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-building"></i> Gestionar Hoteles</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoHotelModal">
                <i class="bi bi-plus-circle"></i> Nuevo Hotel
            </button>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Lista de hoteles -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Zona</th>
                                <th>Usuario</th>
                                <th>Comisión</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hoteles as $hotel): ?>
                            <tr>
                                <td><?= htmlspecialchars($hotel['id_hotel']) ?></td>
                                <td><?= htmlspecialchars($hotel['zona_nombre'] ?? 'Sin zona') ?></td>
                                <td><?= htmlspecialchars($hotel['usuario']) ?></td>
                                <td><?= htmlspecialchars($hotel['Comision']) ?>%</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="editarHotel(<?= htmlspecialchars(json_encode($hotel)) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este hotel?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_hotel" value="<?= $hotel['id_hotel'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Hotel -->
    <div class="modal fade" id="nuevoHotelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Zona</label>
                            <select class="form-select" name="id_zona" required>
                                <option value="">Seleccione una zona</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id_zona'] ?>">
                                        <?= htmlspecialchars($zona['descripcion']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Comisión (%)</label>
                            <input type="number" class="form-control" name="comision" step="0.01" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Crear Hotel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Hotel -->
    <div class="modal fade" id="editarHotelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Hotel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditar">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_hotel" id="edit_id_hotel">
                        
                        <div class="mb-3">
                            <label class="form-label">Zona</label>
                            <select class="form-select" name="id_zona" id="edit_id_zona" required>
                                <option value="">Seleccione una zona</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id_zona'] ?>">
                                        <?= htmlspecialchars($zona['descripcion']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" id="edit_usuario" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Comisión (%)</label>
                            <input type="number" class="form-control" name="comision" id="edit_comision" step="0.01" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarHotel(hotel) {
            document.getElementById('edit_id_hotel').value = hotel.id_hotel;
            document.getElementById('edit_id_zona').value = hotel.id_zona || '';
            document.getElementById('edit_usuario').value = hotel.usuario || '';
            document.getElementById('edit_comision').value = hotel.Comision || '';
            document.getElementById('edit_password').value = '';
            
            new bootstrap.Modal(document.getElementById('editarHotelModal')).show();
        }
    </script>
</body>
</html>
