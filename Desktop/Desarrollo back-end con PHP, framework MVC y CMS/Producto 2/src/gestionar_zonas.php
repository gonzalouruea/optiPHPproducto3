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

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        // Verificar si hay hoteles asociados
        $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_hotel WHERE id_zona = ?");
        $stmt->execute([$_POST['id_zona']]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: gestionar_zonas.php?error=No se puede eliminar la zona porque tiene hoteles asociados");
            exit;
        }

        // Verificar si hay reservas asociadas a hoteles en esta zona
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM transfer_reservas r
            INNER JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
            WHERE h.id_zona = ?
        ");
        $stmt->execute([$_POST['id_zona']]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: gestionar_zonas.php?error=No se puede eliminar la zona porque hay reservas asociadas a hoteles en esta zona");
            exit;
        }

        $stmt = $db->prepare("DELETE FROM transfer_zona WHERE id_zona = ?");
        $stmt->execute([$_POST['id_zona']]);
        header("Location: gestionar_zonas.php?success=Zona eliminada correctamente");
        exit;
    } catch (PDOException $e) {
        header("Location: gestionar_zonas.php?error=Error al eliminar la zona: " . urlencode($e->getMessage()));
        exit;
    }
}

// Procesar creación/actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['create', 'update'])) {
    try {
        // Validar descripción
        if (empty($_POST['descripcion'])) {
            header("Location: gestionar_zonas.php?error=La descripción es requerida");
            exit;
        }

        if (strlen($_POST['descripcion']) > 100) {
            header("Location: gestionar_zonas.php?error=La descripción no debe exceder 100 caracteres");
            exit;
        }

        // Verificar si ya existe una zona con la misma descripción
        $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_zona WHERE LOWER(descripcion) = LOWER(?)");
        $stmt->execute([trim($_POST['descripcion'])]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: gestionar_zonas.php?error=Ya existe una zona con esta descripción");
            exit;
        }

        if ($_POST['action'] === 'create') {
            $stmt = $db->prepare("INSERT INTO transfer_zona (descripcion) VALUES (?)");
            $stmt->execute([trim($_POST['descripcion'])]);
        } else {
            // Verificar que la zona existe
            $stmt = $db->prepare("SELECT id_zona FROM transfer_zona WHERE id_zona = ?");
            $stmt->execute([$_POST['id_zona']]);
            if (!$stmt->fetch()) {
                header("Location: gestionar_zonas.php?error=La zona que intenta editar no existe");
                exit;
            }

            $stmt = $db->prepare("UPDATE transfer_zona SET descripcion = ? WHERE id_zona = ?");
            $stmt->execute([trim($_POST['descripcion']), $_POST['id_zona']]);
        }
        
        header("Location: gestionar_zonas.php?success=Operación realizada con éxito");
        exit;
    } catch (PDOException $e) {
        header("Location: gestionar_zonas.php?error=Error en la operación: " . urlencode($e->getMessage()));
        exit;
    }
}

// Obtener lista de zonas
$zonas = $db->query("SELECT z.*, 
    (SELECT COUNT(*) FROM transfer_hotel h WHERE h.id_zona = z.id_zona) as num_hoteles
    FROM transfer_zona z
    ORDER BY z.id_zona")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Zonas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav_admin.php'; ?>
    
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-geo-alt"></i> Gestionar Zonas</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaZonaModal">
                <i class="bi bi-plus-circle"></i> Nueva Zona
            </button>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Lista de zonas -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Hoteles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($zonas as $zona): ?>
                            <tr>
                                <td><?= htmlspecialchars($zona['id_zona']) ?></td>
                                <td><?= htmlspecialchars($zona['descripcion']) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($zona['num_hoteles']) ?> hoteles
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="editarZona(<?= htmlspecialchars(json_encode($zona)) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($zona['num_hoteles'] == 0): ?>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('¿Está seguro de eliminar esta zona?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_zona" value="<?= $zona['id_zona'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Zona -->
    <div class="modal fade" id="nuevaZonaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Crear Zona</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Zona -->
    <div class="modal fade" id="editarZonaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditar">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_zona" id="edit_id_zona">
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit_descripcion" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarZona(zona) {
            document.getElementById('edit_id_zona').value = zona.id_zona;
            document.getElementById('edit_descripcion').value = zona.descripcion;
            
            new bootstrap.Modal(document.getElementById('editarZonaModal')).show();
        }
    </script>
</body>
</html>
