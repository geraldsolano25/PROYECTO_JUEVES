<?php
session_start();
require_once "../models/Usuario.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registro') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'] ?? null;
    $rol = 'ciudadano';

    Usuario::registrar($nombre, $correo, $password, $telefono, $rol);
    header("Location: ../../public/index.php?registro=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $user = Usuario::login($_POST['correo'], $_POST['password']);

    if ($user) {
        $_SESSION['usuario'] = $user;
        header("Location: ../views/dashboard.php");
        exit();
    }

    header("Location: ../../public/index.php?error=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_perfil') {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../../public/index.php");
        exit();
    }

    $actualizado = Usuario::actualizarPerfil(
        $_SESSION['usuario']['id_usuario'],
        trim($_POST['nombre']),
        trim($_POST['correo']),
        trim($_POST['telefono'] ?? ''),
        $_POST['password'] ?? ''
    );

    if ($actualizado) {
        $usuario = Usuario::obtenerPorId($_SESSION['usuario']['id_usuario']);
        $_SESSION['usuario'] = $usuario;
        header("Location: ../views/perfil.php?success=1");
    } else {
        header("Location: ../views/perfil.php?error=1");
    }
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../../public/index.php");
    exit();
}
