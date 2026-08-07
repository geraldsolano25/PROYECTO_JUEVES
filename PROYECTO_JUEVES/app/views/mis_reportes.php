<?php
session_start();
require_once "../models/Incidente.php";
require_once "../helpers/report_format.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$misReportes = Incidente::obtenerPorUsuario($_SESSION['usuario']['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<?php include "partials/main_nav.php"; ?>
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Mis reportes</h2>
                <div class="d-flex gap-2">
                    <a href="perfil.php" class="btn btn-outline-primary">Mi perfil</a>
                    <a href="dashboard.php" class="btn btn-outline-secondary">Volver</a>
                </div>
            </div>

            <?php if (isset($_GET['reporte_actualizado'])): ?>
                <div class="alert alert-success">Reporte actualizado correctamente.</div>
            <?php endif; ?>

            <?php if (isset($_GET['reporte_cancelado'])): ?>
                <div class="alert alert-success">Reporte cancelado correctamente.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'accion_no_permitida'): ?>
                <div class="alert alert-warning">Solo puede editar o cancelar reportes que sigan pendientes.</div>
            <?php endif; ?>

            <?php if ($misReportes->num_rows > 0): ?>
                <?php while ($r = $misReportes->fetch_assoc()): ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <h5 class="mb-1"><?= $r['titulo'] ?></h5>
                                <span class="text-muted"><?= $r['nombre_categoria'] ?></span>
                            </div>
                            <div>
                                <span class="status-badge <?= estadoReporteClass($r['estado']) ?>"><?= estadoReporteLabel($r['estado']) ?></span>
                                <span class="priority-badge <?= prioridadReporteClass($r['prioridad']) ?>"><?= prioridadReporteLabel($r['prioridad']) ?></span>
                            </div>
                        </div>
                        <p class="mt-2 mb-2"><?= $r['descripcion'] ?></p>
                        <?= imagenReporteHtml($r['imagen'] ?? '', $r['titulo']) ?>
                        <div class="text-muted small">
                            Zona: <?= $r['distrito'] ?>, <?= $r['canton'] ?>, <?= $r['provincia'] ?><br>
                            Creado: <?= $r['fecha_creacion'] ?>
                        </div>
                        <?php if (!empty($r['imagen'])): ?>
                            <a href="<?= $r['imagen'] ?>" target="_blank" rel="noopener">Ver evidencia</a>
                        <?php endif; ?>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <a href="detalle_reporte.php?id=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-primary">Ver seguimiento</a>
                            <?php if ($r['estado'] === 'pendiente'): ?>
                                <a href="editar_mi_reporte.php?id=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form method="POST" action="../controllers/IncidenteController.php" class="d-inline">
                                    <input type="hidden" name="id_reporte" value="<?= $r['id_reporte'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" name="cancelar_reporte_pendiente" onclick="return confirm('¿Cancelar este reporte pendiente?')">Cancelar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0">Todavia no ha registrado reportes.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
