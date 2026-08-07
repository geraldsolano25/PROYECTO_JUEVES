<?php
require_once "../helpers/auth.php";

requerirLogin();
$usuario = usuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<?php include "partials/main_nav.php"; ?>
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Mi perfil</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary">Volver</a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Perfil actualizado correctamente.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">No se pudo actualizar el perfil. Revise si el correo ya esta registrado.</div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AuthController.php" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="nombre" placeholder="Nombre completo" value="<?= $usuario['nombre'] ?>" required>
                </div>
                <div class="col-md-6">
                    <input type="email" class="form-control" name="correo" placeholder="Correo" value="<?= $usuario['correo'] ?>" required>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="telefono" placeholder="Telefono" value="<?= $usuario['telefono'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <input type="password" class="form-control" name="password" placeholder="Nueva contrasena (opcional)">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" name="accion" value="actualizar_perfil">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
