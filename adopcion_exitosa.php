<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/Perrito.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';

// Recoger el id del perro desde la URL
$id = $_GET['id'] ?? 0;

// Si no hay id, devolver a la lista de perritos
if (!$id) {
    header('Location: perritos.php');
    exit;
}

// Crear objeto para usar los metodos de la base de datos
$perrito = new Perrito();

// Sacar los datos del perro y sus fotos
$perro = $perrito->obtenerPorId($id);
$listaFotos = $perrito->getFotos($id);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                
                <!-- Cabecera de exito -->
                <div class="bg-primary text-white p-5 text-center">
                    <div class="display-1 mb-3">🐾❤️</div>
                    <h1 class="display-4 fw-bold mb-3">¡Felicidades!</h1>
                    <p class="lead mb-2">Has adoptado a</p>
                    <h2 class="display-3 fw-bold text-warning"><?php echo htmlspecialchars($perro->nombre); ?></h2>
                </div>
                
                <!-- Fotos del perrito -->
                <div class="card-body p-4">
                    
                    <!-- Carrusel de fotos -->
                    <?php if (!empty($listaFotos)): ?>
                        <div id="carouselAdopcion" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-3 shadow">
                                <?php
                                $primera = true;
                                foreach ($listaFotos as $foto):
                                ?>
                                    <div class="carousel-item <?php echo $primera ? 'active' : ''; ?>">
                                        <img src="/uploads/<?php echo htmlspecialchars($foto['nombre_foto']); ?>" 
                                             class="d-block w-100" 
                                             style="height: 350px; object-fit: cover;"
                                             onerror="this.src='/img/perrillono.png'">
                                    </div>
                                <?php
                                    $primera = false;
                                endforeach;
                                ?>
                            </div>
                            
                            <!-- Botones para cambiar de foto (solo si hay mas de una) -->
                            <?php if (count($listaFotos) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselAdopcion" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselAdopcion" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center mb-4">
                            <img src="/img/perrillono.png" 
                                 class="rounded-3 shadow" 
                                 style="max-height: 250px;"
                                 onerror="this.src='/img/perrillono.png'">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Datos del perrito -->
                    <div class="text-center">
                        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($perro->raza); ?></span>
                            <span class="badge bg-info"><?php echo $perro->edad; ?> años</span>
                            <span class="badge bg-primary"><?php echo $perro->sexo; ?></span>
                            <span class="badge bg-warning"><?php echo $perro->tamano; ?></span>
                        </div>
                        
                        <p class="lead mb-4">
                            ¡Enhorabuena! <strong><?php echo htmlspecialchars($perro->nombre); ?></strong> ya forma parte de tu familia.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-4">
                            <p class="mb-0 fst-italic">
                                "<?php echo nl2br(htmlspecialchars($perro->descripcion)); ?>"
                            </p>
                        </div>
                    </div>
                    
                    <!-- Botones para seguir navegando -->
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="/perritos.php" class="btn btn-primary px-4 py-2 rounded-pill">
                            <i class="bi bi-arrow-left"></i> Seguir viendo
                        </a>
                        
                        <a href="/mis_adopciones.php" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                            <i class="bi bi-heart-fill"></i> Mis adopciones
                        </a>
                    </div>
                    
                </div>
                
                <!-- Footer -->
                <div class="card-footer bg-light p-3 text-center">
                    <p class="mb-0 text-muted small">
                        Gracias por adoptar, no comprar
                    </p>
                </div>
                
            </div>
            
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
