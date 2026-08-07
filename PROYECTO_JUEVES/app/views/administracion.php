<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../helpers/report_format.php";

requerirAdmin();

$reportes = Incidente::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container wide">
    <h2>Gestión administrativa</h2>
    <a href="dashboard.php">Volver al panel</a>
    <?php include "partials/admin_nav.php"; ?>

    <?php while ($r = $reportes->fetch_assoc()): ?>
        <div class="card">
            <strong><?= $r['titulo'] ?></strong><br>
            Ciudadano: <?= $r['nombre'] ?> | Categoría: <?= $r['nombre_categoria'] ?><br>
            Descripción: <?= $r['descripcion'] ?><br>
            <span class="status-badge <?= estadoReporteClass($r['estado']) ?>"><?= estadoReporteLabel($r['estado']) ?></span>
            <span class="priority-badge <?= prioridadReporteClass($r['prioridad']) ?>"><?= prioridadReporteLabel($r['prioridad']) ?></span><br>
            <form method="POST" action="../controllers/IncidenteController.php">
                <input type="hidden" name="id_reporte" value="<?= $r['id_reporte'] ?>">
                <select name="estado">
                    <?php foreach (estadosReporte() as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $r['estado'] == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="prioridad">
                    <?php foreach (prioridadesReporte() as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $r['prioridad'] == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="comentario" placeholder="Comentario de seguimiento"></textarea>
                <button type="submit" name="actualizar_estado">Actualizar</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
