<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/core/Database.php';

Auth::check();
if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = Database::getConnection();
$solicitudes = $db->query("SELECT * FROM solicitudes ORDER BY fecha_solicitud DESC")
                  ->fetchAll(PDO::FETCH_ASSOC);

require_once $_SERVER['DOCUMENT_ROOT'].'/app/views/layout/header.php';
?>

<h2>Solicitudes de adopción 🐾</h2>

<?php if (empty($solicitudes)): ?>
    <p>No hay solicitudes pendientes.</p>
<?php else: ?>
    <?php foreach ($solicitudes as $s): ?>
        <div class="solicitud-card">
            <h3><?= htmlspecialchars($s['nombre_perro']) ?></h3>
            <p><strong>Raza:</strong> <?= $s['raza'] ?></p>
            <p><strong>Edad:</strong> <?= $s['edad'] ?></p>
            <p><strong>Sexo:</strong> <?= $s['sexo'] ?></p>
            <p><strong>Tamaño:</strong> <?= $s['tamano'] ?></p>
            <p><strong>Descripción:</strong> <?= $s['descripcion'] ?></p>
            <p><strong>Usuario:</strong> <?= $s['nombre_usuario'] ?></p>
            <p><strong>Motivo:</strong> <?= $s['motivo'] ?></p>

            <form method="post" action="procesar_solicitud.php">
                <input type="hidden" name="id_solicitud" value="<?= $s['id_solicitud'] ?>">
                <button name="accion" value="aceptar">✅ Aceptar</button>
                <button name="accion" value="rechazar">❌ Rechazar</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/app/views/layout/footer.php'; ?>
