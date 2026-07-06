<?php
session_start();
require_once "../models/Usuario.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'admin';
$usuarios = Usuario::obtenerTodos();
$editar = isset($_GET['editar']) ? Usuario::obtenerPorId($_GET['editar']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Gestión de usuarios</h2>
                <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">✅ Usuario agregado correctamente.</div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger">⚠️ El correo ya está registrado. Intenta con otro.</div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AdminCrudController.php" class="row g-3 mb-3">
                <input type="hidden" name="id_usuario" value="<?= isset($editar['id_usuario']) ? $editar['id_usuario'] : '' ?>">
                <div class="col-md-3"><input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?= $editar['nombre'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="email" class="form-control" name="correo" placeholder="Correo" value="<?= $editar['correo'] ?? '' ?>" required></div>
                <div class="col-md-2"><input type="password" class="form-control" name="password" placeholder="Contraseña" <?= isset($editar) && $editar !== null ? '' : 'required' ?>></div>
                <div class="col-md-2"><input type="text" class="form-control" name="telefono" placeholder="Teléfono" value="<?= $editar['telefono'] ?? '' ?>"></div>
                <div class="col-md-2"><select class="form-select" name="rol"><option value="ciudadano" <?= ($editar['rol'] ?? 'ciudadano') == 'ciudadano' ? 'selected' : '' ?>>Ciudadano</option><option value="admin" <?= ($editar['rol'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrador</option></select></div>
                <div class="col-md-2"><select class="form-select" name="estado"><option value="activo" <?= ($editar['estado'] ?? 'activo') == 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($editar['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" name="<?= isset($editar) && $editar !== null ? 'editar_usuario' : 'guardar_usuario' ?>">
                        <?= isset($editar) && $editar !== null ? 'Actualizar' : 'Agregar usuario' ?>
                    </button>
                </div>
            </form>
            <?php if (!$esAdmin): ?>
                <div class="alert alert-secondary py-2">Solo los administradores pueden gestionar usuarios.</div>
            <?php endif; ?>

            <table id="tablaUsuarios" class="table table-striped table-hover">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php while ($u = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['id_usuario'] ?></td>
                            <td><?= $u['nombre'] ?></td>
                            <td><?= $u['correo'] ?></td>
                            <td><?= $u['rol'] ?></td>
                            <td><span class="badge bg-<?= $u['estado'] == 'activo' ? 'success' : 'secondary' ?>"><?= $u['estado'] ?></span></td>
                            <td>
                                <a href="crud_usuarios.php?editar=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../controllers/AdminCrudController.php?eliminar_usuario=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#tablaUsuarios').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'}});
});
</script>
</body>
</html>
