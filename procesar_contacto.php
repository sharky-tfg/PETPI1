<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: contacto.php');
    exit;
}

// Recoger datos del formulario
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// Comprobar campos obligatorios
if ($nombre == '' || $email == '' || $asunto == '' || $mensaje == '') {
    header('Location: contacto.php?error=campos_vacios');
    exit;
}

if (strpos($email, '@') == false) {
    header('Location: contacto.php?error=email_invalido');
    exit;
}


$db = Database::getConnection();

$stmt = $db->prepare("
    INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, fecha, ip)
    VALUES (?, ?, ?, ?, ?, NOW(), ?)
");

$stmt->execute([$nombre, $email, $telefono, $asunto, $mensaje, $ip]);

header('Location: contacto.php?success=1');
exit;
?>
