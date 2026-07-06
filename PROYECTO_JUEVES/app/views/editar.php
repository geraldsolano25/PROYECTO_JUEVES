<?php
require_once "../../config/database.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id <= 0) {
    header("Location: mis_reservas.php");
    exit();
}

$db = Database::conectar();

$stmt = $db->prepare("SELECT * FROM reservas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if (!$r) {
    header("Location: mis_reservas.php");
    exit();
}
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
