<?php
session_start();
require_once "../models/Usuario.php";
require_once "../models/Categoria.php";
require_once "../models/Incidente.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'admin';
$puedeGestionarCategorias = isset($_SESSION['usuario']['id_usuario']) || $esAdmin;

if (isset($_POST['guardar_usuario'])) {
    $guardado = Usuario::guardar($_POST['nombre'], $_POST['correo'], $_POST['password'], $_POST['telefono'], $_POST['rol'], $_POST['estado']);
    if ($guardado) {
        header("Location: ../views/crud_usuarios.php?success=1");
    } else {
        header("Location: ../views/crud_usuarios.php?error=1");
    }
    exit();
}

if (isset($_POST['editar_usuario'])) {
    Usuario::actualizar($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['rol'], $_POST['estado']);
    header("Location: ../views/crud_usuarios.php");
    exit();
}

if (isset($_GET['eliminar_usuario'])) {
    Usuario::eliminar($_GET['eliminar_usuario']);
    header("Location: ../views/crud_usuarios.php");
    exit();
}

if (isset($_POST['guardar_categoria'])) {
    Categoria::guardar($_POST['nombre_categoria'], $_POST['descripcion'], $_POST['estado']);
    header("Location: ../views/crud_categorias.php");
    exit();
}

if (isset($_POST['editar_categoria']) || (isset($_POST['id_categoria']) && !empty($_POST['id_categoria']) && isset($_POST['nombre_categoria']) && isset($_POST['descripcion']) && isset($_POST['estado']))) {
    $idCategoria = isset($_POST['id_categoria']) ? (int) $_POST['id_categoria'] : 0;
    if ($idCategoria <= 0) {
        header("Location: ../views/crud_categorias.php");
        exit();
    }
    Categoria::actualizar($idCategoria, $_POST['nombre_categoria'], $_POST['descripcion'], $_POST['estado']);
    header("Location: ../views/crud_categorias.php");
    exit();
}

if (isset($_GET['eliminar_categoria'])) {
    Categoria::eliminar($_GET['eliminar_categoria']);
    header("Location: ../views/crud_categorias.php");
    exit();
}

if (isset($_POST['guardar_reporte'])) {
    Incidente::crear($_SESSION['usuario']['id_usuario'], $_POST['id_categoria'], $_POST['titulo'], $_POST['descripcion'], $_POST['ubicacion'], $_POST['distrito'], $_POST['canton'], $_POST['provincia'], $_POST['imagen'], $_POST['estado'], $_POST['prioridad']);
    header("Location: ../views/crud_reportes.php");
    exit();
}

if (isset($_POST['editar_reporte'])) {
    Incidente::actualizar($_POST['id_reporte'], $_POST['id_categoria'], $_POST['titulo'], $_POST['descripcion'], $_POST['ubicacion'], $_POST['distrito'], $_POST['canton'], $_POST['provincia'], $_POST['imagen'], $_POST['estado'], $_POST['prioridad']);
    header("Location: ../views/crud_reportes.php");
    exit();
}

if (isset($_GET['eliminar_reporte'])) {
    Incidente::eliminar($_GET['eliminar_reporte']);
    header("Location: ../views/crud_reportes.php");
    exit();
}
