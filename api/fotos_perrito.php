<?php
session_start();
require_once '/app/core/Database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Obtener ID del perrito
$perrito_id = $_GET['id'] ?? 0;

if(!$perrito_id) {
    echo json_encode(['error' => 'ID de perrito no proporcionado']);
    exit;
}

$db = new Database();

// Si el usuario está logueado, devuelve TODAS las fotos
if(isset($_SESSION['usuario_id'])) {
    $fotos = $db->query(
        "SELECT * FROM fotos_perritos 
         WHERE perrito_id = ? 
         ORDER BY es_principal DESC, orden ASC",
        [$perrito_id]
    )->fetchAll();
} else {
    // Invitado: solo la foto principal
    $fotos = $db->query(
        "SELECT * FROM fotos_perritos 
         WHERE perrito_id = ? AND es_principal = 1",
        [$perrito_id]
    )->fetchAll();
}

echo json_encode($fotos);
?>
