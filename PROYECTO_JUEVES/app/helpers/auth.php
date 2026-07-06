<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioActual() {
    return $_SESSION['usuario'] ?? null;
}

function usuarioEsAdmin() {
    return (usuarioActual()['rol'] ?? '') === 'admin';
}

function requerirLogin() {
    if (!usuarioActual()) {
        header("Location: ../../public/index.php");
        exit();
    }
}

function requerirAdmin() {
    requerirLogin();

    if (!usuarioEsAdmin()) {
        header("Location: ../views/dashboard.php?error=sin_permiso");
        exit();
    }
}
