<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../models/Categoria.php";

requerirAdmin();

$reportes = Incidente::obtenerTodos();
$categorias = Categoria::obtenerTodas();
$editar = isset($_GET['editar']) ? Incidente::obtenerPorId($_GET['editar']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Reportes</title>
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
                <h2 class="mb-0">Gestión de reportes</h2>
                <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <form method="POST" action="../controllers/AdminCrudController.php" class="row g-3 mb-3">
                <input type="hidden" name="id_reporte" value="<?= isset($editar['id_reporte']) ? $editar['id_reporte'] : '' ?>">
                <div class="col-md-3"><select class="form-select" name="id_categoria" required><option value="">Seleccione categoría</option><?php while ($c = $categorias->fetch_assoc()): ?><option value="<?= $c['id_categoria'] ?>" <?= ($editar['id_categoria'] ?? '') == $c['id_categoria'] ? 'selected' : '' ?>><?= $c['nombre_categoria'] ?></option><?php endwhile; ?></select></div>
                <div class="col-md-3"><input type="text" class="form-control" name="titulo" placeholder="Título" value="<?= $editar['titulo'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="text" class="form-control" name="ubicacion" placeholder="Ubicación" value="<?= $editar['ubicacion'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="text" class="form-control" name="distrito" placeholder="Distrito" value="<?= $editar['distrito'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="text" class="form-control" name="canton" placeholder="Cantón" value="<?= $editar['canton'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="text" class="form-control" name="provincia" placeholder="Provincia" value="<?= $editar['provincia'] ?? '' ?>" required></div>
                <div class="col-md-3"><input type="text" class="form-control" name="imagen" placeholder="URL imagen" value="<?= $editar['imagen'] ?? '' ?>"></div>
                <div class="col-md-3"><select class="form-select" name="estado"><option value="pendiente" <?= ($editar['estado'] ?? 'pendiente') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option><option value="en_proceso" <?= ($editar['estado'] ?? '') == 'en_proceso' ? 'selected' : '' ?>>En proceso</option><option value="resuelto" <?= ($editar['estado'] ?? '') == 'resuelto' ? 'selected' : '' ?>>Resuelto</option></select></div>
                <div class="col-md-3"><select class="form-select" name="prioridad"><option value="baja" <?= ($editar['prioridad'] ?? 'media') == 'baja' ? 'selected' : '' ?>>Baja</option><option value="media" <?= ($editar['prioridad'] ?? 'media') == 'media' ? 'selected' : '' ?>>Media</option><option value="alta" <?= ($editar['prioridad'] ?? 'media') == 'alta' ? 'selected' : '' ?>>Alta</option></select></div>
                <div class="col-md-6"><textarea class="form-control" name="descripcion" placeholder="Descripción" required><?= $editar['descripcion'] ?? '' ?></textarea></div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100" name="<?= isset($editar) && $editar !== null ? 'editar_reporte' : 'guardar_reporte' ?>">
                        <?= isset($editar) && $editar !== null ? 'Actualizar' : 'Agregar reporte' ?>
                    </button>
                </div>
            </form>
            <table id="tablaReportes" class="table table-striped table-hover">
                <thead><tr><th>ID</th><th>Título</th><th>Usuario</th><th>Categoría</th><th>Estado</th><th>Prioridad</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php while ($r = $reportes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id_reporte'] ?></td>
                            <td><?= $r['titulo'] ?></td>
                            <td><?= $r['nombre'] ?></td>
                            <td><?= $r['nombre_categoria'] ?></td>
                            <td><span class="badge bg-<?= $r['estado'] == 'resuelto' ? 'success' : ($r['estado'] == 'en_proceso' ? 'warning' : 'secondary') ?>"><?= $r['estado'] ?></span></td>
                            <td><span class="badge bg-<?= $r['prioridad'] == 'alta' ? 'danger' : ($r['prioridad'] == 'media' ? 'warning' : 'secondary') ?>"><?= $r['prioridad'] ?></span></td>
                            <td>
                                <a href="crud_reportes.php?editar=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../controllers/AdminCrudController.php?eliminar_reporte=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar reporte?')">Eliminar</a>
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
    $('#tablaReportes').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'}});
});
</script>
</body>
</html>
