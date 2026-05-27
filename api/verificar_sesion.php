<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'logueado' => isset($_SESSION['usuario_id']),
    'usuario' => $_SESSION['usuario_nombre'] ?? null
]);
?>
