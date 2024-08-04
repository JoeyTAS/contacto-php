<?php
#session_start(); FUNCION END

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $foto = $_FILES['foto'];
    
    $stmt = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_photo = $user['foto'];


    $default_photo = 'uploads/predeterminado.jpg';

    if ($foto['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $foto['tmp_name'];
        $foto_name = basename($foto['name']);
        $upload_dir = 'uploads/';
        $target_file = $upload_dir . $foto_name;


        if (move_uploaded_file($tmp_name, $target_file)) {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nombre, $correo, $target_file, $user_id]);
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, foto = ? WHERE id = ?");
        $stmt->execute([$nombre, $correo, $default_photo, $user_id]);
    }
    header('Location: perfil.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados */
        .perfil-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Perfil de Usuario</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Editar Perfil</h2>
                <form action="perfil.php" method="post" enctype="multipart/form-data">
                    <div class="text-center">
                        <?php if ($usuario['foto']) : ?>
                            <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de perfil" class="perfil-img">
                        <?php else : ?>
                            <img src="uploads/predeterminado.jpg" alt="Foto de perfil" class="perfil-img">
                        <?php endif; ?>
                        <input type="file" id="foto" name="foto" accept="image/*" class="mt-3">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" class="form-control" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
