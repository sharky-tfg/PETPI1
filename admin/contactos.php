<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

Auth::check();

if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = Database::getConnection();

//eliminar mensaje
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $stmt = $db->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->execute([$_GET['eliminar']]);
    header('Location: contactos.php?eliminado=1');
    exit;
}

//obtener mensajes
$consulta = $db->query("SELECT * FROM contactos ORDER BY fecha DESC");
$contactos = $consulta->fetchAll();

$total_mensajes = count($contactos);

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';
?>

<div class="container mt-4">
    
    <h1>Mensajes de contacto</h1>
    <p>Consulta los mensajes de los visitantes</p>
    
    <div class="alert alert-info">
        <strong><?= $total_mensajes ?></strong> mensajes recibidos
    </div>
    
    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success">
            Mensaje eliminado correctamente
        </div>
    <?php endif; ?>
    
    <?php if (empty($contactos)): ?>
        <div class="alert alert-warning">
            <h4>No hay mensajes</h4>
            <p>Cuando alguien contacte, aparecerán aquí</p>
        </div>
    <?php else: ?>
        
        <?php foreach ($contactos as $c): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                    - <?= htmlspecialchars($c['email']) ?>
                    <?php if ($c['telefono']): ?>
                        - Tel: <?= htmlspecialchars($c['telefono']) ?>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($c['asunto']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($c['mensaje'])) ?></p>
                    <p class="text-muted">Fecha: <?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></p>
                    <a href="?eliminar=<?= $c['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Eliminar este mensaje?')">
                        Eliminar
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        
    <?php endif; ?>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
