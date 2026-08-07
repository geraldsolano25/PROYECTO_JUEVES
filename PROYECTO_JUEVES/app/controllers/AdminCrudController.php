<?php
require_once "../helpers/auth.php";
require_once "../models/Usuario.php";
require_once "../models/Categoria.php";
require_once "../models/Incidente.php";
require_once "../helpers/Mailer.php";

requerirAdmin();

if (isset($_POST['guardar_usuario'])) {
    $guardado = Usuario::guardar($_POST['nombre'], $_POST['correo'], $_POST['password'], $_POST['telefono'], $_POST['rol'], $_POST['estado']);
    if ($guardado) {
        header("Location: ../views/crud_usuarios.php?success=1#tabla-usuarios");
    } else {
        header("Location: ../views/crud_usuarios.php?error=1#form-usuarios");
    }
    exit();
}

if (isset($_POST['editar_usuario'])) {
    Usuario::actualizar($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['rol'], $_POST['estado']);
    header("Location: ../views/crud_usuarios.php#tabla-usuarios");
    exit();
}

if (isset($_GET['eliminar_usuario'])) {
    Usuario::eliminar($_GET['eliminar_usuario']);
    header("Location: ../views/crud_usuarios.php#tabla-usuarios");
    exit();
}

if (isset($_POST['guardar_categoria'])) {
    Categoria::guardar($_POST['nombre_categoria'], $_POST['descripcion'], $_POST['estado']);
    header("Location: ../views/crud_categorias.php#tabla-categorias");
    exit();
}

if (isset($_POST['editar_categoria']) || (isset($_POST['id_categoria']) && !empty($_POST['id_categoria']) && isset($_POST['nombre_categoria']) && isset($_POST['descripcion']) && isset($_POST['estado']))) {
    $idCategoria = isset($_POST['id_categoria']) ? (int) $_POST['id_categoria'] : 0;
    if ($idCategoria <= 0) {
        header("Location: ../views/crud_categorias.php#form-categorias");
        exit();
    }
    Categoria::actualizar($idCategoria, $_POST['nombre_categoria'], $_POST['descripcion'], $_POST['estado']);
    header("Location: ../views/crud_categorias.php#tabla-categorias");
    exit();
}

if (isset($_GET['eliminar_categoria'])) {
    Categoria::eliminar($_GET['eliminar_categoria']);
    header("Location: ../views/crud_categorias.php#tabla-categorias");
    exit();
}

if (isset($_POST['guardar_reporte'])) {
    $distrito = trim($_POST['distrito']);
    $canton = trim($_POST['canton']);
    $provincia = trim($_POST['provincia']);
    $ubicacion = trim($distrito . ', ' . $canton . ', ' . $provincia);
    Incidente::crear($_SESSION['usuario']['id_usuario'], $_POST['id_categoria'], $_POST['titulo'], $_POST['descripcion'], $ubicacion, $distrito, $canton, $provincia, $_POST['imagen'], $_POST['estado'], $_POST['prioridad']);
    header("Location: ../views/crud_reportes.php#tabla-reportes");
    exit();
}

if (isset($_POST['editar_reporte'])) {
    $reporteAnterior = Incidente::obtenerPorId($_POST['id_reporte']);
    $distrito = trim($_POST['distrito']);
    $canton = trim($_POST['canton']);
    $provincia = trim($_POST['provincia']);
    $ubicacion = trim($distrito . ', ' . $canton . ', ' . $provincia);
    $estadoAnterior = $reporteAnterior['estado'] ?? 'pendiente';
    $prioridadAnterior = $reporteAnterior['prioridad'] ?? 'media';

    Incidente::actualizar($_POST['id_reporte'], $_POST['id_categoria'], $_POST['titulo'], $_POST['descripcion'], $ubicacion, $distrito, $canton, $provincia, $_POST['imagen'], $estadoAnterior, $prioridadAnterior);

    $cambioSeguimiento = $reporteAnterior && ($estadoAnterior !== $_POST['estado'] || $prioridadAnterior !== $_POST['prioridad']);
    if ($cambioSeguimiento) {
        $comentario = trim($_POST['comentario_seguimiento'] ?? '');
        if ($comentario === '') {
            $comentario = 'Actualizacion administrativa del reporte.';
        }

        $actualizado = Incidente::actualizarEstado($_POST['id_reporte'], $_POST['estado'], $_POST['prioridad'], $comentario, $_SESSION['usuario']['id_usuario']);
        if ($actualizado) {
            Mailer::enviarCambioEstado($reporteAnterior, $_POST['estado'], $_POST['prioridad'], $comentario);
        }
    }

    header("Location: ../views/crud_reportes.php" . ($cambioSeguimiento ? "?correo=procesado" : "") . "#tabla-reportes");
    exit();
}

if (isset($_GET['eliminar_reporte'])) {
    Incidente::eliminar($_GET['eliminar_reporte']);
    header("Location: ../views/crud_reportes.php#tabla-reportes");
    exit();
}
