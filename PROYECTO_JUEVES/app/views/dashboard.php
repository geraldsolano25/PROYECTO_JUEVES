<?php
session_start();
require_once "../models/Categoria.php";
require_once "../models/Incidente.php";
require_once "../helpers/report_format.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$categorias = Categoria::obtenerActivas();
$categoriasFiltro = Categoria::obtenerActivas();
$misReportes = Incidente::obtenerPorUsuario($_SESSION['usuario']['id_usuario']);
$filtrosReportes = [
    'id_categoria' => $_GET['id_categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'provincia' => $_GET['provincia'] ?? '',
    'canton' => $_GET['canton'] ?? '',
];
$hayFiltros = array_filter($filtrosReportes, fn($valor) => trim((string) $valor) !== '') !== [];
$reportes = Incidente::obtenerFiltrados($filtrosReportes);
$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
                <?php if ($esAdmin): ?>
                <li class="nav-item"><a class="nav-link" href="crud_usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_categorias.php">Categorías</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_reportes.php">Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_seguimientos.php">Seguimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="crud_votos.php">Votos</a></li>
                <li class="nav-item"><a class="nav-link" href="estadisticas.php">Estadisticas</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="mis_reportes.php">Mis reportes</a></li>
            </ul>
            <a class="btn btn-outline-light btn-sm" href="../controllers/AuthController.php?logout=true">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container py-2">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="mb-1">Panel principal</h2>
            <p class="text-muted mb-0">Bienvenido, <?= $_SESSION['usuario']['nombre']; ?></p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Reporte registrado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'sin_permiso'): ?>
        <div class="alert alert-warning">No tiene permisos para acceder a esa seccion.</div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4" id="reportar">
        <div class="card-body">
            <h3>Registrar incidente</h3>
            <form method="POST" action="../controllers/IncidenteController.php" class="row g-3" data-location-form>
                <div class="col-md-4"><select class="form-select" name="id_categoria" required><option value="">Seleccione una categoría</option><?php while ($c = $categorias->fetch_assoc()): ?><option value="<?= $c['id_categoria'] ?>"><?= $c['nombre_categoria'] ?></option><?php endwhile; ?></select></div>
                <div class="col-md-4"><input type="text" class="form-control" name="titulo" placeholder="Título del incidente" required></div>
                <div class="col-md-4"><select class="form-select" name="provincia" data-location-field="provincia" required><option value="">Cargando provincias...</option></select></div>
                <div class="col-md-4"><select class="form-select" name="canton" data-location-field="canton" required><option value="">Seleccione canton</option></select></div>
                <div class="col-md-4"><select class="form-select" name="distrito" data-location-field="distrito" required><option value="">Seleccione distrito</option></select></div>
                <div class="col-12"><small class="text-muted" data-location-status></small></div>
                <div class="col-12"><textarea class="form-control" name="descripcion" placeholder="Descripción detallada" required></textarea></div>
                <div class="col-12"><input type="text" class="form-control" name="imagen" placeholder="URL de evidencia (opcional)"></div>
                <div class="col-12"><button class="btn btn-primary" type="submit" name="crear_reporte">Reportar incidente</button></div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="mis-reportes">
        <div class="card-body">
            <h3>Mis reportes</h3>
            <?php if ($misReportes->num_rows > 0): ?>
                <?php while ($r = $misReportes->fetch_assoc()): ?>
                    <div class="border rounded p-3 mb-2">
                        <strong><?= $r['titulo'] ?></strong><br>
                        <span class="text-muted"><?= $r['nombre_categoria'] ?></span><br>
                        <span class="status-badge <?= estadoReporteClass($r['estado']) ?>"><?= estadoReporteLabel($r['estado']) ?></span>
                        <span class="priority-badge <?= prioridadReporteClass($r['prioridad']) ?>"><?= prioridadReporteLabel($r['prioridad']) ?></span><br>
                        <?= $r['descripcion'] ?><br>
                        <small>Creado: <?= $r['fecha_creacion'] ?></small>
                        <div class="mt-2">
                            <a href="detalle_reporte.php?id=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-primary">Ver seguimiento</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No hay reportes registrados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm" id="reportes-comunitarios">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                <h3 class="mb-0">Reportes comunitarios</h3>
                <?php if ($hayFiltros): ?>
                    <a href="dashboard.php#reportes-comunitarios" class="btn btn-sm btn-outline-secondary">Limpiar filtros</a>
                <?php endif; ?>
            </div>

            <form method="GET" action="dashboard.php#reportes-comunitarios" class="row g-3 mb-4" data-location-form>
                <div class="col-md-3">
                    <select class="form-select" name="id_categoria">
                        <option value="">Todas las categorias</option>
                        <?php while ($c = $categoriasFiltro->fetch_assoc()): ?>
                            <option value="<?= $c['id_categoria'] ?>" <?= ($filtrosReportes['id_categoria'] ?? '') == $c['id_categoria'] ? 'selected' : '' ?>><?= $c['nombre_categoria'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <?php foreach (estadosReporte() as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= ($filtrosReportes['estado'] ?? '') == $valor ? 'selected' : '' ?>><?= $texto ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="provincia" data-location-field="provincia" data-selected="<?= $filtrosReportes['provincia'] ?>" data-empty-label="Todas las provincias">
                        <option value="">Cargando provincias...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="canton" data-location-field="canton" data-selected="<?= $filtrosReportes['canton'] ?>" data-empty-label="Todos los cantones">
                        <option value="">Todos los cantones</option>
                    </select>
                </div>
                <div class="col-12"><small class="text-muted" data-location-status></small></div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit">Filtrar</button>
                </div>
            </form>

            <?php if ($reportes->num_rows > 0): ?>
                <?php while ($r = $reportes->fetch_assoc()): ?>
                    <div class="border rounded p-3 mb-2">
                        <strong><?= $r['titulo'] ?></strong><br>
                        <span class="text-muted"><?= $r['nombre'] ?> · <?= $r['nombre_categoria'] ?></span><br>
                        <?= $r['descripcion'] ?><br>
                        Zona: <?= $r['distrito'] ?>, <?= $r['canton'] ?>, <?= $r['provincia'] ?><br>
                        <div class="report-meta">
                            <span class="status-badge <?= estadoReporteClass($r['estado']) ?>"><?= estadoReporteLabel($r['estado']) ?></span>
                            <span class="priority-badge <?= prioridadReporteClass($r['prioridad']) ?>"><?= prioridadReporteLabel($r['prioridad']) ?></span>
                            <span class="vote-count">Votos: <?= Incidente::contarVotos($r['id_reporte']) ?></span>
                        </div>
                        <a href="../controllers/IncidenteController.php?votar=<?= $r['id_reporte'] ?>" class="btn btn-sm btn-outline-primary mt-2">Votar prioridad</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0">No hay reportes que coincidan con los filtros seleccionados.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../public/js/costa-rica-location.js"></script>
</body>
</html>
