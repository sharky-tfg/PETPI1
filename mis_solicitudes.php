<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';

Auth::check();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getConnection();
$id_usuario = $_SESSION['usuario']['id_usuario'];

// Sacar las solicitudes del usuario
$consulta = $db->prepare("
    SELECT * FROM solicitudes 
    WHERE id_usuario = ? 
    ORDER BY fecha_solicitud DESC
");
$consulta->execute([$id_usuario]);
$solicitudes = $consulta->fetchAll();

// Sacar las solicitudes rechazadas
$consultaRech = $db->prepare("
    SELECT sr.*, s.nombre_perro 
    FROM solicitudes_rechazadas sr
    JOIN solicitudes s ON sr.id_solicitud = s.id_solicitud
    WHERE s.id_usuario = ? 
    ORDER BY sr.fecha_rechazo DESC
");
$consultaRech->execute([$id_usuario]);
$rechazadas = $consultaRech->fetchAll();

// Marcar como leidas
if (count($rechazadas) > 0) {
    $actualizar = $db->prepare("
        UPDATE solicitudes_rechazadas sr
        JOIN solicitudes s ON sr.id_solicitud = s.id_solicitud
        SET sr.leida = 1
        WHERE s.id_usuario = ? AND sr.leida = 0
    ");
    $actualizar->execute([$id_usuario]);
}
?>

<div class="container py-5">
    
    <h1 class="text-center mb-4">Mis Solicitudes de Adopción</h1>
    
    <div class="row">
        
        <!-- Solicitudes activas -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Solicitudes Activas</h4>
                </div>
                <div class="card-body">
                    <?php if (count($solicitudes) == 0): ?>
                        <p class="text-center">No tienes solicitudes activas</p>
                    <?php else: ?>
                        <?php for ($i = 0; $i < count($solicitudes); $i++): 
                            $s = $solicitudes[$i];
                        ?>
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <h5><?php echo htmlspecialchars($s['nombre_perro']); ?></h5>
                                    <?php if ($s['estado'] == 'pendiente'): ?>
                                        <span class="badge bg-warning">Pendiente</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aceptada</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($s['raza']); ?> | <?php echo $s['edad']; ?> años</p>
                                <p class="text-muted small mb-0">Solicitado el <?php echo date('d/m/Y', strtotime($s['fecha_solicitud'])); ?></p>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Solicitudes rechazadas -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Solicitudes Rechazadas</h4>
                </div>
                <div class="card-body">
                    <?php if (count($rechazadas) == 0): ?>
                        <p class="text-center">No tienes solicitudes rechazadas</p>
                    <?php else: ?>
                        <?php for ($i = 0; $i < count($rechazadas); $i++): 
                            $r = $rechazadas[$i];
                        ?>
                            <div class="border rounded p-3 mb-3">
                                <h5><?php echo htmlspecialchars($r['nombre_perro']); ?></h5>
                                <div class="bg-light p-2 rounded mt-2">
                                    <strong>Motivo:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($r['motivo_rechazo'])); ?>
                                </div>
                                <p class="text-muted small mb-0 mt-2">Rechazado el <?php echo date('d/m/Y', strtotime($r['fecha_rechazo'])); ?></p>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="text-center mt-3">
        <a href="/perritos.php" class="btn btn-primary">Volver a ver perritos</a>
    </div>
    
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
