<?php
require_once "../../config/database.php";

class Incidente {
    private static $estadosPermitidos = ['pendiente', 'en_revision', 'en_proceso', 'resuelto', 'rechazado'];
    private static $prioridadesPermitidas = ['baja', 'media', 'alta'];

    private static function idPositivo($valor) {
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? $id : null;
    }

    private static function normalizarEstado($estado) {
        return in_array($estado, self::$estadosPermitidos, true) ? $estado : 'pendiente';
    }

    private static function normalizarPrioridad($prioridad) {
        return in_array($prioridad, self::$prioridadesPermitidas, true) ? $prioridad : 'media';
    }

    public static function crear($id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad) {
        $estado = self::normalizarEstado($estado);
        $prioridad = self::normalizarPrioridad($prioridad);
        $db = Database::conectar();
        $sql = "INSERT INTO reportes (id_usuario, id_categoria, titulo, descripcion, ubicacion, distrito, canton, provincia, imagen, estado, prioridad, fecha_creacion, fecha_actualizacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iisssssssss", $id_usuario, $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad);
        return $stmt->execute();
    }

    public static function obtenerPorUsuario($id_usuario) {
        $id_usuario = self::idPositivo($id_usuario) ?? 0;
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT r.*, c.nombre_categoria FROM reportes r LEFT JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id_usuario = ? ORDER BY r.fecha_creacion DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function obtenerTodos() {
        $db = Database::conectar();
        return $db->query("SELECT r.*, u.nombre, c.nombre_categoria FROM reportes r LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario LEFT JOIN categorias c ON r.id_categoria = c.id_categoria ORDER BY r.prioridad DESC, r.fecha_creacion DESC");
    }

    public static function obtenerPorId($id_reporte) {
        $id_reporte = self::idPositivo($id_reporte) ?? 0;
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT r.*, u.nombre, c.nombre_categoria FROM reportes r LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario LEFT JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id_reporte = ?");
        $stmt->bind_param("i", $id_reporte);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function actualizarEstado($id_reporte, $estado, $prioridad, $comentario, $id_usuario_admin) {
        $id_reporte = self::idPositivo($id_reporte);
        $id_usuario_admin = self::idPositivo($id_usuario_admin);
        $estado = self::normalizarEstado($estado);
        $prioridad = self::normalizarPrioridad($prioridad);
        if ($id_reporte === null || $id_usuario_admin === null) {
            return false;
        }

        $db = Database::conectar();
        $reporte = self::obtenerPorId($id_reporte);
        if (!$reporte) {
            return false;
        }
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
        $id_reporte = self::idPositivo($id_reporte);
        $id_categoria = self::idPositivo($id_categoria);
        $estado = self::normalizarEstado($estado);
        $prioridad = self::normalizarPrioridad($prioridad);
        if ($id_reporte === null || $id_categoria === null) {
            return false;
        }

        $db = Database::conectar();
        $stmt = $db->prepare("UPDATE reportes SET id_categoria = ?, titulo = ?, descripcion = ?, ubicacion = ?, distrito = ?, canton = ?, provincia = ?, imagen = ?, estado = ?, prioridad = ?, fecha_actualizacion = NOW() WHERE id_reporte = ?");
        $stmt->bind_param("isssssssssi", $id_categoria, $titulo, $descripcion, $ubicacion, $distrito, $canton, $provincia, $imagen, $estado, $prioridad, $id_reporte);
        return $stmt->execute();
    }

    public static function eliminar($id_reporte) {
        $id_reporte = self::idPositivo($id_reporte);
        if ($id_reporte === null) {
            return false;
        }

        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM reportes WHERE id_reporte = ?");
        $stmt->bind_param("i", $id_reporte);
        return $stmt->execute();
    }

    public static function obtenerSeguimiento($id_reporte) {
        $id_reporte = self::idPositivo($id_reporte) ?? 0;
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT s.*, u.nombre FROM seguimiento_reportes s LEFT JOIN usuarios u ON s.id_usuario_admin = u.id_usuario WHERE s.id_reporte = ? ORDER BY s.fecha_cambio DESC");
        $stmt->bind_param("i", $id_reporte);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function votar($id_reporte, $id_usuario) {
        $id_reporte = self::idPositivo($id_reporte);
        $id_usuario = self::idPositivo($id_usuario);
        if ($id_reporte === null || $id_usuario === null) {
            return false;
        }

        $db = Database::conectar();
        $stmtExiste = $db->prepare("SELECT id_voto FROM votos_reportes WHERE id_reporte = ? AND id_usuario = ?");
        $stmtExiste->bind_param("ii", $id_reporte, $id_usuario);
        $stmtExiste->execute();
        $existe = $stmtExiste->get_result();
        if ($existe->num_rows > 0) {
            return false;
        }

        $stmt = $db->prepare("INSERT INTO votos_reportes (id_reporte, id_usuario, fecha_voto) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $id_reporte, $id_usuario);
        return $stmt->execute();
    }

    public static function contarVotos($id_reporte) {
        $id_reporte = self::idPositivo($id_reporte);
        if ($id_reporte === null) {
            return 0;
        }

        $db = Database::conectar();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM votos_reportes WHERE id_reporte = ?");
        $stmt->bind_param("i", $id_reporte);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc()['total'];
    }
}
