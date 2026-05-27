<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Verifica si el usuario ha iniciado sesión
Auth::check();

if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Conexión a la base de datos
$db = Database::getConnection();

$solicitudes = [];

$consulta = $db->query("
    SELECT s.*, u.nombre as nombre_usuario, u.email as email_usuario
    FROM solicitudes s
    LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_solicitud DESC
");

while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
    $solicitudes[] = $fila;
}

// Prepara la consulta para evitar inyección SQL
for ($i = 0; $i < count($solicitudes); $i++) {
    $id_solicitud = $solicitudes[$i]['id_solicitud'];
    
    $stmt = $db->prepare("SELECT * FROM fotos_solicitudes WHERE solicitud_id = ? ORDER BY es_principal DESC, orden ASC");
    $stmt->execute([$id_solicitud]);
    
    $fotos = [];
    while ($foto = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fotos[] = $foto;
    }
    
    $solicitudes[$i]['fotos'] = $fotos;
    
    if (count($fotos) > 0) {
        $solicitudes[$i]['foto_principal'] = $fotos[0]['nombre_foto'];
    } else {
        $solicitudes[$i]['foto_principal'] = 'perrillono.png';
    }
}

$total_pendientes = count($solicitudes);

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';
?>

<div class="container py-4">
    
    <div class="text-center mb-4">
        <h1 class="text-primary">📋 Solicitudes de Adopción</h1>
        <p class="text-muted">Revisa las personas que quieren adoptar</p>
        
        <div class="d-flex justify-content-center mt-3">
            <div class="card text-center p-3" style="border-top: 4px solid #f59e0b;">
                <div class="fs-1 fw-bold text-dark"><?php echo $total_pendientes; ?></div>
                <div class="text-muted small">PENDIENTES</div>
            </div>
        </div>
    </div>
    
    <?php if ($total_pendientes == 0): ?>
        <div class="text-center py-5 bg-light rounded-4">
            <div class="fs-1">🎉</div>
            <h3 class="mt-2">¡Todo al día!</h3>
            <p class="text-muted">No hay solicitudes pendientes</p>
        </div>
    <?php else: ?>
        
        <div class="row g-4">
            <?php for ($i = 0; $i < $total_pendientes; $i++): 
                $s = $solicitudes[$i];
                $id = $s['id_solicitud'];
                $fotos = $s['fotos'];
                $num_fotos = count($fotos);
            ?>
                <div class="col-12" id="solicitud-<?php echo $id; ?>">
                    <div class="card shadow-sm">
                        
                        <div class="card-header bg-light d-flex justify-content-between align-items-center" 
                             style="cursor: pointer;" 
                             onclick="toggleSolicitud(<?php echo $id; ?>)">
                            <div>
                                <span class="badge bg-primary rounded-pill me-2">
                                <strong><?php echo htmlspecialchars($s['nombre_perro']); ?></strong>
                                <small class="text-muted ms-2"><?php echo htmlspecialchars($s['raza']); ?></small>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small"><?php echo date('d/m/Y', strtotime($s['fecha_solicitud'])); ?></div>
                                <div class="text-muted small"><?php echo date('H:i', strtotime($s['fecha_solicitud'])); ?></div>
                            </div>
                        </div>
                        
                        <div id="content-<?php echo $id; ?>" class="card-body" style="display: none;">
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <?php if ($num_fotos > 0): ?>
                                        <img src="/uploads/<?php echo $s['foto_principal']; ?>" 
                                             class="img-fluid rounded mb-2 w-100" 
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             onclick="abrirZoom('/uploads/<?php echo $s['foto_principal']; ?>')"
                                             onerror="this.src='/img/perrillono.png'">
                                        
                                        <?php if ($num_fotos > 1): ?>
                                            <div class="d-flex gap-2 mt-2">
                                                <?php 
                                                $contador = 0;
                                                for ($j = 0; $j < $num_fotos; $j++) {
                                                    if ($fotos[$j]['nombre_foto'] != $s['foto_principal'] && $contador < 3) {
                                                        echo '<img src="/uploads/' . $fotos[$j]['nombre_foto'] . '" 
                                                              class="rounded" 
                                                              style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                              onclick="abrirZoom(\'/uploads/' . $fotos[$j]['nombre_foto'] . '\')"
                                                              onerror="this.src=\'/img/perrillono.png\'">';
                                                        $contador++;
                                                    }
                                                }
                                                ?>
                                                <?php if ($num_fotos > 4): ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        +<?php echo ($num_fotos - 4); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="bg-light rounded text-center py-4">
                                            <div class="text-muted">📷 Sin fotos</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-8">
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-secondary me-1"><?php echo $s['sexo']; ?></span>
                                        <span class="badge bg-secondary me-1"><?php echo $s['edad']; ?> años</span>
                                        <span class="badge bg-secondary"><?php echo $s['tamano']; ?></span>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded mb-3">
                                        <strong>📝 Descripción</strong>
                                        <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($s['descripcion'])); ?></p>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded mb-3">
                                        <strong>❤️ Motivo</strong>
                                        <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($s['motivo'])); ?></p>
                                    </div>
                                    
                                    <div class="bg-info bg-opacity-10 p-3 rounded mb-3">
                                        <strong>👤 Solicitante:</strong> <?php echo htmlspecialchars($s['nombre_usuario']); ?><br>
                                        <strong>📧 Email:</strong> <?php echo htmlspecialchars($s['email_usuario']); ?>
                                    </div>
                                    
                                    <form action="procesar_solicitud.php" method="post">
                                        <input type="hidden" name="id_solicitud" value="<?php echo $id; ?>">
                                        
                                        <div id="motivo-group-<?php echo $id; ?>" class="mb-3" style="display: none;">
                                            <label class="form-label">✏️ Motivo del rechazo</label>
                                            <textarea name="motivo_rechazo" 
                                                      id="motivo-<?php echo $id; ?>"
                                                      class="form-control" 
                                                      rows="2" 
                                                      placeholder="Escribe aquí por qué rechazas esta solicitud..."></textarea>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="accion" value="aceptar" class="btn btn-success"
                                                    onclick="return confirm('¿Aceptar? El perro se publicará.')">
                                                ✅ Aceptar
                                            </button>
                                            
                                            <button type="button" class="btn btn-danger" onclick="mostrarMotivo(<?php echo $id; ?>)">
                                                ❌ Rechazar
                                            </button>
                                            
                                            <button type="submit" name="accion" value="rechazar" 
                                                    class="btn btn-warning" 
                                                    id="btn-confirmar-<?php echo $id; ?>"
                                                    style="display: none;"
                                                    onclick="return validarMotivo(<?php echo $id; ?>)">
                                                ✓ Confirmar rechazo
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
    <?php endif; ?>
