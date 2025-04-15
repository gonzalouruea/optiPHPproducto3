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
        $stmt = $db->prepare("DELETE FROM transfer_tipo_reserva WHERE id_tipo_reserva = ?");
        $stmt->execute([$_POST['id_tipo_reserva']]);
        header("Location: gestionar_tipos_reserva.php?success=Tipo de reserva eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "No se puede eliminar el tipo de reserva porque tiene reservas asociadas";
    }
}

// Procesar creación/actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['create', 'update'])) {
    try {
        if ($_POST['action'] === 'create') {
            $stmt = $db->prepare("INSERT INTO transfer_tipo_reserva (Descripción) VALUES (?)");
            $stmt->execute([$_POST['descripcion']]);
        } else {
            $stmt = $db->prepare("UPDATE transfer_tipo_reserva SET Descripción = ? WHERE id_tipo_reserva = ?");
            $stmt->execute([$_POST['descripcion'], $_POST['id_tipo_reserva']]);
        }
        
        header("Location: gestionar_tipos_reserva.php?success=Operación realizada con éxito");
        exit;
    } catch (PDOException $e) {
        $error = "Error en la operación: " . $e->getMessage();
    }
}

// Obtener lista de tipos de reserva con contador de uso
$tipos = $db->query("SELECT tr.*, 
    (SELECT COUNT(*) FROM transfer_reservas r WHERE r.id_tipo_reserva = tr.id_tipo_reserva) as num_reservas
    FROM transfer_tipo_reserva tr
    ORDER BY tr.id_tipo_reserva")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tipos de Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav_admin.php'; ?>
    
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-bookmark"></i> Gestionar Tipos de Reserva</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoTipoModal">
                <i class="bi bi-plus-circle"></i> Nuevo Tipo
            </button>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Lista de tipos de reserva -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Reservas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipos as $tipo): ?>
                            <tr>
                                <td><?= htmlspecialchars($tipo['id_tipo_reserva']) ?></td>
                                <td><?= htmlspecialchars($tipo['Descripción']) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($tipo['num_reservas']) ?> reservas
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="editarTipo(<?= htmlspecialchars(json_encode($tipo)) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($tipo['num_reservas'] == 0): ?>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este tipo de reserva?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_tipo_reserva" value="<?= $tipo['id_tipo_reserva'] ?>">
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

    <!-- Modal Nuevo Tipo -->
    <div class="modal fade" id="nuevoTipoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Tipo de Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Crear Tipo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Tipo -->
    <div class="modal fade" id="editarTipoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tipo de Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditar">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_tipo_reserva" id="edit_id_tipo_reserva">
                        
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
        function editarTipo(tipo) {
            document.getElementById('edit_id_tipo_reserva').value = tipo.id_tipo_reserva;
            document.getElementById('edit_descripcion').value = tipo.Descripción;
            
            new bootstrap.Modal(document.getElementById('editarTipoModal')).show();
        }
    </script>
</body>
</html>
