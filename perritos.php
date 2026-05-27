<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/Perrito.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';

// Sacar todos los perritos
$perritos = Perrito::obtenerTodos();

// Separar disponibles y adoptados
$disponibles = [];
$adoptados = [];

for ($i = 0; $i < count($perritos); $i++) {
    if ($perritos[$i]->estado == 'Disponible') {
        $disponibles[] = $perritos[$i];
    } else {
        $adoptados[] = $perritos[$i];
    }
}

$logueado = isset($_SESSION['usuario']);
$esUsuario = $logueado && $_SESSION['usuario']['rol'] == 'usuario';
?>

<div class="container py-5">

    <!-- Header bonito -->
    <div class="text-center mb-5">
        <div class="mb-3">
            <span class="badge bg-primary px-3 py-2 rounded-pill">🐾 Adopta, no compres</span>
        </div>
        <h1 class="display-4 fw-bold mb-3">Nuestros <span style="color: #0d6efd;">Perritos</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            Encuentra a tu próximo mejor amigo y cambia una vida para siempre.
        </p>
        
        <?php if (!$logueado): ?>
        <div class="row justify-content-center mt-5 pt-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-7 p-5">
                            <h3 class="fw-bold mb-3">¡Únete a nuestra familia!</h3>
                            <p class="text-muted mb-4">Regístrate gratis para adoptar, dar en adopción o ver todas las fotos de los perritos.</p>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="/registro.php" class="btn btn-primary rounded-pill px-4 py-2">
                                    <i class="bi bi-person-plus"></i> Crear cuenta
                                </a>
                                <a href="/login.php" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                    <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                                </a>
                            </div>
                        </div>
                     
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>

    <?php if (count($perritos) == 0): ?>
        <div class="text-center py-5">
            <div class="display-1 mb-3">🐶</div>
            <h3 class="fw-bold">No hay perritos disponibles</h3>
            <p class="text-muted">¡Vuelve pronto!</p>
        </div>
    <?php else: ?>

        <!-- Perritos disponibles -->
        <?php if (count($disponibles) > 0): ?>
            <div class="d-flex align-items-center gap-3 mb-4">
                <h2 class="fw-bold mb-0" style="color: #10b981;">🐾 Buscan un hogar</h2>
                <span class="badge bg-success rounded-pill px-3 py-2"><?php echo count($disponibles); ?> disponibles</span>
                <div class="flex-grow-1" style="height: 2px; background: linear-gradient(90deg, #10b981, transparent);"></div>
            </div>
            
            <div class="row g-4">
                <?php for ($i = 0; $i < count($disponibles); $i++): 
                    $perro = $disponibles[$i];
                    $perritoModel = new Perrito();
                    $fotoPrincipal = $perritoModel->getFotoPrincipal($perro->id_perro);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card perro-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="position-relative">
                                <img src="/uploads/<?php echo htmlspecialchars($fotoPrincipal); ?>"
                                     class="card-img-top"
                                     style="height: 220px; width: 100%; object-fit: cover;"
                                     onerror="this.src='/img/perrillono.png'">
                                <?php if (!$logueado): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-secondary bg-opacity-75 py-2 px-3 rounded-pill">
                                            <i class="bi bi-lock-fill"></i>  fotos
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="card-title fw-bold fs-4 mb-0"><?php echo htmlspecialchars($perro->nombre); ?></h3>
                                    <div class="d-flex gap-1">
                                        <span class="badge bg-light text-dark rounded-pill">
                                            <i class="bi bi-calendar"></i> <?php echo $perro->edad; ?> años
                                        </span>
                                    </div>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($perro->raza); ?>
                                </p>
                                <p class="small text-muted mb-3">
                                    <?php echo htmlspecialchars(substr($perro->descripcion, 0, 80)); ?>...
                                </p>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="perrito_detalle.php?id=<?php echo $perro->id_perro; ?>" class="btn btn-outline-primary rounded-pill flex-grow-1">
                                        <i class="bi bi-eye"></i> Ver detalles
                                    </a>
                                    <?php if ($logueado && $esUsuario && $perro->id_usuario != $_SESSION['usuario']['id_usuario']): ?>
                                        <form method="post" action="/adoptar.php" class="flex-grow-1">
                                            <input type="hidden" name="id_perro" value="<?php echo $perro->id_perro; ?>">
                                            <button type="submit" class="btn btn-success rounded-pill w-100" onclick="return confirm('¿Quieres adoptar a <?php echo htmlspecialchars($perro->nombre); ?>?')">
                                                <i class="bi bi-heart-fill"></i> Adoptar
                                            </button>
                                        </form>
                                    <?php elseif ($logueado && $esUsuario && $perro->id_usuario == $_SESSION['usuario']['id_usuario']): ?>
                                        <button class="btn btn-secondary rounded-pill flex-grow-1" disabled>
                                            <i class="bi bi-person"></i> Tu perro
                                        </button>
                                    <?php elseif (!$logueado): ?>
                                        <a href="/login.php" class="btn btn-success rounded-pill flex-grow-1">
                                            <i class="bi bi-heart-fill"></i> Adoptar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- Perritos adoptados -->
        <?php if (count($adoptados) > 0): ?>
            <div class="d-flex align-items-center gap-3 mb-4 mt-5 pt-3">
                <h2 class="fw-bold mb-0" style="color: #64748b;">❤️ Ya encontraron familia</h2>
                <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo count($adoptados); ?> adoptados</span>
                <div class="flex-grow-1" style="height: 2px; background: linear-gradient(90deg, #64748b, transparent);"></div>
            </div>
            
            <div class="row g-4">
                <?php for ($i = 0; $i < count($adoptados); $i++): 
                    $perro = $adoptados[$i];
                    $perritoModel = new Perrito();
                    $fotoPrincipal = $perritoModel->getFotoPrincipal($perro->id_perro);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card perro-card-adoptado h-100 border-0 shadow-sm rounded-4 overflow-hidden" style="filter: grayscale(0.3); opacity: 0.85;">
                            <div class="position-relative">
                                <img src="/uploads/<?php echo htmlspecialchars($fotoPrincipal); ?>"
                                     class="card-img-top"
                                     style="height: 220px; width: 100%; object-fit: cover;"
                                     onerror="this.src='/img/perrillono.png'">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-danger rounded-pill py-2 px-3 shadow">
                                        <i class="bi bi-heart-fill me-1"></i> Adoptado
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h3 class="card-title fw-bold fs-4 mb-2"><?php echo htmlspecialchars($perro->nombre); ?></h3>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($perro->raza); ?>
                                </p>
                                <div class="mt-3 pt-2">
                                    <a href="perrito_detalle.php?id=<?php echo $perro->id_perro; ?>" class="btn btn-outline-secondary rounded-pill w-100">
                                        <i class="bi bi-eye"></i> Ver historia
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

   

</div>


<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
