<?php
// Cargar las clases necesarias
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/Usuario.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Auth.php';

$error = "";
$email = "";

// Si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);

    if (empty($email) || empty($_POST['password'])) {
        $error = "Por favor completa todos los campos.";
    } else {
        $usuario = Usuario::login($email, $_POST['password']);

        if ($usuario) {
            Auth::login($usuario);
            header("Location: index.php");
            exit;
        } else {
            $error = "Email o contraseña incorrectos.";
        }
    }
}

// Mostrar la cabecera de la pagina
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php';
?>

<div class="auth-page">
    <div class="auth-box">

        <h3>Iniciar sesión</h3>
        <p class="auth-subtitle">Accede para encontrar a tu próximo mejor amigo</p>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">

            <input 
                type="email" 
                name="email" 
                placeholder="Correo electrónico"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >

            <input 
                type="password" 
                name="password" 
                id="password"
                placeholder="Contraseña"
                autocomplete="new-password"
                required
            >

            <button type="submit" class="btn-login-main">
                Iniciar sesión
            </button>

        </form>

        <div class="auth-link">
            ¿No tienes cuenta? <a href="registro.php">Crear cuenta</a>
        </div>

    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
