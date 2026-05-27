<?php

class Database {
    public static function getConnection() {
        return new PDO(
            "mysql:host=localhost;dbname=datospetadopta;charset=utf8",
            "phpmyadmin",
            "adrian123",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
