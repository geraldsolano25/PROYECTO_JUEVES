<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {
    private static function cargarPHPMailer() {
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
            return;
        }

        require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
    }

    private static function config() {
        return require __DIR__ . '/../../config/mail.php';
    }

    public static function enviar($destinatario, $nombre, $asunto, $html, $texto = '') {
        $config = self::config();

        if (!$config['enabled'] || empty($config['username']) || empty($config['password']) || empty($config['from_email'])) {
            return self::registrarEnLog($destinatario, $nombre, $asunto, $texto ?: strip_tags($html), $config['debug_log']);
        }

        self::cargarPHPMailer();

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->Port = $config['port'];

            if ($config['encryption'] === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($destinatario, $nombre);
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $html;
            $mail->AltBody = $texto ?: strip_tags($html);

            return $mail->send();
        } catch (Exception $e) {
            self::registrarEnLog($destinatario, $nombre, 'ERROR: ' . $asunto, $e->getMessage(), $config['debug_log']);
            return false;
        }
    }

    private static function registrarEnLog($destinatario, $nombre, $asunto, $contenido, $ruta) {
        $directorio = dirname($ruta);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $entrada = "[" . date('Y-m-d H:i:s') . "]\n";
        $entrada .= "Para: {$nombre} <{$destinatario}>\n";
        $entrada .= "Asunto: {$asunto}\n";
        $entrada .= trim($contenido) . "\n\n";

        return file_put_contents($ruta, $entrada, FILE_APPEND) !== false;
    }

    private static function escapar($valor) {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    public static function enviarCambioEstado($reporte, $estadoNuevo, $prioridad, $comentario) {
        if (empty($reporte['correo'])) {
            return false;
        }

        $estadoLegible = ucfirst(str_replace('_', ' ', $estadoNuevo));
        $titulo = self::escapar($reporte['titulo']);
        $nombre = self::escapar($reporte['nombre']);
        $prioridadLegible = self::escapar($prioridad);
        $comentarioSeguro = self::escapar($comentario ?: 'Sin comentario adicional.');
        $asunto = "Actualizacion de su reporte: " . $reporte['titulo'];
        $html = "
            <h2>Su reporte fue actualizado</h2>
            <p>Hola {$nombre}, el reporte <strong>{$titulo}</strong> ahora esta en estado <strong>{$estadoLegible}</strong>.</p>
            <p><strong>Prioridad:</strong> {$prioridadLegible}</p>
            <p><strong>Comentario:</strong> " . nl2br($comentarioSeguro) . "</p>
            <p>Gracias por usar AlertaComunal.</p>
        ";
        $texto = "Su reporte '{$reporte['titulo']}' ahora esta en estado {$estadoLegible}. Prioridad: {$prioridad}. Comentario: " . ($comentario ?: 'Sin comentario adicional.');

        return self::enviar($reporte['correo'], $reporte['nombre'], $asunto, $html, $texto);
    }
}
