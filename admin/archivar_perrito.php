<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/models/Perrito.php';

Auth::check();

if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

if (!isset($_POST['id_perro'])) {
    header('Location: /perritos.php');
    exit;
}

$id = $_POST['id_perro'];

Perrito::eliminarPerrito($id, $_SESSION['usuario']['id_usuario']);

header('Location: /perritos.php');
exit;
