<?php
$perritoModel = new Perrito();
$tieneMasFotos = $perritoModel->tieneMasFotos($perro->id_perro);
$fotosExtra = $perritoModel->contarFotosExtra($perro->id_perro);
$fotoPrincipal = $perritoModel->getFotoPrincipal($perro->id_perro);
$estado = strtolower($perro->estado);
$logueado = isset($_SESSION['usuario']);
$esPropietario = $logueado && ($_SESSION['usuario']['id_usuario'] == $perro->id_usuario);
?>

<div class="col-lg-4 col-md-6 mb-4">
    <div class="card perro-card-modern h-100 border-0" data-id="<?= $perro->id_perro ?>">

        
        <div class="perro-img-wrapper" style="position: relative;">
            <img src="/uploads/<?= htmlspecialchars($fotoPrincipal) ?>"
                 class="card-img-top"
                 alt="<?= htmlspecialchars($perro->nombre) ?>"
                 onclick="window.location='perrito_detalle.php?id=<?= $perro->id_perro ?>'"
                 style="cursor: pointer;"
                 onerror="this.src='/img/perrillono.png'">
            
            
            <?php if($tieneMasFotos): ?>
                <div class="indicador-fotos"
                     onclick="verMasFotos(<?= $perro->id_perro ?>)"
                     style="position: absolute; bottom: 10px; right: 10px; z-index: 10; cursor: pointer;">
                    <span class="icono-fotos">📸</span>
                    +<?= $fotosExtra ?> <?= $fotosExtra == 1 ? 'foto' : 'fotos' ?>
                    
                    <?php if(!$logueado): ?>
                        <span class="tooltip">🔒 Inicia sesión para verlas</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <span class="estado-badge-modern <?= $estado === 'disponible' ? 'badge-disponible' : 'badge-adoptado' ?>">
                <?= ucfirst($estado) ?>
            </span>
        </div>

       
        <div class="card-body d-flex flex-column p-4">

            <h5 class="fw-bold perro-nombre" onclick="window.location='perrito_detalle.php?id=<?= $perro->id_perro ?>'" style="cursor: pointer;">
                <?= htmlspecialchars($perro->nombre) ?>
            </h5>

            <p class="text-muted small mb-3">
                <?= htmlspecialchars($perro->raza) ?>
            </p>

            <div class="perro-meta mb-3">
                <span>📅 <?= htmlspecialchars($perro->edad) ?> años</span>
                <span>⚧ <?= htmlspecialchars($perro->sexo) ?></span>
                <span>📏 <?= htmlspecialchars($perro->tamano) ?></span>
            </div>

            <p class="descripcion-perro flex-grow-1">
                <?= htmlspecialchars(substr($perro->descripcion, 0, 110)) ?>...
            </p>

          
            <?php if ($logueado): ?>
                
                <?php if ($_SESSION['usuario']['rol'] === 'usuario' && $estado === 'disponible'): ?>
                    
                    <?php if ($esPropietario): ?>
                        <!-- Es su propio perro - NO puede adoptar -->
                        <div class="mt-3 text-center">
                            <span class="badge bg-secondary text-white p-2 w-100">
                                <i class="bi bi-info-circle"></i> Es tu perro
                            </span>
                        </div>
                    <?php else: ?>
                       
                        <form method="post" action="/adoptar.php" class="mt-3">
                            <input type="hidden" name="id_perro" value="<?= $perro->id_perro ?>">
                            <button type="submit" class="btn btn-adopt w-100">
                                🐾 Adoptar
                            </button>
                        </form>
                    <?php endif; ?>
                    
                <?php endif; ?>

              
                <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                    <div class="mt-3 d-flex gap-2">
                        <a href="/editar_perro.php?id=<?= $perro->id_perro ?>"
                           class="btn btn-edit w-50">
                            ✏ Editar
                        </a>

                        <form method="post"
                              action="/eliminar_perro.php"
                              class="w-50"
                              onsubmit="return confirm('¿Seguro que quieres eliminar este perro?');">
                            <input type="hidden" name="id_perro" value="<?= $perro->id_perro ?>">
                            
                            <?php if ($estado === 'adoptado'): ?>
                                <button type="submit" class="btn btn-archive w-100">📦 Archivar</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-delete w-100">🗑 Eliminar</button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endif; ?>

            <?php else: ?>
             
                <div class="mt-3 text-center">
                    <span class="badge bg-warning text-dark p-2 w-100">
                        🔒 Inicia sesión para adoptar
                    </span>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>
