<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Solo administradores
Auth::check();

if ($_SESSION['usuario']['rol'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Comprobar que llegan los datos
if (!isset($_POST['id_solicitud']) || !isset($_POST['accion'])) {
    header('Location: admin_solicitudes.php');
    exit;
}

$id_solicitud = $_POST['id_solicitud'];
$accion = $_POST['accion'];
$motivo_rechazo = $_POST['motivo_rechazo'] ?? '';

$db = Database::getConnection();

// Buscar la solicitud
$stmt = $db->prepare("SELECT * FROM solicitudes WHERE id_solicitud = ?");
$stmt->execute([$id_solicitud]);
$solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitud) {
    header('Location: admin_solicitudes.php?error=no_encontrada');
    exit;
}

if ($accion == 'aceptar') {
    
    // Guardar en perritos
    $stmt = $db->prepare("
        INSERT INTO perritos 
        (nombre, raza, edad, sexo, tamano, descripcion, motivo, id_usuario, estado, fecha_publicacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Disponible', NOW())
    ");
    
    $stmt->execute([
        $solicitud['nombre_perro'],
        $solicitud['raza'],
        $solicitud['edad'],
        $solicitud['sexo'],
        $solicitud['tamano'],
        $solicitud['descripcion'],
        $solicitud['motivo'],
        $solicitud['id_usuario']
    ]);
    
    $perro_id = $db->lastInsertId();
    
    // Mover las fotos
    $stmt = $db->prepare("SELECT * FROM fotos_solicitudes WHERE solicitud_id = ?");
    $stmt->execute([$id_solicitud]);
    $fotos = $stmt->fetchAll();
    
    for ($i = 0; $i < count($fotos); $i++) {
        $stmt = $db->prepare("
            INSERT INTO fotos_perritos (perrito_id, nombre_foto, es_principal, orden)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $perro_id,
            $fotos[$i]['nombre_foto'],
            $fotos[$i]['es_principal'],
            $fotos[$i]['orden']
        ]);
    }
    
    // Cambiar estado de la solicitud
    $stmt = $db->prepare("UPDATE solicitudes SET estado = 'aceptada' WHERE id_solicitud = ?");
    $stmt->execute([$id_solicitud]);
    
    header('Location: admin_solicitudes.php?success=aceptada');
    exit;
}

elseif ($accion == 'rechazar') {
    
    // Validar motivo
    if ($motivo_rechazo == '') {
        header('Location: admin_solicitudes.php?error=motivo_requerido');
        exit;
    }
    
    // Actualizar solicitud
    $stmt = $db->prepare("
        UPDATE solicitudes 
        SET estado = 'rechazada', motivo_rechazo = ?
        WHERE id_solicitud = ?
    ");
    $stmt->execute([$motivo_rechazo, $id_solicitud]);
    
    // Guardar en el historial de rechazos
    $id_admin = $_SESSION['usuario']['id_usuario'];
    
    $stmt = $db->prepare("
        INSERT INTO solicitudes_rechazadas 
        (id_solicitud, nombre_perro, motivo_rechazo, id_admin, fecha_rechazo)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $id_solicitud,
        $solicitud['nombre_perro'],
        $motivo_rechazo,
        $id_admin
    ]);
    
    header('Location: admin_solicitudes.php?success=rechazada');
    exit;
}

else {
    header('Location: admin_solicitudes.php');
    exit;
}
?>
