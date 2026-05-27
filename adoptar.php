<?php
// Iniciar sesion
session_start();

// Cargar lo necesario
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Comprobar que el usuario ha iniciado sesion
Auth::check();

// Solo los usuarios normales pueden adoptar
if ($_SESSION['usuario']['rol'] != 'usuario') {
    header('Location: index.php');
    exit;
}

// Comprobar que llega el id del perro
if (!isset($_POST['id_perro'])) {
    header('Location: perritos.php');
    exit;
}

$id_perro = $_POST['id_perro'];
$id_usuario = $_SESSION['usuario']['id_usuario'];

$db = Database::getConnection();

$consulta = $db->prepare("SELECT * FROM perritos WHERE id_perro = ? AND estado = 'Disponible'");
$consulta->execute([$id_perro]);
$perro = $consulta->fetch(PDO::FETCH_ASSOC);

// Si no existe o no esta disponible, volver
if (!$perro) {
    header('Location: perritos.php?error=perro_no_disponible');
    exit;
}

// No se puede adoptar el propio perro
if ($perro['id_usuario'] == $id_usuario) {
    header('Location: perrito_detalle.php?id=' . $id_perro . '&error=auto_adopcion');
    exit;
}

// Cambiar el estado del perro a Adoptado
$actualizar = $db->prepare("UPDATE perritos SET estado = 'Adoptado' WHERE id_perro = ?");
$actualizar->execute([$id_perro]);

$historial = $db->prepare("
    INSERT INTO adopt_historic (id_perro, id_usuario, nombre_usuario, fecha_adopcion)
    VALUES (?, ?, ?, NOW())
");
$historial->execute([$id_perro, $id_usuario, $_SESSION['usuario']['nombre']]);

header('Location: adopcion_exitosa.php?id=' . $id_perro);
exit;
?>
