<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

class Usuario {

    public static function login($email, $password) {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
        if ($user && $user['password'] === $password) {
            return $user;
        }

        return false;
    }

    public static function registrar($nombre, $email, $password) {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol)
             VALUES (?, ?, ?, 'usuario')"
        );

        $stmt->execute([$nombre, $email, $password]);
    }
}
