<?php
require_once "../helpers/auth.php";
require_once "../models/Categoria.php";

requerirAdmin();

$categorias = Categoria::obtenerTodas();
$editar = isset($_GET['editar']) ? Categoria::obtenerPorId($_GET['editar']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-4">
    <?php include "partials/admin_nav.php"; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Gestión de categorías</h2>
                <a href="administracion.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <form id="form-categorias" method="POST" action="../controllers/AdminCrudController.php" class="row g-3 mb-3">
                <input type="hidden" name="id_categoria" value="<?= isset($editar['id_categoria']) ? $editar['id_categoria'] : '' ?>">
                <div class="col-md-4"><input type="text" class="form-control" name="nombre_categoria" placeholder="Nombre categoría" value="<?= $editar['nombre_categoria'] ?? '' ?>" required></div>
                <div class="col-md-4"><textarea class="form-control" name="descripcion" placeholder="Descripción"><?= $editar['descripcion'] ?? '' ?></textarea></div>
                <div class="col-md-2"><select class="form-select" name="estado"><option value="activo" <?= ($editar['estado'] ?? 'activo') == 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($editar['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" name="<?= isset($editar) && $editar !== null ? 'editar_categoria' : 'guardar_categoria' ?>">
                        <?= isset($editar) && $editar !== null ? 'Actualizar' : 'Agregar categoría' ?>
                    </button>
                </div>
            </form>
            <div id="tabla-categorias"></div>
            <table id="tablaCategorias" class="table table-striped table-hover">
                <thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php while ($c = $categorias->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['id_categoria'] ?></td>
                            <td><?= $c['nombre_categoria'] ?></td>
                            <td><?= $c['descripcion'] ?></td>
                            <td><span class="badge bg-<?= $c['estado'] == 'activo' ? 'success' : 'secondary' ?>"><?= $c['estado'] ?></span></td>
                            <td>
                                <a href="crud_categorias.php?editar=<?= $c['id_categoria'] ?>#form-categorias" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../controllers/AdminCrudController.php?eliminar_categoria=<?= $c['id_categoria'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar categoría?')">Eliminar</a>
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
<script>
$(document).ready(function () {
    $('#tablaCategorias').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'}});
});
</script>
</body>
</html>
