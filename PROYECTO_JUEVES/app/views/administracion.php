<?php
session_start();
require_once "../models/Incidente.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$reportes = Incidente::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración</title>
    <link rel="stylesheet" href="/public/css/styles.css">
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
            Estado actual: <?= $r['estado'] ?> | Prioridad: <?= $r['prioridad'] ?><br>
            <form method="POST" action="../controllers/IncidenteController.php">
                <input type="hidden" name="id_reporte" value="<?= $r['id_reporte'] ?>">
                <select name="estado">
                    <option value="pendiente" <?= $r['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_proceso" <?= $r['estado'] == 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                    <option value="resuelto" <?= $r['estado'] == 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                </select>
                <select name="prioridad">
                    <option value="baja" <?= $r['prioridad'] == 'baja' ? 'selected' : '' ?>>Baja</option>
                    <option value="media" <?= $r['prioridad'] == 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="alta" <?= $r['prioridad'] == 'alta' ? 'selected' : '' ?>>Alta</option>
                </select>
                <textarea name="comentario" placeholder="Comentario de seguimiento"></textarea>
                <button type="submit" name="actualizar_estado">Actualizar</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
