<?php
session_start();
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

if (isset($_POST['crear_reporte'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $id_categoria = $_POST['id_categoria'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $distrito = $_POST['distrito'];
    $canton = $_POST['canton'];
    $provincia = $_POST['provincia'];
    $imagen = $_POST['imagen'] ?? '';
    $estado = 'pendiente';
    $prioridad = 'media';

    Incidente::crear($id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad);
    header("Location: ../views/dashboard.php?success=1");
    exit();
}

if (isset($_POST['actualizar_estado'])) {
    requerirAdmin();

    $id_reporte = $_POST['id_reporte'];
    $estado = $_POST['estado'];
    $prioridad = $_POST['prioridad'];
    $comentario = $_POST['comentario'];
    $id_usuario_admin = $_SESSION['usuario']['id_usuario'];

    Incidente::actualizarEstado($id_reporte, $estado, $prioridad, $comentario, $id_usuario_admin);
    header("Location: ../views/administracion.php");
    exit();
}

if (isset($_GET['votar'])) {
    $id_reporte = $_GET['votar'];
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    Incidente::votar($id_reporte, $id_usuario);
    header("Location: ../views/dashboard.php");
    exit();
}
