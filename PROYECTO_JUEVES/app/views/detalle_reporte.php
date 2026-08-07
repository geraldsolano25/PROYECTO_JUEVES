<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../helpers/report_format.php";

requerirLogin();

$idReporte = $_GET['id'] ?? 0;
$usuario = usuarioActual();
$esAdmin = usuarioEsAdmin();
$reporte = $esAdmin
    ? Incidente::obtenerPorId($idReporte)
    : Incidente::obtenerPorIdYUsuario($idReporte, $usuario['id_usuario']);

if (!$reporte) {
    header("Location: dashboard.php?error=sin_permiso");
    exit();
}

$seguimientos = Incidente::obtenerSeguimiento($reporte['id_reporte']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del reporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php if ($esAdmin): ?>
        <?php include "partials/admin_nav.php"; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Detalle del reporte</h2>
            <p class="text-muted mb-0">Seguimiento completo del incidente registrado.</p>
        </div>
        <a href="<?= $esAdmin ? 'crud_reportes.php' : 'mis_reportes.php' ?>" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between gap-3 flex-wrap">
                <div>
                    <h3 class="mb-1"><?= $reporte['titulo'] ?></h3>
                    <span class="text-muted"><?= $reporte['nombre_categoria'] ?> · <?= $reporte['nombre'] ?></span>
                </div>
                <div>
                    <span class="status-badge <?= estadoReporteClass($reporte['estado']) ?>"><?= estadoReporteLabel($reporte['estado']) ?></span>
                    <span class="priority-badge <?= prioridadReporteClass($reporte['prioridad']) ?>"><?= prioridadReporteLabel($reporte['prioridad']) ?></span>
                </div>
            </div>
            <p class="mt-3 mb-2"><?= $reporte['descripcion'] ?></p>
            <div class="text-muted small">
                Zona: <?= $reporte['distrito'] ?>, <?= $reporte['canton'] ?>, <?= $reporte['provincia'] ?><br>
                Creado: <?= $reporte['fecha_creacion'] ?> · Ultima actualizacion: <?= $reporte['fecha_actualizacion'] ?>
            </div>
            <?php if (!empty($reporte['imagen'])): ?>
                <a href="<?= $reporte['imagen'] ?>" target="_blank" rel="noopener">Ver evidencia</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h3>Historial de seguimiento</h3>
            <?php if ($seguimientos->num_rows > 0): ?>
                <div class="timeline">
                    <?php while ($s = $seguimientos->fetch_assoc()): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <span class="status-badge <?= estadoReporteClass($s['estado_anterior']) ?>"><?= estadoReporteLabel($s['estado_anterior']) ?></span>
                                        <span class="timeline-arrow">a</span>
                                        <span class="status-badge <?= estadoReporteClass($s['estado_nuevo']) ?>"><?= estadoReporteLabel($s['estado_nuevo']) ?></span>
                                    </div>
                                    <small class="text-muted"><?= $s['fecha_cambio'] ?></small>
                                </div>
                                <?php if (!empty($s['comentario'])): ?>
                                    <p class="mb-1 mt-2"><?= $s['comentario'] ?></p>
                                <?php endif; ?>
                                <small class="text-muted">Actualizado por <?= $s['nombre'] ?? 'administrador' ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">Todavia no hay movimientos de seguimiento para este reporte.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
