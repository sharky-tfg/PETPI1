<?php
// Cargar lo necesario
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

// Comprobar que el usuario ha iniciado sesion
Auth::check();

// Solo administradores pueden editar
if ($_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$db = Database::getConnection();

// Recoger el id del perro desde la URL
$id = $_GET['id'] ?? null;

// Buscar el perro en la base de datos
$stmt = $db->prepare("SELECT * FROM perritos WHERE id_perro = ?");
$stmt->execute([$id]);
$perro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, volver a la lista
if (!$perro) {
    header("Location: perritos.php");
    exit;
}

// Si se ha enviado el formulario, actualizar los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $update = $db->prepare("
        UPDATE perritos
        SET nombre = ?, raza = ?, edad = ?, sexo = ?, tamano = ?, descripcion = ?
        WHERE id_perro = ?
    ");

    $update->execute([
        $_POST['nombre'],
        $_POST['raza'],
        $_POST['edad'],
        $_POST['sexo'],
        $_POST['tamano'],
        $_POST['descripcion'],
        $id
    ]);

    // Volver a la lista de perritos
    header("Location: perritos.php");
    exit;
}
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">✏️ Editar perro</h2>

    <form method="post">
        <input name="nombre" value="<?php echo htmlspecialchars($perro['nombre']); ?>" class="form-control mb-3" required>
        <input name="raza" value="<?php echo htmlspecialchars($perro['raza']); ?>" class="form-control mb-3" required>
        <input name="edad" value="<?php echo htmlspecialchars($perro['edad']); ?>" class="form-control mb-3" required>
        <input name="sexo" value="<?php echo htmlspecialchars($perro['sexo']); ?>" class="form-control mb-3" required>
        <input name="tamano" value="<?php echo htmlspecialchars($perro['tamano']); ?>" class="form-control mb-3" required>
        <textarea name="descripcion" class="form-control mb-3" required><?php echo htmlspecialchars($perro['descripcion']); ?></textarea>

        <button class="btn btn-primary">Guardar cambios</button>
    </form>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
