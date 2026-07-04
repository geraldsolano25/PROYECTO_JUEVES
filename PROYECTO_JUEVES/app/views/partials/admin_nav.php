<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuAdmin">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_categorias.php">Categorías</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_reportes.php">Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_seguimientos.php">Seguimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_votos.php">Votos</a></li>
            </ul>
            <a class="btn btn-outline-light btn-sm" href="../controllers/AuthController.php?logout=true">Cerrar sesión</a>
        </div>
    </div>
</nav>
