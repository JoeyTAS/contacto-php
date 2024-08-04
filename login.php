<?php
#session_start(); FUNCION END
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    $stmt = $conn->prepare("SELECT id, contrasenia, intentos_fallidos, bloqueado_hasta FROM `usuarios` WHERE nombre=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario está bloqueado
    if ($user && $user['bloqueado_hasta'] && new DateTime() < new DateTime($user['bloqueado_hasta'])) {
        $error = "Tu cuenta está bloqueada. Por favor, inténtalo más tarde.";
    } else {
        if ($user && $password === $user['contrasenia']) {
            // Reiniciar intentos fallidos en caso de éxito
            $stmt = $conn->prepare("UPDATE `usuarios` SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);

            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            // Incrementar intentos fallidos
            if ($user) {
                $intentos_fallidos = $user['intentos_fallidos'] + 1;
                if ($intentos_fallidos >= 5) {
                    // Bloquear la cuenta por 15 minutos
                    $bloqueado_hasta = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
                    $stmt = $conn->prepare("UPDATE `usuarios` SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id = ?");
                    $stmt->execute([$intentos_fallidos, $bloqueado_hasta, $user['id']]);
                    $error = "Demasiados intentos fallidos. Tu cuenta está bloqueada por 15 minutos.";
                } else {
                    $stmt = $conn->prepare("UPDATE `usuarios` SET intentos_fallidos = ? WHERE id = ?");
                    $stmt->execute([$intentos_fallidos, $user['id']]);
                    $error = "Credenciales incorrectas. Por favor, inténtalo de nuevo.";
                }
            } else {
                $error = "Credenciales incorrectas. Por favor, inténtalo de nuevo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form method="post" action="login.php">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Iniciar Sesión">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p>No tienes cuenta? <a href="register.php">Regístrate ahora</a></p>
    </div>
</body>
</html>
