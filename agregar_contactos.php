<?php
#session_start(); FUNCION END


if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $correo = htmlspecialchars(trim($_POST['correo']));


    $foto = $_FILES['foto'];
    $uploadOk = 1;
    $target_file = '';

    if ($foto['size'] > 0) {
        $check = getimagesize($foto["tmp_name"]);
        if ($check !== false) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($foto["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($foto["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($foto["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
    } else {
        echo "No file selected";
        $uploadOk = 0;
    }


    $stmt = $conn->prepare("INSERT INTO contactos (nombre, apellidos, telefono, correo, foto, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellidos, $telefono, $correo, $target_file, $user_id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Contacto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styleAgregar.css"> <!-- Estilos adicionales personalizados -->
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="INDEX.PHP">Añadir Contacto</a>
            <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                    <a class="nav-link" href="index.php">Volver a la Lista</a>
                    </li>
          </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto de Perfil:</label>
                <input type="file" id="foto" name="foto" accept="image/*" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>