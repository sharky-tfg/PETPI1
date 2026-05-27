<?php
// Cargar lo necesario
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Comprobar que el usuario ha iniciado sesion
Auth::check();

// Solo administradores pueden eliminar
if ($_SESSION['usuario']['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

$db = Database::getConnection();

// Recoger el id del perro
$id = $_POST['id_perro'] ?? null;

if (!$id) {
    header("Location: perritos.php");
    exit;
}

// Buscar el perro en la base de datos
$consulta = $db->prepare("SELECT * FROM perritos WHERE id_perro = ?");
$consulta->execute([$id]);
$perro = $consulta->fetch(PDO::FETCH_ASSOC);

// Si no existe, volver
if (!$perro) {
    header("Location: perritos.php");
    exit;
}

// Si el perro estaba adoptado, lo guardo en el historial
if ($perro['estado'] == 'Adoptado') {

    $guardar = $db->prepare("
        INSERT INTO perritos_eliminados
        (nombre, raza, edad, sexo, tamano, descripcion, imagen, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $guardar->execute([
        $perro['nombre'],
        $perro['raza'],
        $perro['edad'],
        $perro['sexo'],
        $perro['tamano'],
        $perro['descripcion'],
        $perro['imagen'],
        $perro['estado']
    ]);
}

// Eliminar el perro de la tabla principal
$borrar = $db->prepare("DELETE FROM perritos WHERE id_perro = ?");
$borrar->execute([$id]);

// Volver a la lista de perritos
header("Location: perritos.php");
exit;
?>
