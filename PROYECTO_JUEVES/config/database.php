<?php
class Database {
    private static $conexion = null;

    public static function conectar() {
        if (self::$conexion !== null) {
            return self::$conexion;
        }

        $conexion = new mysqli("localhost", "root", "root", "PROYECTO_JUEVES");

        if ($conexion->connect_error) {
            $base = new mysqli("localhost", "root", "root");
            if ($base->connect_error) {
                die("Error de conexión: " . $base->connect_error);
            }
            $base->query("CREATE DATABASE IF NOT EXISTS PROYECTO_JUEVES");
            $base->close();
            $conexion = new mysqli("localhost", "root", "root", "PROYECTO_JUEVES");
        }

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        self::$conexion = $conexion;
        self::inicializar();
        return self::$conexion;
    }

    public static function inicializar() {
        $sql = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            telefono VARCHAR(20) DEFAULT NULL,
            rol VARCHAR(20) NOT NULL DEFAULT 'ciudadano',
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categorias (
            id_categoria INT AUTO_INCREMENT PRIMARY KEY,
            nombre_categoria VARCHAR(100) NOT NULL,
            descripcion TEXT,
            estado VARCHAR(20) NOT NULL DEFAULT 'activo'
        );

        CREATE TABLE IF NOT EXISTS reportes (
            id_reporte INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_categoria INT NOT NULL,
            titulo VARCHAR(150) NOT NULL,
            descripcion TEXT NOT NULL,
            ubicacion VARCHAR(255) NOT NULL,
            distrito VARCHAR(100) NOT NULL,
            canton VARCHAR(100) NOT NULL,
            provincia VARCHAR(100) NOT NULL,
            imagen VARCHAR(255) DEFAULT NULL,
            estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
            prioridad VARCHAR(20) NOT NULL DEFAULT 'media',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS seguimiento_reportes (
            id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
            id_reporte INT NOT NULL,
            id_usuario_admin INT NOT NULL,
            estado_anterior VARCHAR(30) NOT NULL,
            estado_nuevo VARCHAR(30) NOT NULL,
            comentario TEXT,
            fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS votos_reportes (
            id_voto INT AUTO_INCREMENT PRIMARY KEY,
            id_reporte INT NOT NULL,
            id_usuario INT NOT NULL,
            fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";

        self::$conexion->multi_query($sql);
        do {
            if ($result = self::$conexion->store_result()) {
                $result->free();
            }
        } while (self::$conexion->more_results() && self::$conexion->next_result());

        $categorias = self::$conexion->query("SELECT COUNT(*) AS total FROM categorias");
        $total = $categorias->fetch_assoc()['total'];
        if ($total == 0) {
            self::$conexion->query("INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES
                ('Servicios públicos', 'Problemas relacionados con agua, luz, alumbrado y servicios básicos', 'activo'),
                ('Seguridad', 'Incidentes de seguridad, delincuencia o vandalismo', 'activo'),
                ('Vías y movilidad', 'Problemas en carreteras, transporte o señalización', 'activo'),
                ('Medio ambiente', 'Basura, focos de contaminación o zonas afectadas', 'activo'),
                ('Infraestructura', 'Daños en parques, edificios o espacios comunitarios', 'activo')");
        }
    }
}