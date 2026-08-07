<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioNav = $_SESSION['usuario'] ?? null;
$esAdminNav = ($usuarioNav['rol'] ?? '') === 'admin';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 app-nav">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">AlertaComunal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="mis_reportes.php">Mis reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="perfil.php">Mi perfil</a></li>
                <?php if ($esAdminNav): ?>
                    <li class="nav-item"><a class="nav-link" href="crud_reportes.php">Gestionar reportes</a></li>
                    <li class="nav-item"><a class="nav-link" href="estadisticas.php">Estadisticas</a></li>
                    <li class="nav-item"><a class="nav-link" href="crud_usuarios.php">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="crud_categorias.php">Categorias</a></li>
                    <li class="nav-item"><a class="nav-link" href="crud_seguimientos.php">Historial</a></li>
                    <li class="nav-item"><a class="nav-link" href="crud_votos.php">Votos</a></li>
                <?php endif; ?>
            </ul>
            <?php if ($usuarioNav): ?>
                <span class="navbar-text me-3"><?= $usuarioNav['nombre'] ?></span>
            <?php endif; ?>
            <a class="btn btn-outline-light btn-sm" href="../controllers/AuthController.php?logout=true">Cerrar sesion</a>
        </div>
    </div>
</nav>
<script src="../../public/js/app-ui.js" defer></script>
