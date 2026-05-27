<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/models/Perrito.php';

$db = Database::getConnection();

$id = $_POST['id_solicitud'];

$s = $db->prepare("SELECT * FROM solicitudes WHERE id_solicitud=?");
$s->execute([$id]);
$sol = $s->fetch();

if (!$sol) {
    header('Location: admin_solicitudes.php?error=no_encontrada');
    exit;
}

/* CREAR PERRITO CON IMAGEN */
Perrito::crearDesdeSolicitud($sol, $_SESSION['usuario']['id_usuario']);

/* GUARDAR EN ACEPTADAS */
$insert = $db->prepare("
    INSERT INTO solicitudes_aceptadas (id_solicitud, nombre_perro, id_admin, fecha_aceptacion)
    VALUES (?, ?, ?, NOW())
");
$insert->execute([
    $sol['id_solicitud'],
    $sol['nombre_perro'],
    $_SESSION['usuario']['id_usuario']
]);

/* BORRAR DE SOLICITUDES */
$borrar = $db->prepare("DELETE FROM solicitudes WHERE id_solicitud=?");
$borrar->execute([$id]);

header('Location: admin_solicitudes.php');
exit;
