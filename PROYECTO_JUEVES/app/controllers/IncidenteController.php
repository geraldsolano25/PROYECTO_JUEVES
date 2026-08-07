<?php
session_start();
require_once "../helpers/auth.php";
require_once "../models/Incidente.php";
require_once "../helpers/Mailer.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

if (isset($_POST['crear_reporte'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $id_categoria = $_POST['id_categoria'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $distrito = trim($_POST['distrito']);
    $canton = trim($_POST['canton']);
    $provincia = trim($_POST['provincia']);
    $ubicacion = trim($distrito . ', ' . $canton . ', ' . $provincia);
    $imagen = $_POST['imagen'] ?? '';
    $estado = 'pendiente';
    $prioridad = 'media';

    Incidente::crear($id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad);
    header("Location: ../views/dashboard.php?success=1#reportar");
    exit();
}

if (isset($_POST['actualizar_estado'])) {
    requerirAdmin();

    $id_reporte = $_POST['id_reporte'];
    $estado = $_POST['estado'];
    $prioridad = $_POST['prioridad'];
    $comentario = $_POST['comentario'];
    $id_usuario_admin = $_SESSION['usuario']['id_usuario'];
    $reporte = Incidente::obtenerPorId($id_reporte);

    $actualizado = Incidente::actualizarEstado($id_reporte, $estado, $prioridad, $comentario, $id_usuario_admin);
    if ($actualizado && $reporte) {
        Mailer::enviarCambioEstado($reporte, $estado, $prioridad, $comentario);
    }

    header("Location: ../views/crud_reportes.php?correo=" . ($actualizado ? "procesado" : "error") . "#tabla-reportes");
    exit();
}

if (isset($_POST['actualizar_reporte_pendiente'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $distrito = trim($_POST['distrito']);
    $canton = trim($_POST['canton']);
    $provincia = trim($_POST['provincia']);
    $ubicacion = trim($distrito . ', ' . $canton . ', ' . $provincia);
    $actualizado = Incidente::actualizarPendientePorUsuario(
        $_POST['id_reporte'],
        $id_usuario,
        $_POST['id_categoria'],
        trim($_POST['titulo']),
        trim($_POST['descripcion']),
        $ubicacion,
        $distrito,
        $canton,
        $provincia,
        trim($_POST['imagen'] ?? '')
    );

    header("Location: ../views/mis_reportes.php?" . ($actualizado ? "reporte_actualizado=1" : "error=accion_no_permitida") . "#mis-reportes");
    exit();
}

if (isset($_POST['cancelar_reporte_pendiente'])) {
    $cancelado = Incidente::eliminarPendientePorUsuario($_POST['id_reporte'], $_SESSION['usuario']['id_usuario']);
    header("Location: ../views/mis_reportes.php?" . ($cancelado ? "reporte_cancelado=1" : "error=accion_no_permitida") . "#mis-reportes");
    exit();
}

if (isset($_GET['votar'])) {
    $id_reporte = $_GET['votar'];
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    Incidente::votar($id_reporte, $id_usuario);
    header("Location: ../views/dashboard.php#reportes-comunitarios");
    exit();
}
