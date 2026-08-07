<?php
require_once "../../config/database.php";

class Usuario {
    public static function registrar($nombre, $correo, $password, $telefono = null, $rol = 'ciudadano') {
        $db = Database::conectar();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO usuarios (nombre, correo, password, telefono, rol, estado, fecha_registro) VALUES (?, ?, ?, ?, ?, 'activo', NOW())");
        $stmt->bind_param("sssss", $nombre, $correo, $hash, $telefono, $rol);

        return $stmt->execute();
    }

    public static function login($correo, $password) {
        $db = Database::conectar();

        $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $usuario = $stmt->get_result()->fetch_assoc();

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }

        return false;
    }

    public static function obtenerTodos() {
        $db = Database::conectar();
        return $db->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
    }

    public static function obtenerPorId($id) {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function guardar($nombre, $correo, $password, $telefono, $rol, $estado) {
        $db = Database::conectar();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, correo, password, telefono, rol, estado, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $nombre, $correo, $hash, $telefono, $rol, $estado);

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public static function actualizar($id, $nombre, $correo, $telefono, $rol, $estado) {
        $db = Database::conectar();
        $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ?, rol = ?, estado = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssssi", $nombre, $correo, $telefono, $rol, $estado, $id);
        return $stmt->execute();
    }

    public static function actualizarPerfil($id, $nombre, $correo, $telefono, $password = '') {
        $db = Database::conectar();

        if (trim($password) !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ?, password = ? WHERE id_usuario = ?");
            $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $hash, $id);
        } else {
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ? WHERE id_usuario = ?");
            $stmt->bind_param("sssi", $nombre, $correo, $telefono, $id);
        }

        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public static function eliminar($id) {
        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
