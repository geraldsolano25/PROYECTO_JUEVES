<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Incidentes comunitarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/mi_proyecto/public/css/styles.css">
</head>
<body>
<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">
            <h2 class="text-center mb-3">Iniciar sesión</h2>
            <p class="text-muted text-center">Accede a la plataforma de incidentes comunitarios.</p>

            <?php if (isset($_GET['registro']) && $_GET['registro'] == 1): ?>
                <div class="alert alert-success">✅ Registro completado. Ahora puede iniciar sesión.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger">❌ Correo o contraseña incorrectos.</div>
            <?php endif; ?>

            <form method="POST" action="../controllers/AuthController.php" class="mt-3">
                <div class="mb-3"><input type="email" class="form-control" name="correo" placeholder="Correo" required></div>
                <div class="mb-3"><input type="password" class="form-control" name="password" placeholder="Contraseña" required></div>
                <button class="btn btn-primary w-100" name="accion" value="login">Ingresar</button>
            </form>

            <div class="text-center mt-3"><a href="register.php">Crear una cuenta</a></div>
        </div>
    </div>
</div>
</body>
</html>
