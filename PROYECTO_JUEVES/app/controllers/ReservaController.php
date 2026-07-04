<?php
session_start();
require_once "../models/Reserva.php";
require_once "../../config/database.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

// ✅ ELIMINAR
if (isset($_GET['delete'])) {

    Reserva::eliminar($_GET['delete']);
    header("Location: ../views/mis_reservas.php");
    exit();
}

// ✅ ACTUALIZAR (PRIMERO)
if (isset($_POST['actualizar'])) {

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $personas = $_POST['personas'];
    $comentarios = $_POST['comentarios'];

    $db = Database::conectar();

    $stmt = $db->prepare("UPDATE reservas 
        SET nombre=?, fecha=?, personas=?, comentarios=? 
        WHERE id=?");

    $stmt->bind_param("ssisi", $nombre, $fecha, $personas, $comentarios, $id);
    $stmt->execute();

    header("Location: ../views/mis_reservas.php");
    exit(); // 🔥 MUY IMPORTANTE
}

// ✅ GUARDAR (SOLO SI NO ES ACTUALIZAR)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['actualizar'])) {

    $usuario_id = $_SESSION['usuario']['id'];
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $personas = $_POST['personas'];
    $comentarios = $_POST['comentarios'];

    // Validación
    if ($personas <= 0) {
        echo "Cantidad inválida";
        exit();
    }

    Reserva::guardar($usuario_id, $nombre, $fecha, $personas, $comentarios);

    header("Location: ../views/dashboard.php?success=1&nombre=$nombre&fecha=$fecha&personas=$personas");
    exit();
}
?>