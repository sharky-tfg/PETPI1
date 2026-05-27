<?php
// Auth.php - SIN session_start() aquí
class Auth {
    public static function login($usuario) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['usuario'] = $usuario;
    }

    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario']);
    }

    public static function esAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return self::check() && $_SESSION['usuario']['rol'] === 'admin';
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }
}
?>
