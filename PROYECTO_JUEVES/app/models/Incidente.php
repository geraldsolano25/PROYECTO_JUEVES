<?php
require_once "../../config/database.php";

class Incidente {
    public static function crear($id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad) {
        $db = Database::conectar();
        $sql = "INSERT INTO reportes (id_usuario, id_categoria, titulo, descripcion, ubicacion, distrito, canton, provincia, imagen, estado, prioridad, fecha_creacion, fecha_actualizacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iisssssssss", $id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad);
        return $stmt->execute();
    }

    public static function obtenerPorUsuario($id_usuario) {
        $db = Database::conectar();
        return $db->query("SELECT r.*, c.nombre_categoria FROM reportes r LEFT JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id_usuario = $id_usuario ORDER BY r.fecha_creacion DESC");
    }

    public static function obtenerTodos() {
        $db = Database::conectar();
        return $db->query("SELECT r.*, u.nombre, c.nombre_categoria FROM reportes r LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario LEFT JOIN categorias c ON r.id_categoria = c.id_categoria ORDER BY r.prioridad DESC, r.fecha_creacion DESC");
    }

    public static function obtenerPorId($id_reporte) {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT r.*, u.nombre, c.nombre_categoria FROM reportes r LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario LEFT JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id_reporte = ?");
        $stmt->bind_param("i", $id_reporte);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function actualizarEstado($id_reporte, $estado, $prioridad, $comentario, $id_usuario_admin) {
        $db = Database::conectar();
        $reporte = self::obtenerPorId($id_reporte);
        $estado_anterior = $reporte['estado'];

        $sql = "UPDATE reportes SET estado = ?, prioridad = ?, fecha_actualizacion = NOW() WHERE id_reporte = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssi", $estado, $prioridad, $id_reporte);
        $stmt->execute();

        $sqlSeguimiento = "INSERT INTO seguimiento_reportes (id_reporte, id_usuario_admin, estado_anterior, estado_nuevo, comentario, fecha_cambio) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmtSeguimiento = $db->prepare($sqlSeguimiento);
        $stmtSeguimiento->bind_param("iisss", $id_reporte, $id_usuario_admin, $estado_anterior, $estado, $comentario);
        return $stmtSeguimiento->execute();
    }

    public static function actualizar($id_reporte, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad) {
        $db = Database::conectar();
        $stmt = $db->prepare("UPDATE reportes SET id_categoria = ?, titulo = ?, descripcion = ?, ubicacion = ?, distrito = ?, canton = ?, provincia = ?, imagen = ?, estado = ?, prioridad = ?, fecha_actualizacion = NOW() WHERE id_reporte = ?");
        $stmt->bind_param("isssssssssi", $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad, $id_reporte);
        return $stmt->execute();
    }

    public static function eliminar($id_reporte) {
        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM reportes WHERE id_reporte = ?");
        $stmt->bind_param("i", $id_reporte);
        return $stmt->execute();
    }

    public static function obtenerSeguimiento($id_reporte) {
        $db = Database::conectar();
        return $db->query("SELECT s.*, u.nombre FROM seguimiento_reportes s LEFT JOIN usuarios u ON s.id_usuario_admin = u.id_usuario WHERE s.id_reporte = $id_reporte ORDER BY s.fecha_cambio DESC");
    }

    public static function votar($id_reporte, $id_usuario) {
        $db = Database::conectar();
        $existe = $db->query("SELECT id_voto FROM votos_reportes WHERE id_reporte = $id_reporte AND id_usuario = $id_usuario");
        if ($existe->num_rows > 0) {
            return false;
        }

        $stmt = $db->prepare("INSERT INTO votos_reportes (id_reporte, id_usuario, fecha_voto) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $id_reporte, $id_usuario);
        return $stmt->execute();
    }

    public static function contarVotos($id_reporte) {
        $db = Database::conectar();
        $resultado = $db->query("SELECT COUNT(*) AS total FROM votos_reportes WHERE id_reporte = $id_reporte");
        return $resultado->fetch_assoc()['total'];
    }
}
