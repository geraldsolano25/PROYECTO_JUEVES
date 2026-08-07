<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../helpers/report_format.php";

requerirAdmin();

$seguimientos = null;
if (isset($_GET['reporte'])) {
    $seguimientos = Incidente::obtenerSeguimiento($_GET['reporte']);
}

$reportes = Incidente::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Seguimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Gestión de seguimientos</h2>
                <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-8">
                    <select class="form-select" name="reporte" onchange="this.form.submit()">
                        <option value="">Seleccione un reporte para ver su historial</option>
                        <?php while ($r = $reportes->fetch_assoc()): ?>
                            <option value="<?= $r['id_reporte'] ?>" <?= (isset($_GET['reporte']) && $_GET['reporte'] == $r['id_reporte']) ? 'selected' : '' ?>><?= $r['titulo'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
            <?php if ($seguimientos !== null): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Reporte</th><th>Administrador</th><th>Estado anterior</th><th>Estado nuevo</th><th>Comentario</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($s = $seguimientos->fetch_assoc()): ?>
                            <tr>
                                <td><?= $s['id_seguimiento'] ?></td>
                                <td><?= $s['id_reporte'] ?></td>
                                <td><?= $s['nombre'] ?></td>
                                <td><span class="status-badge <?= estadoReporteClass($s['estado_anterior']) ?>"><?= estadoReporteLabel($s['estado_anterior']) ?></span></td>
                                <td><span class="status-badge <?= estadoReporteClass($s['estado_nuevo']) ?>"><?= estadoReporteLabel($s['estado_nuevo']) ?></span></td>
                                <td><?= $s['comentario'] ?></td>
                                <td><?= $s['fecha_cambio'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
