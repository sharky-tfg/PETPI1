<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/Perrito.php';
session_start();

//agarro el id de la url
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: perritos.php');
    exit;
}

$perritoModel = new Perrito();
$perro = $perritoModel->obtenerPorId($id);

if (!$perro) {
    header('Location: perritos.php');
    exit;
}

//saco las fotos del perro
$fotos = $perritoModel->getFotos($id);
$fotoPrincipal = $perritoModel->getFotoPrincipal($id);
$tieneMasFotos = $perritoModel->tieneMasFotos($id);

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';
?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'auto_adopcion'): ?>
    <div class="alert alert-warning m-3">No puedes adoptar tu propio perro</div>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 'adoptado'): ?>
    <div class="alert alert-success m-3">¡Felicidades! Has adoptado a <?php echo htmlspecialchars($perro->nombre); ?></div>
<?php endif; ?>

<?php if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1): ?>
    <div class="alert alert-success m-3">Perro eliminado correctamente</div>
<?php endif; ?>

<div class="container py-5">
    <div class="row">
        
        <div class="col-md-6">
            <img src="/uploads/<?php echo htmlspecialchars($fotoPrincipal); ?>"
                 class="img-fluid rounded w-100"
                 style="height: 400px; object-fit: cover; cursor: pointer;"
                 onclick="abrirZoom(this.src)"
                 onerror="this.src='/img/perrillono.png'">
            
            <?php if($tieneMasFotos): ?>
                <div class="row mt-2">
                    <?php for($i = 1; $i < count($fotos) && $i < 4; $i++): ?>
                        <div class="col-3">
                            <img src="/uploads/<?php echo htmlspecialchars($fotos[$i]['nombre_foto']); ?>"
                                 class="img-fluid rounded"
                                 style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                 onclick="abrirZoom(this.src)"
                                 onerror="this.src='/img/perrillono.png'">
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-6">
            
            <h1 class="display-4"><?php echo htmlspecialchars($perro->nombre); ?></h1>
            <p class="text-muted"><?php echo htmlspecialchars($perro->raza); ?> | <?php echo $perro->edad; ?> años</p>
            
            <hr>
            
            <h4>Descripcion</h4>
            <p><?php echo nl2br(htmlspecialchars($perro->descripcion)); ?></p>
            
            <?php if($perro->motivo): ?>
                <h4>Motivo de adopcion</h4>
                <p><?php echo nl2br(htmlspecialchars($perro->motivo)); ?></p>
            <?php endif; ?>
            
            <hr>
            
            <h4>Datos</h4>
            <ul class="list-unstyled">
                <li><strong>Sexo:</strong> <?php echo $perro->sexo; ?></li>
                <li><strong>Tamaño:</strong> <?php echo $perro->tamano; ?></li>
                <li><strong>Edad:</strong> <?php echo $perro->edad; ?> años</li>
            </ul>
            
            <!--si es admin muestro botones de eliminar y editar-->
            <?php if(isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'admin'): ?>
                
                <div class="row g-2">
                    <div class="col-6">
                        <form method="post" action="/eliminar_perro.php" onsubmit="return confirm('¿Seguro que quieres eliminar este perro? Se guardará en el historial.');">
                            <input type="hidden" name="id_perro" value="<?php echo $perro->id_perro; ?>">
                            <input type="hidden" name="motivo" value="Eliminado por administrador">
                            <button type="submit" class="btn btn-danger btn-lg w-100">🗑️ Eliminar</button>
                        </form>
                    </div>
                    <div class="col-6">
                        <a href="/editar_perro.php?id=<?php echo $perro->id_perro; ?>" class="btn btn-warning btn-lg w-100">✏️ Editar</a>
                    </div>
                </div>
            
            <!--si no es admin y esta disponible muestro boton adoptar-->
            <?php elseif($perro->estado == 'Disponible'): ?>
                
                <?php if(!isset($_SESSION['usuario'])): ?>
                    <a href="/login.php" class="btn btn-primary btn-lg w-100">Inicia sesion para adoptar</a>
                <?php elseif($_SESSION['usuario']['id_usuario'] == $perro->id_usuario): ?>
                    <div class="alert alert-info">Este perro fue publicado por ti</div>
                <?php else: ?>
                    <form method="post" action="/adoptar.php">
                        <input type="hidden" name="id_perro" value="<?php echo $perro->id_perro; ?>">
                        <button type="submit" class="btn btn-success btn-lg w-100">¡Quiero adoptarlo!</button>
                    </form>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="alert alert-success text-center">
                    <h4>✅ Este perrito ya fue adoptado</h4>
                    <a href="/perritos.php" class="btn btn-primary mt-2">Ver otros perritos</a>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<!--modal para ver fotos en grande-->
<div id="modalZoom" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; cursor:pointer;" onclick="cerrarZoom()">
    <img id="imgZoom" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); max-width:90%; max-height:90%;">
    <span style="position:absolute; top:20px; right:30px; color:white; font-size:40px;">&times;</span>
</div>

<script>
function abrirZoom(src) {
    document.getElementById('modalZoom').style.display = 'block';
    document.getElementById('imgZoom').src = src;
}
function cerrarZoom() {
    document.getElementById('modalZoom').style.display = 'none';
}
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
