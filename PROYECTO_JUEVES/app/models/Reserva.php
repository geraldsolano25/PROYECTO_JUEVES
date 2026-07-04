<?php
require_once "../../config/database.php";

class Reserva {

    public static function guardar($usuario_id, $nombre, $fecha, $personas, $comentarios) {
        $db = Database::conectar();

        $sql = "INSERT INTO reservas (usuario_id, nombre, fecha, personas, comentarios)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("issis", $usuario_id, $nombre, $fecha, $personas, $comentarios);

        return $stmt->execute();
    }

    public static function obtenerPorUsuario($usuario_id) {
    $db = Database::conectar();

    return $db->query("SELECT * FROM reservas WHERE usuario_id = $usuario_id");
}

public static function eliminar($id) {
    $db = Database::conectar();

    return $db->query("DELETE FROM reservas WHERE id = $id");
}
}