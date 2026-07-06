<?php
session_start();
require_once "../models/Reserva.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit();
}

$reservas = Reserva::obtenerPorUsuario($_SESSION['usuario']['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>

<div class="container dashboard">
    <h2>Mis Reservas</h2>

    <a href="dashboard.php">Volver</a>

    <br><br>

    <?php while($r = $reservas->fetch_assoc()): ?>
<div class="reserva">
    <strong><?= $r['nombre'] ?></strong><br>
    📅 <?= $r['fecha'] ?><br>
    👥 <?= $r['personas'] ?> personas<br>
    💬 <?= $r['comentarios'] ?><br><br>

    <!-- BOTÓN EDITAR -->
    <a class="btn-editar" href="editar.php?id=<?= $r['id'] ?>">
        Editar
    </a>

    <!-- BOTÓN ELIMINAR -->
    <a class="btn-eliminar" 
       href="../controllers/ReservaController.php?delete=<?= $r['id'] ?>"
       onclick="return confirm('¿Eliminar reserva?')">
       Eliminar
    </a>
</div>
    <?php endwhile; ?>

</div>

</body>
</html>
