<li class="nav-item dropdown">
    <a class="nav-link btn-noti dropdown-toggle" href="#" data-bs-toggle="dropdown">
        🔔 Notificaciones
        <?php if($total > 0): ?>
            <span class="badge-noti"><?= $total ?></span>
        <?php endif; ?>
    </a>
    
    <ul class="dropdown-menu dropdown-noti">
        <li class="dropdown-header text-center fw-bold py-2">📢 Notificaciones</li>
        
        <?php if($total == 0): ?>
            <li class="dropdown-item text-center text-muted">No hay notificaciones</li>
        <?php else: ?>
            
            <?php foreach($solicitudes as $s): ?>
                <li class="noti-item">
                    <div class="d-flex align-items-center gap-2">
                        <div class="noti-icono <?= $s['estado'] == 'pendiente' ? 'icono-pendiente' : 'icono-aceptada' ?>">
                            <?= $s['estado'] == 'pendiente' ? '⏳' : '✅' ?>
                        </div>
                        <div class="flex-grow-1">
                            <strong>🐶 <?= htmlspecialchars($s['nombre_perro']) ?></strong>
                            <span class="badge <?= $s['estado'] == 'pendiente' ? 'badge-pendiente' : 'badge-aceptada' ?> ms-2">
                                <?= $s['estado'] == 'pendiente' ? 'Pendiente' : 'Aceptada' ?>
                            </span>
                            <div><small><?= date('d/m/Y', strtotime($s['fecha_solicitud'])) ?></small></div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            
            <?php foreach($rechazadas as $r): ?>
                <li class="noti-item">
                    <div class="d-flex align-items-center gap-2">
                        <div class="noti-icono icono-rechazada">❌</div>
                        <div class="flex-grow-1">
                            <strong>🐶 <?= htmlspecialchars($r['nombre_perro']) ?></strong>
                            <span class="badge-rechazada ms-2">Rechazada</span>
                            <div class="motivo-caja mt-1">
                                <small>Motivo: <?= htmlspecialchars($r['motivo_rechazo']) ?></small>
                            </div>
                            <div><small><?= date('d/m/Y', strtotime($r['fecha_rechazo'])) ?></small></div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            
        <?php endif; ?>
        
        <?php if($total > 0): ?>
            <li class="dropdown-item text-center py-2">
                <a href="/mis_solicitudes.php" class="text-decoration-none">📋 Ver todas las solicitudes</a>
            </li>
        <?php endif; ?>
    </ul>
</li>
