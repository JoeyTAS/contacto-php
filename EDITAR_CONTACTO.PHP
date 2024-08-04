<?php
#session_start(); FUNCION END

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM contactos WHERE id = ?");
    $stmt->execute([$id]);
    $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener la foto del contacto si está disponible
    $contacto_photo = isset($contacto['foto']) ? htmlspecialchars($contacto['foto']) : 'uploads/default_contact.jpg'; // Ruta de la imagen predeterminada para contactos

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];

        // Actualizar el contacto en la base de datos
        $stmt = $conn->prepare("UPDATE contactos SET nombre = ?, apellidos = ?, telefono = ?, correo = ? WHERE id = ?");
        $stmt->execute([$nombre, $apellidos, $telefono, $correo, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contacto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            <a class="navbar-brand" href="#">Editar Contacto</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Volver a la Lista</a>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center">
                    <img src="<?php echo $contacto_photo; ?>" alt="Foto de Contacto" class="perfil-img">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="form-group mt-3">
                            <label for="foto">Cambiar Foto:</label>
                            <input type="file" id="foto" name="foto" accept="image/*" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-sm btn-secondary">Subir Foto</button>
                    </form>
                </div>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($contacto['apellidos']); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($contacto['telefono']); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($contacto['correo']); ?>" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap y jQuery necesarios -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
