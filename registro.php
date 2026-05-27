<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/Usuario.php';

$error = "";
$nombre = "";
$email = "";

//si envian formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    //validar campos
    if (empty($nombre) || empty($email) || empty($password) || empty($password2)) {
        $error = "Todos los campos son obligatorios.";
    } 
    elseif (strpos($email, '@') === false || strpos($email, '.') === false) {
        $error = "Introduce un email válido.";
    } 
    elseif ($password !== $password2) {
        $error = "Las contraseñas no coinciden.";
    } 
    elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } 
    else {
        //guardar usuario
        Usuario::registrar($nombre, $email, $password);

        header("Location: login.php");
        exit;
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';
?>

<div class="auth-page">
    <div class="auth-box">

        <h3>Crear cuenta</h3>
        <p class="auth-subtitle">Únete y cambia una vida</p>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">

            <input 
                type="text"
                name="nombre"
                placeholder="Nombre completo"
                value="<?= htmlspecialchars($nombre) ?>"
                required
                autocomplete="off"
            >

            <input 
                type="email"
                name="email"
                placeholder="Correo electrónico"
                value="<?= htmlspecialchars($email) ?>"
                required
                autocomplete="off"
            >

            <input 
                type="password"
                name="password"
                placeholder="Contraseña"
                required
                autocomplete="new-password"
            >

            <input 
                type="password"
                name="password2"
                placeholder="Repetir contraseña"
                required
                autocomplete="new-password"
            >

            <button type="submit" class="btn-register-main">
                Crear cuenta
            </button>

        </form>

        <div class="auth-link">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
        </div>

    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
