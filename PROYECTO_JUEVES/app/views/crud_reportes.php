<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../models/Categoria.php";
require_once "../helpers/report_format.php";

requerirAdmin();

$reportes = Incidente::obtenerTodos();
$categorias = Categoria::obtenerTodas();
$editar = isset($_GET['editar']) ? Incidente::obtenerPorId($_GET['editar']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestion de reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h2 class="mb-0">Gestion de reportes</h2>
                    <p class="text-muted mb-0">Edite reportes y notifique cambios de estado desde un solo lugar.</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <?php if (isset($_GET['correo']) && $_GET['correo'] === 'procesado'): ?>
                <div class="alert alert-info">Cambio guardado. El aviso por correo fue procesado.</div>
            <?php endif; ?>

            <form id="form-reportes" method="POST" action="../controllers/AdminCrudController.php" class="row g-3 mb-4" data-location-form>
                <input type="hidden" name="id_reporte" value="<?= isset($editar['id_reporte']) ? $editar['id_reporte'] : '' ?>">

                <div class="col-md-3">
                    <select class="form-select" name="id_categoria" required>
                        <option value="">Seleccione categoria</option>
                        <?php while ($c = $categorias->fetch_assoc()): ?>
                            <option value="<?= $c['id_categoria'] ?>" <?= ($editar['id_categoria'] ?? '') == $c['id_categoria'] ? 'selected' : '' ?>><?= $c['nombre_categoria'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="titulo" placeholder="Titulo" value="<?= $editar['titulo'] ?? '' ?>" required>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="provincia" data-location-field="provincia" data-selected="<?= $editar['provincia'] ?? '' ?>" required><option value="">Cargando provincias...</option></select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="canton" data-location-field="canton" data-selected="<?= $editar['canton'] ?? '' ?>" required><option value="">Seleccione canton</option></select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="distrito" data-location-field="distrito" data-selected="<?= $editar['distrito'] ?? '' ?>" required><option value="">Seleccione distrito</option></select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="imagen" placeholder="URL imagen" value="<?= $editar['imagen'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <?php foreach (estadosReporte() as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= ($editar['estado'] ?? 'pendiente') == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="prioridad">
                        <?php foreach (prioridadesReporte() as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= ($editar['prioridad'] ?? 'media') == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12"><small class="text-muted" data-location-status></small></div>
                <div class="col-md-6">
                    <textarea class="form-control" name="descripcion" placeholder="Descripcion" required><?= $editar['descripcion'] ?? '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <textarea class="form-control" name="comentario_seguimiento" placeholder="Comentario para el ciudadano si cambia estado o prioridad"></textarea>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100" name="<?= isset($editar) && $editar !== null ? 'editar_reporte' : 'guardar_reporte' ?>">
                        <?= isset($editar) && $editar !== null ? 'Actualizar reporte' : 'Agregar reporte' ?>
                    </button>
                </div>
            </form>

            <div id="tabla-reportes"></div>
            <table id="tablaReportes" class="table table-striped table-hover align-middle">
                <thead><tr><th>ID</th><th>Titulo</th><th>Usuario</th><th>Categoria</th><th>Estado</th><th>Prioridad</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php while ($r = $reportes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id_reporte'] ?></td>
                            <td><?= $r['titulo'] ?></td>
                            <td><?= $r['nombre'] ?></td>
                            <td><?= $r['nombre_categoria'] ?></td>
                            <td><span class="status-badge <?= estadoReporteClass($r['estado']) ?>"><?= estadoReporteLabel($r['estado']) ?></span></td>
                            <td><span class="priority-badge <?= prioridadReporteClass($r['prioridad']) ?>"><?= prioridadReporteLabel($r['prioridad']) ?></span></td>
                            <td>
                                <a href="detalle_reporte.php?id=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-secondary">Detalle</a>
                                <a href="crud_reportes.php?editar=<?= $r['id_reporte'] ?>#form-reportes" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../controllers/AdminCrudController.php?eliminar_reporte=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminar reporte?')">Eliminar</a>
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
<script src="../../public/js/costa-rica-location.js"></script>
<script>
$(document).ready(function () {
    $('#tablaReportes').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'}});
});
</script>
</body>
</html>
