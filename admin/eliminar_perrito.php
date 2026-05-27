<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/models/Perrito.php';

Auth::check();

if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

if (!isset($_POST['id_perro'], $_POST['motivo'])) {
    header('Location: /perritos.php');
    exit;
}

$id = $_POST['id_perro'];
$motivo = trim($_POST['motivo']);

if (empty($motivo)) {
    die("El motivo es obligatorio.");
}

Perrito::eliminar($id, $_SESSION['usuario']['id_usuario'], $motivo);

header('Location: /perritos.php');
exit;
