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
        // Verificar si hay reservas asociadas
        $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_reservas WHERE id_vehiculo = ?");
        $stmt->execute([$_POST['id_vehiculo']]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: gestionar_vehiculos.php?error=No se puede eliminar el vehículo porque tiene reservas asociadas");
            exit;
        }

        $stmt = $db->prepare("DELETE FROM transfer_vehiculo WHERE id_vehiculo = ?");
        $stmt->execute([$_POST['id_vehiculo']]);
        header("Location: gestionar_vehiculos.php?success=Vehículo eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        header("Location: gestionar_vehiculos.php?error=Error al eliminar el vehículo: " . urlencode($e->getMessage()));
        exit;
    }
}

// Procesar creación/actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['create', 'update'])) {
    try {
        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            header("Location: gestionar_vehiculos.php?error=El email del conductor no es válido");
            exit;
        }

        // Validar descripción
        if (empty($_POST['descripcion']) || strlen($_POST['descripcion']) > 100) {
            header("Location: gestionar_vehiculos.php?error=La descripción es requerida y no debe exceder 100 caracteres");
            exit;
        }

        if ($_POST['action'] === 'create') {
            // Validar que el email no esté en uso
            $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_vehiculo WHERE email_conductor = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetchColumn() > 0) {
                header("Location: gestionar_vehiculos.php?error=El email del conductor ya está en uso");
                exit;
            }

            // Validar contraseña
            if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
                header("Location: gestionar_vehiculos.php?error=La contraseña debe tener al menos 6 caracteres");
                exit;
            }

            $stmt = $db->prepare("INSERT INTO transfer_vehiculo (Descripción, email_conductor, password) VALUES (?, ?, ?)");
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([$_POST['descripcion'], $_POST['email'], $password]);
        } else {
            // Validar que el email no esté en uso por otro vehículo
            $stmt = $db->prepare("SELECT COUNT(*) FROM transfer_vehiculo WHERE email_conductor = ? AND id_vehiculo != ?");
            $stmt->execute([$_POST['email'], $_POST['id_vehiculo']]);
            if ($stmt->fetchColumn() > 0) {
                header("Location: gestionar_vehiculos.php?error=El email del conductor ya está en uso por otro vehículo");
                exit;
            }

            $sql = "UPDATE transfer_vehiculo SET Descripción = ?, email_conductor = ?";
            $params = [$_POST['descripcion'], $_POST['email']];
            
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    header("Location: gestionar_vehiculos.php?error=La contraseña debe tener al menos 6 caracteres");
                    exit;
                }
                $sql .= ", password = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id_vehiculo = ?";
            $params[] = $_POST['id_vehiculo'];
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }
        
        header("Location: gestionar_vehiculos.php?success=Operación realizada con éxito");
        exit;
    } catch (PDOException $e) {
        header("Location: gestionar_vehiculos.php?error=Error en la operación: " . urlencode($e->getMessage()));
        exit;
    }
}

// Obtener lista de vehículos
$vehiculos = $db->query("SELECT * FROM transfer_vehiculo")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Vehículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav_admin.php'; ?>
    
    <div class="container my-4">
        <h2>Gestionar Vehículos</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Formulario para nuevo vehículo -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Nuevo Vehículo</h5>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" class="form-control" name="descripcion" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email del Conductor</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Crear Vehículo</button>
                </form>
            </div>
        </div>

        <!-- Lista de vehículos -->
        <h3>Vehículos Existentes</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Email del Conductor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr>
                        <td><?= htmlspecialchars($vehiculo['id_vehiculo']) ?></td>
                        <td><?= htmlspecialchars($vehiculo['Descripción']) ?></td>
                        <td><?= htmlspecialchars($vehiculo['email_conductor']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    onclick="editarVehiculo(<?= htmlspecialchars(json_encode($vehiculo)) ?>)">
                                Editar
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este vehículo?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditar">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_vehiculo" id="edit_id_vehiculo">
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="edit_descripcion" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email del Conductor</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarVehiculo(vehiculo) {
            document.getElementById('edit_id_vehiculo').value = vehiculo.id_vehiculo;
            document.getElementById('edit_descripcion').value = vehiculo.Descripción;
            document.getElementById('edit_email').value = vehiculo.email_conductor;
            document.getElementById('edit_password').value = '';
            
            new bootstrap.Modal(document.getElementById('editarModal')).show();
        }
    </script>
</body>
</html>
