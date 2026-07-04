<?php
require_once "../../config/database.php";

class Categoria {
    public static function obtenerActivas() {
        $db = Database::conectar();
        $resultado = $db->query("SELECT * FROM categorias WHERE estado = 'activo' ORDER BY nombre_categoria");
        return $resultado;
    }

    public static function obtenerTodas() {
        $db = Database::conectar();
        return $db->query("SELECT * FROM categorias ORDER BY nombre_categoria");
    }

    public static function obtenerPorId($id) {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function guardar($nombre, $descripcion, $estado) {
        $db = Database::conectar();
        $stmt = $db->prepare("INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $descripcion, $estado);
        return $stmt->execute();
    }

    public static function actualizar($id, $nombre, $descripcion, $estado) {
        $db = Database::conectar();
        $stmt = $db->prepare("UPDATE categorias SET nombre_categoria = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
        $stmt->bind_param("sssi", $nombre, $descripcion, $estado, $id);
        return $stmt->execute();
    }

    public static function eliminar($id) {
        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
