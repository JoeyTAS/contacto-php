<?php
#session_start(); FUNCION END
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    $stmt = $conn->prepare("SELECT count(*) FROM `usuarios` WHERE nombre=?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $error = "El nombre de usuario ya existe. Por favor, elige otro.";
    } else {
        $stmt = $conn->prepare("INSERT INTO `usuarios` (nombre, contrasenia) VALUES (?, ?)");
        if ($stmt->execute([$username, $password])) {
            // Obtener el ID del nuevo usuario
            $user_id = $conn->lastInsertId();
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;

            // Redirigir a la página de éxito
            header("Location: registro_exitoso.php");
            exit;
        } else {
            $error = "Hubo un problema al crear tu cuenta. Por favor, inténtalo de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Registrarse</h2>
        <form method="post" action="register.php">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Crear cuenta">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
