<?php
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../models/Categoria.php";

requerirLogin();

$reporte = Incidente::obtenerPorIdYUsuario($_GET['id'] ?? 0, usuarioActual()['id_usuario']);
if (!$reporte || $reporte['estado'] !== 'pendiente') {
    header("Location: mis_reportes.php?error=accion_no_permitida");
    exit();
}

$categorias = Categoria::obtenerActivas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar reporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<?php include "partials/main_nav.php"; ?>
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Editar reporte pendiente</h2>
                <a href="mis_reportes.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <form method="POST" action="../controllers/IncidenteController.php" class="row g-3" data-location-form>
                <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                <div class="col-md-4">
                    <select class="form-select" name="id_categoria" required>
                        <option value="">Seleccione una categoria</option>
                        <?php while ($c = $categorias->fetch_assoc()): ?>
                            <option value="<?= $c['id_categoria'] ?>" <?= $reporte['id_categoria'] == $c['id_categoria'] ? 'selected' : '' ?>><?= $c['nombre_categoria'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="titulo" placeholder="Titulo del incidente" value="<?= $reporte['titulo'] ?>" required>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="provincia" data-location-field="provincia" data-selected="<?= $reporte['provincia'] ?>" required><option value="">Cargando provincias...</option></select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="canton" data-location-field="canton" data-selected="<?= $reporte['canton'] ?>" required><option value="">Seleccione canton</option></select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="distrito" data-location-field="distrito" data-selected="<?= $reporte['distrito'] ?>" required><option value="">Seleccione distrito</option></select>
                </div>
                <div class="col-12"><small class="text-muted" data-location-status></small></div>
                <div class="col-12">
                    <textarea class="form-control" name="descripcion" placeholder="Descripcion detallada" required><?= $reporte['descripcion'] ?></textarea>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" name="imagen" placeholder="URL de evidencia (opcional)" value="<?= $reporte['imagen'] ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" name="actualizar_reporte_pendiente">Guardar reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../public/js/costa-rica-location.js"></script>
</body>
</html>
