<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">
            <h2 class="text-center mb-3">Registro de ciudadano</h2>
            <form method="POST" action="../controllers/AuthController.php">
                <div class="mb-3"><input type="text" class="form-control" name="nombre" placeholder="Nombre completo" required></div>
                <div class="mb-3"><input type="email" class="form-control" name="correo" placeholder="Correo" required></div>
                <div class="mb-3"><input type="password" class="form-control" name="password" placeholder="Contraseña" required></div>
                <div class="mb-3"><input type="text" class="form-control" name="telefono" placeholder="Teléfono (opcional)"></div>
                <button class="btn btn-primary w-100" name="accion" value="registro">Registrarse</button>
            </form>
            <div class="text-center mt-3"><a href="../../public/index.php">Volver al login</a></div>
        </div>
    </div>
</div>
</body>
</html>
