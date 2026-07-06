<?php
require_once "../../config/database.php";

$id = $_GET['id'];
$db = Database::conectar();

$r = $db->query("SELECT * FROM reservas WHERE id=$id")->fetch_assoc();
?>

<link rel="stylesheet" href="/public/css/styles.css">

<div class="container">
    <h2>Editar Reserva</h2>

    <form method="POST" action="../controllers/ReservaController.php">

        <input type="hidden" name="id" value="<?= $r['id'] ?>">

        <input type="text" name="nombre" value="<?= $r['nombre'] ?>" required>
        <input type="date" name="fecha" value="<?= $r['fecha'] ?>" required>
        <input type="number" name="personas" value="<?= $r['personas'] ?>" required>

        <textarea name="comentarios"><?= $r['comentarios'] ?></textarea>

        <button type="submit" name="actualizar">Actualizar</button>

    </form>
</div>
