<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Database.php';

$db = Database::getConnection();

$id = $_POST['id_solicitud'];
$motivo = $_POST['motivo_rechazo'];

$s = $db->prepare("SELECT * FROM solicitudes WHERE id_solicitud=?");
$s->execute([$id]);
$sol = $s->fetch();

if (!$sol) {
    header('Location: admin_solicitudes.php?error=no_encontrada');
    exit;
}
//GUARDAR EN RECHAZADAS
$insert = $db->prepare("
    INSERT INTO solicitudes_rechazadas 
    (id_solicitud, nombre_perro, motivo_rechazo, id_admin, fecha_rechazo)
    VALUES (?, ?, ?, ?, NOW())
");
$insert->execute([
    $sol['id_solicitud'],
    $sol['nombre_perro'],
    $motivo,
    $_SESSION['usuario']['id_usuario']
]);
//BORRAR DE SOLICITUDES
$borrar = $db->prepare("DELETE FROM solicitudes WHERE id_solicitud=?");
$borrar->execute([$id]);

header('Location: admin_solicitudes.php');
exit;
