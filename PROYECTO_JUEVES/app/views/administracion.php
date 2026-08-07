<?php
require_once "../helpers/auth.php";

requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administracion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2>Centro administrativo</h2>
            <p class="text-muted">Use estas secciones para revisar reportes, actualizar estados, consultar estadisticas y mantener los catalogos del sistema.</p>
            <div class="admin-actions">
                <a href="crud_reportes.php" class="btn btn-primary">Gestionar reportes</a>
                <a href="estadisticas.php" class="btn btn-outline-primary">Ver estadisticas</a>
                <a href="crud_usuarios.php" class="btn btn-outline-secondary">Usuarios</a>
                <a href="crud_categorias.php" class="btn btn-outline-secondary">Categorias</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
