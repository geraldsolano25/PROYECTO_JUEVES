<?php
require_once "../helpers/auth.php";
require_once "../helpers/report_format.php";
require_once "../models/Incidente.php";

requerirAdmin();

$resumen = Incidente::resumenEstadistico();
$porEstado = Incidente::reportesPorEstado();
$porCategoria = Incidente::reportesPorCategoria();
$zonas = Incidente::zonasConMasReportes();
$masVotados = Incidente::reportesMasVotados();

function porcentajeEstadistica($valor, $total) {
    if ($total <= 0) {
        return 0;
    }

    return round(($valor / $total) * 100);
}

function barraEstadistica($valor, $maximo) {
    if ($maximo <= 0) {
        return 0;
    }

    return max(6, round(($valor / $maximo) * 100));
}

$totalReportes = (int) ($resumen['total'] ?? 0);
$tarjetas = [
    ['label' => 'Reportes totales', 'value' => $totalReportes],
    ['label' => 'Resueltos', 'value' => (int) ($resumen['resueltos'] ?? 0)],
    ['label' => 'En proceso', 'value' => (int) ($resumen['en_proceso'] ?? 0)],
    ['label' => 'Pendientes', 'value' => (int) ($resumen['pendientes'] ?? 0)],
];

$categorias = [];
$maxCategoria = 0;
while ($fila = $porCategoria->fetch_assoc()) {
    $fila['total'] = (int) $fila['total'];
    $maxCategoria = max($maxCategoria, $fila['total']);
    $categorias[] = $fila;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadisticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Estadisticas</h2>
            <p class="text-muted mb-0">Resumen general de reportes comunitarios.</p>
        </div>
        <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="stats-grid mb-4">
        <?php foreach ($tarjetas as $tarjeta): ?>
            <div class="stat-card">
                <span><?= $tarjeta['label'] ?></span>
                <strong><?= $tarjeta['value'] ?></strong>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h3>Reportes por estado</h3>
                    <?php if ($totalReportes > 0): ?>
                        <?php while ($fila = $porEstado->fetch_assoc()): ?>
                            <?php $totalEstado = (int) $fila['total']; ?>
                            <div class="stat-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="status-badge <?= estadoReporteClass($fila['estado']) ?>"><?= estadoReporteLabel($fila['estado']) ?></span>
                                    <strong><?= $totalEstado ?> (<?= porcentajeEstadistica($totalEstado, $totalReportes) ?>%)</strong>
                                </div>
                                <div class="stat-bar"><span style="width: <?= porcentajeEstadistica($totalEstado, $totalReportes) ?>%"></span></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">Todavia no hay reportes registrados.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h3>Problemas por categoria</h3>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $fila): ?>
                            <div class="stat-row">
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <span><?= $fila['nombre_categoria'] ?></span>
                                    <strong><?= $fila['total'] ?></strong>
                                </div>
                                <div class="stat-bar"><span style="width: <?= barraEstadistica($fila['total'], $maxCategoria) ?>%"></span></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No hay categorias registradas.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h3>Zonas con mas reportes</h3>
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Zona</th><th class="text-end">Reportes</th></tr></thead>
                        <tbody>
                            <?php if ($zonas->num_rows > 0): ?>
                                <?php while ($zona = $zonas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $zona['provincia'] ?>, <?= $zona['canton'] ?>, <?= $zona['distrito'] ?></td>
                                        <td class="text-end"><strong><?= $zona['total'] ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-muted">No hay zonas registradas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h3>Reportes con mas votos</h3>
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Reporte</th><th>Estado</th><th class="text-end">Votos</th></tr></thead>
                        <tbody>
                            <?php if ($masVotados->num_rows > 0): ?>
                                <?php while ($reporte = $masVotados->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $reporte['titulo'] ?></td>
                                        <td><span class="status-badge <?= estadoReporteClass($reporte['estado']) ?>"><?= estadoReporteLabel($reporte['estado']) ?></span></td>
                                        <td class="text-end"><strong><?= $reporte['votos'] ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-muted">No hay reportes registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
