<?php
require_once "../../config/database.php";

class Reserva {
    private static function idPositivo($valor) {
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? $id : null;
    }

    public static function guardar($usuario_id, $nombre, $fecha, $personas, $comentarios) {
        $usuario_id = self::idPositivo($usuario_id);
        if ($usuario_id === null) {
            return false;
        }

        $db = Database::conectar();

        $sql = "INSERT INTO reservas (usuario_id, nombre, fecha, personas, comentarios)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("issis", $usuario_id, $nombre, $fecha, $personas, $comentarios);

        return $stmt->execute();
    }

    public static function obtenerPorUsuario($usuario_id) {
        $usuario_id = self::idPositivo($usuario_id) ?? 0;
        $db = Database::conectar();

        $stmt = $db->prepare("SELECT * FROM reservas WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function eliminar($id) {
        $id = self::idPositivo($id);
        if ($id === null) {
            return false;
        }

        $db = Database::conectar();

        $stmt = $db->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
