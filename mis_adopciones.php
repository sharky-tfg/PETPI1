<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';

// Comprobar que el usuario ha iniciado sesion
Auth::check();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$db = Database::getConnection();

// Sacar las adopciones del usuario
$stmt = $db->prepare("
    SELECT ah.*, p.nombre as perro_nombre, p.raza, p.id_perro
    FROM adopt_historic ah
    JOIN perritos p ON ah.id_perro = p.id_perro
    WHERE ah.id_usuario = ?
    ORDER BY ah.fecha_adopcion DESC
");
$stmt->execute([$id_usuario]);
$adopciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sacar las fotos principales de cada perro
for ($i = 0; $i < count($adopciones); $i++) {
    $id_perro = $adopciones[$i]['id_perro'];
    
    $stmtFoto = $db->prepare("SELECT nombre_foto FROM fotos_perritos WHERE perrito_id = ? AND es_principal = 1 LIMIT 1");
    $stmtFoto->execute([$id_perro]);
    $foto = $stmtFoto->fetch(PDO::FETCH_ASSOC);
    
    if ($foto) {
        $adopciones[$i]['foto_principal'] = $foto['nombre_foto'];
    } else {
        $adopciones[$i]['foto_principal'] = 'perrillono.png';
    }
}
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Mis Adopciones</h1>
    
    <?php if (empty($adopciones)): ?>
        <div class="text-center py-5">
            <div class="display-1 mb-4">🐾</div>
            <h2 class="text-muted mb-4">No has adoptado ningún perrito todavía</h2>
            <a href="/perritos.php" class="btn btn-primary btn-lg px-5 py-3">
                Buscar perritos
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php for ($i = 0; $i < count($adopciones); $i++): 
                $adopcion = $adopciones[$i];
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow">
                        <img src="/uploads/<?php echo htmlspecialchars($adopcion['foto_principal']); ?>" 
                             class="card-img-top" 
                             style="height: 250px; width: 100%; object-fit: cover;"
                             onerror="this.src='/img/perrillono.png'">
                        
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($adopcion['perro_nombre']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($adopcion['raza']); ?></p>
                            
                            <div class="mt-3">
                                <small class="text-muted">Fecha de adopción</small>
                                <p><strong><?php echo date('d/m/Y', strtotime($adopcion['fecha_adopcion'])); ?></strong></p>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-0 pb-4">
                            <a href="/perrito_detalle.php?id=<?php echo $adopcion['id_perro']; ?>" 
                               class="btn btn-outline-primary w-100">
                                Ver perrito
                            </a>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
