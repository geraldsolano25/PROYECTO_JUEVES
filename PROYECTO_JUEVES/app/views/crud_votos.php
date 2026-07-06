<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";

requerirAdmin();

$reportes = Incidente::obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Votos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Gestión de votos</h2>
                <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <table id="tablaVotos" class="table table-striped table-hover">
                <thead>
                    <tr><th>ID reporte</th><th>Título</th><th>Votos</th></tr>
                </thead>
                <tbody>
                    <?php while ($r = $reportes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id_reporte'] ?></td>
                            <td><?= $r['titulo'] ?></td>
                            <td><?= Incidente::contarVotos($r['id_reporte']) ?></td>
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
    $('#tablaVotos').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'}});
});
</script>
</body>
</html>
