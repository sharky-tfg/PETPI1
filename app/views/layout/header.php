<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PetAdopta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-premium">
  <div class="container-fluid px-5">

    <a class="navbar-brand d-flex align-items-center gap-3" href="/index.php">
        <div class="logo-image-container">
            <img src="/img/logo-petadopta.png" 
                 alt="PetAdopta" 
                 class="logo-image"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-image-placeholder" style="display: none;"></div>
        </div>
        <div class="logo-text">
            <span class="brand-name">PetAdopta</span>
        </div>
    </a>

    <button class="navbar-toggler border-0 shadow-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarPremium">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between mt-4 mt-lg-0" id="navbarPremium">

        <ul class="navbar-nav mx-auto gap-lg-4 text-center">

            <li class="nav-item">
                <a class="nav-link nav-link-premium" href="/index.php">
                    <i class="bi bi-house-door me-1"></i> Inicio
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link nav-link-premium" href="/perritos.php">
                    <i class="bi bi-heart me-1"></i> Perritos
                </a>
            </li>
            
            <?php if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-premium" href="/contacto.php">
                        <i class="bi bi-envelope me-1"></i> Contacto
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-premium" href="/conocenos.php">
                        <i class="bi bi-emoji-smile me-1"></i> Conócenos
                    </a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'usuario'): ?>
                <li class="nav-item">
                    <a class="btn btn-adopt-premium ms-lg-3 mt-3 mt-lg-0" href="/dar_en_adopcion.php">
                        <i class="bi bi-heart-fill me-1"></i>
                        Dar en adopción
                    </a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="btn btn-admin-premium ms-lg-3 mt-3 mt-lg-0" href="/admin_solicitudes.php">
                        <i class="bi bi-clipboard-data me-1"></i> Solicitudes
                    </a>
                </li>
              
                <li class="nav-item">
                    <a class="btn btn-primary ms-lg-3 mt-3 mt-lg-0" href="/admin/contactos.php">
                        <i class="bi bi-envelope-fill me-1"></i> Mensajes
                    </a>
                </li>
            <?php endif; ?>

        </ul>

        <ul class="navbar-nav align-items-center text-center">

            <?php if (isset($_SESSION['usuario'])): ?>
                
                <?php if ($_SESSION['usuario']['rol'] === 'usuario'): ?>
                    <?php 
                    $db = Database::getConnection();
                    $id_usuario = $_SESSION['usuario']['id_usuario'];
                    
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM solicitudes WHERE id_usuario = ? AND estado IN ('pendiente', 'aceptada')");
                    $stmt->execute([$id_usuario]);
                    $activas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as total 
                        FROM solicitudes_rechazadas sr
                        JOIN solicitudes s ON sr.id_solicitud = s.id_solicitud
                        WHERE s.id_usuario = ?
                    ");
                    $stmt->execute([$id_usuario]);
                    $rechazadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    $total_notificaciones = $activas + $rechazadas;
                    ?>
                    
                    <li class="nav-item mx-2">
                        <a class="btn btn-notificaciones position-relative" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill me-1"></i>
                            <span class="d-none d-md-inline">Notificaciones</span>
                            <?php if ($total_notificaciones > 0): ?>
                                <span class="badge-notificacion animate__animated animate__pulse"><?= $total_notificaciones ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-end notificaciones-dropdown shadow-lg border-0 rounded-4 p-0" style="width: 350px;">
                            <div class="dropdown-header bg-primary text-white rounded-top-4 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-bell-fill fs-5"></i>
                                <h6 class="mb-0 fw-bold flex-grow-1">Notificaciones</h6>
                                <?php if ($total_notificaciones > 0): ?>
                                    <span class="badge bg-light text-primary"><?= $total_notificaciones ?> nuevas</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="notificaciones-container" style="max-height: 400px; overflow-y: auto;">
                                <?php if ($total_notificaciones == 0): ?>
                                    <div class="text-center py-5 px-3">
                                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                            <i class="bi bi-check-circle fs-1 text-success"></i>
                                        </div>
                                        <p class="mt-2 text-muted fw-bold">¡Todo al día!</p>
                                        <p class="text-muted small">No tienes notificaciones pendientes</p>
                                    </div>
                                <?php else: ?>
                                    <?php
                                    $stmt = $db->prepare("
                                        SELECT * FROM solicitudes 
                                        WHERE id_usuario = ? AND estado IN ('pendiente', 'aceptada')
                                        ORDER BY fecha_solicitud DESC LIMIT 5
                                    ");
                                    $stmt->execute([$id_usuario]);
                                    $activas_list = $stmt->fetchAll();
                                    
                                    $stmt = $db->prepare("
                                        SELECT sr.*, s.nombre_perro 
                                        FROM solicitudes_rechazadas sr
                                        JOIN solicitudes s ON sr.id_solicitud = s.id_solicitud
                                        WHERE s.id_usuario = ? 
                                        ORDER BY sr.fecha_rechazo DESC LIMIT 5
                                    ");
                                    $stmt->execute([$id_usuario]);
                                    $rechazadas_list = $stmt->fetchAll();
                                    ?>
                                    
                                    <?php foreach ($activas_list as $item): ?>
                                        <div class="dropdown-item border-bottom p-3">
                                            <div class="d-flex gap-3">
                                                <div class="notificacion-icon">
                                                    <?php if ($item['estado'] == 'pendiente'): ?>
                                                        <div class="icono-pendiente"><i class="bi bi-hourglass-split"></i></div>
                                                    <?php else: ?>
                                                        <div class="icono-aceptada"><i class="bi bi-check-lg"></i></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">🐶 <?= htmlspecialchars($item['nombre_perro']) ?></h6>
                                                    <p class="mb-1 small">
                                                        <?php if ($item['estado'] == 'pendiente'): ?>
                                                            <span class="badge-pendiente">⏳ Pendiente</span>
                                                        <?php else: ?>
                                                            <span class="badge-aceptada">✅ Aceptada</span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="text-muted small mb-0">
                                                        <?= htmlspecialchars($item['raza']) ?> · <?= $item['edad'] ?> años
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php foreach ($rechazadas_list as $item): ?>
                                        <div class="dropdown-item border-bottom p-3">
                                            <div class="d-flex gap-3">
                                                <div class="notificacion-icon">
                                                    <div class="icono-rechazada"><i class="bi bi-x-lg"></i></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">🐶 <?= htmlspecialchars($item['nombre_perro']) ?></h6>
                                                    <span class="badge-rechazada mb-2">❌ Rechazada</span>
                                                    <div class="motivo-box small">
                                                        <i class="bi bi-chat-left-text me-1"></i>
                                                        <?= htmlspecialchars($item['motivo_rechazo']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($total_notificaciones > 0): ?>
                                <div class="dropdown-footer bg-light p-3 text-center rounded-bottom-4 border-top">
                                    <a href="/mis_solicitudes.php" class="btn btn-primary w-100 py-2">
                                        <i class="bi bi-eye me-1"></i> Ver todas mis solicitudes
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['usuario']['rol'] === 'usuario'): ?>
                    <li class="nav-item dropdown ms-2">
                        <a class="btn btn-user-premium" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 240px;">
                            <li class="px-3 py-2 text-center border-bottom">
                                <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-2" style="width: 60px; height: 60px;">
                                    <i class="bi bi-person-circle fs-2"></i>
                                </div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h6>
                                <small class="text-muted">Usuario</small>
                            </li>
                            
                            <li>
                                <a class="dropdown-item rounded-3 py-3 my-1" href="/mis_adopciones.php" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle p-2 me-3" style="width: 35px; height: 35px;">
                                            <i class="bi bi-heart-fill"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block">Mis Adopciones</strong>
                                            <small class="text-success">Perros que has adoptado</small>
                                        </div>
                                        <i class="bi bi-chevron-right ms-auto"></i>
                                    </div>
                                </a>
                            </li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <li>
                                <a class="dropdown-item text-danger rounded-3 py-2" href="/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                    <li class="nav-item dropdown ms-2">
                        <a class="btn btn-user-premium" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-lock me-1"></i>
                            <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 200px;">
                            <li class="px-3 py-2 text-center">
                                <div class="bg-primary text-white rounded-circle d-inline-flex p-2 mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-lock fs-3"></i>
                                </div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h6>
                                <small class="badge bg-danger mt-1">Administrador</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger rounded-3 py-2 text-center" href="/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

            <?php else: ?>
                <li class="nav-item">
                    <a class="btn btn-login-premium mt-3 mt-lg-0" href="/login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-light mt-3 mt-lg-0" href="/registro.php">
                        <i class="bi bi-person-plus me-1"></i> Registrarse
                    </a>
                </li>
            <?php endif; ?>

        </ul>

    </div>
  </div>
</nav>

<main class="flex-grow-1">
