<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Comprobar que el usuario ha iniciado sesion
Auth::check();

// Solo usuarios normales pueden dar perros en adopcion
if ($_SESSION['usuario']['rol'] != 'usuario') {
    header('Location: index.php');
    exit;
}

$db = Database::getConnection();
$errores = [];

// Procesar el formulario cuando se envia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validar que hay fotos
    if (empty($_FILES['imagenes']['name'][0])) {
        $errores[] = "Tienes que subir al menos una foto";
    }
    
    // Validar campos obligatorios
    if (empty($_POST['nombre'])) $errores[] = "El nombre es obligatorio";
    if (empty($_POST['raza'])) $errores[] = "La raza es obligatoria";
    if (empty($_POST['edad'])) $errores[] = "La edad es obligatoria";
    if (empty($_POST['sexo'])) $errores[] = "El sexo es obligatorio";
    if (empty($_POST['tamano'])) $errores[] = "El tamaño es obligatorio";
    if (empty($_POST['descripcion'])) $errores[] = "La descripcion es obligatoria";
    if (empty($_POST['motivo'])) $errores[] = "El motivo es obligatorio";
    
    // Si hay errores, los muestro y paro
    if (!empty($errores)) {
        // Mostrar errores (se vera feo pero funciona)
        foreach ($errores as $error) {
            echo "<p style='color:red'>$error</p>";
        }
        exit;
    }
    
    // Tipos de archivo permitidos
$permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$imagenesSubidas = [];
$maxSize = 5 * 1024 * 1024; // 5 megas
    
    // Recorrer las fotos subidas
    for ($i = 0; $i < count($_FILES['imagenes']['name']); $i++) {
        $extension = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
        
        // Comprobar tamaño
        if ($_FILES['imagenes']['size'][$i] > $maxSize) {
            $errores[] = "La foto " . ($i+1) . " pesa mas de 5MB";
            continue;
        }
        
        // Comprobar extension
        if (!in_array($extension, $permitidos)) {
            $errores[] = "La foto " . ($i+1) . " tiene formato no valido";
            continue;
        }
        
        // Nombre unico para la foto
        $nombreFoto = uniqid() . '.' . $extension;
        $ruta = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $nombreFoto;
        
        // Guardar la foto
        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta)) {
            $imagenesSubidas[] = $nombreFoto;
        } else {
            $errores[] = "Error al subir la foto " . ($i+1);
        }
    }
    
    // Si hay errores, borrar las fotos que ya se subieron
    if (!empty($errores)) {
        foreach ($imagenesSubidas as $img) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $img);
        }
        echo "<div style='color:red'>" . implode("<br>", $errores) . "</div>";
        exit;
    }
    
    // Guardar en la base de datos
    $stmt = $db->prepare("
        INSERT INTO solicitudes 
        (nombre_perro, raza, edad, sexo, tamano, descripcion, motivo, id_usuario, nombre_usuario, fecha_solicitud, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pendiente')
    ");
    
    $stmt->execute([
        $_POST['nombre'],
        $_POST['raza'],
        $_POST['edad'],
        $_POST['sexo'],
        $_POST['tamano'],
        $_POST['descripcion'],
        $_POST['motivo'],
        $_SESSION['usuario']['id_usuario'],
        $_SESSION['usuario']['nombre']
    ]);
    
    $idSolicitud = $db->lastInsertId();
    
    // Guardar las fotos
    for ($i = 0; $i < count($imagenesSubidas); $i++) {
        $esPrincipal = ($i == 0) ? 1 : 0;
        
        $stmtFoto = $db->prepare("
            INSERT INTO fotos_solicitudes (solicitud_id, nombre_foto, es_principal, orden)
            VALUES (?, ?, ?, ?)
        ");
        $stmtFoto->execute([$idSolicitud, $imagenesSubidas[$i], $esPrincipal, $i]);
    }
    
    // Redirigir a la pagina de perritos
    header('Location: perritos.php?ok=1');
    exit;
}
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow rounded-4">
                <div class="card-body p-5">
                    
                    <h2 class="text-center mb-4">🐶 Dar perro en adopción</h2>
                    
                    <div class="alert alert-info mb-4">
                        Tu solicitud será revisada antes de publicarse.
                        <br>
                        <strong>Estado:</strong> Pendiente de aprobación
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre del perro</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Raza</label>
                                <input type="text" class="form-control" name="raza" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Edad</label>
                                <input type="number" class="form-control" name="edad" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sexo</label>
                                <select class="form-select" name="sexo" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Macho">Macho</option>
                                    <option value="Hembra">Hembra</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tamaño</label>
                                <select class="form-select" name="tamano" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Pequeño">Pequeño</option>
                                    <option value="Mediano">Mediano</option>
                                    <option value="Grande">Grande</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="4" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Motivo</label>
                                <textarea class="form-control" name="motivo" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Fotos (máx 5)</label>
                                <input type="file" name="imagenes[]" multiple accept="image/*" class="form-control" required>
                                <small class="text-muted">La primera foto será la principal. Máximo 5MB por foto.</small>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    Enviar solicitud
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