</div>

<!-- MODAL PARA VER FOTOS GRANDES -->
<div id="modalZoom" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="text-end mb-2">
                <button type="button" class="btn-close btn-close-white" onclick="cerrarZoom()"></button>
            </div>
            <img id="imgZoom" class="img-fluid rounded">
        </div>
    </div>
</div>

<script>
// Despliega o esconde el contenido de la solicitud
function toggleSolicitud(id) {
    var contenido = document.getElementById('content-' + id);
    var icono = document.getElementById('icon-' + id);
    
    if (contenido.style.display === 'block') {
        contenido.style.display = 'none';
        icono.innerHTML = '▼';
    } else {
        contenido.style.display = 'block';
        icono.innerHTML = '▲';
    }
}

// Muestra el campo para escribir el motivo del rechazo
function mostrarMotivo(id) {
    document.getElementById('motivo-group-' + id).style.display = 'block';
    document.querySelector('#solicitud-' + id + ' .btn-danger').style.display = 'none';
    document.getElementById('btn-confirmar-' + id).style.display = 'block';
}

// Comprueba que el motivo no esté vacío
function validarMotivo(id) {
    var motivo = document.getElementById('motivo-' + id).value.trim();
    if (motivo === '') {
        alert('❌ Tienes que escribir el motivo para rechazar');
        return false;
    }
    return true;
}

// Abre la foto en grande
function abrirZoom(src) {
    var modal = new bootstrap.Modal(document.getElementById('modalZoom'));
    document.getElementById('imgZoom').src = src;
    modal.show();
}

function cerrarZoom() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalZoom'));
    modal.hide();
}
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
