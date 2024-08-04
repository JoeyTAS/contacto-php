<?php
#session_start(); FUNCION END

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_photo = $user['foto'];

if (!$user_photo || !file_exists($user_photo)) {
    $user_photo = 'uploads/predeterminado.jpg';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contactos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .perfil-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .contact-counter {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .card {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 10px;
        }

        .card-body {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info {
            margin-right: 10px;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            min-width: auto;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Contactos Actuales</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="agregar_contactos.php">Agregar Contacto</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Opciones
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="index.php">Lista de Contactos</a>
                            <a class="dropdown-item" href="crear_etiqueta.php">Crear Etiquetas</a>
            
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="Foto de Perfil" class="perfil-img">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="perfil.php">Perfil</a>
                            <a class="dropdown-item" href="logout.php">Cerrar Sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

   

    <div class="container">
    <?php
 
    $stmt = $conn->prepare("SELECT COUNT(*) FROM contactos WHERE id_usuario = ?");
    $stmt->execute([$user_id]);
    $num_contactos = $stmt->fetchColumn();
    ?>
    <div class="contact-counter">
        <p>Tienes <?php echo $num_contactos; ?> contactos.</p>
    </div>
    <h2>Lista de Contactos</h2>
    <div class="row">
        <?php
      
        $stmt = $conn->prepare("SELECT * FROM contactos WHERE id_usuario = ?");
        $stmt->execute([$user_id]);
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($contactos as $contacto) {
            echo "<div class='col-md-4'>";
            echo "<div class='card'>";
            echo "<div class='card-header'>";
            echo "<h3>" . htmlspecialchars($contacto['nombre']) . " " . htmlspecialchars($contacto['apellidos']) . "</h3>";
            echo "<p><strong>Correo Electrónico:</strong> " . htmlspecialchars($contacto['correo']) . "</p>";
            echo "<p><strong>Número de Celular:</strong> " . htmlspecialchars($contacto['telefono']) . "</p>";
            echo "</div>";
            echo "<div class='card-body'>";

         
            $stmt_etiquetas = $conn->prepare("SELECT e.nombre_etiqueta FROM etiquetas e
                                              INNER JOIN contactos_etiquetas ce ON e.id_etiqueta = ce.id_etiqueta
                                              WHERE ce.id_contacto = ?");
            $stmt_etiquetas->execute([$contacto['id']]);
            $etiquetas = $stmt_etiquetas->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($etiquetas)) {
                echo "<p><strong>Etiquetas:</strong> " . implode(", ", $etiquetas) . "</p>";
            }

            $contacto_photo = isset($contacto['foto']) && file_exists($contacto['foto']) ? htmlspecialchars($contacto['foto']) : 'uploads/predeterminado.jpg';

            echo "<div class='contact-info'>";
            echo "<img src='$contacto_photo' alt='Foto de Contacto' class='perfil-img'>";
            echo "</div>";

            echo "<div class='btn-group'>";
            echo "<a href='editar_contacto.php?id=" . $contacto['id'] . "' class='btn btn-primary mr-2'>Editar</a>";
            echo "<a href='eliminar_contacto.php?id=" . $contacto['id'] . "' class='btn btn-danger'>Eliminar</a>";
            echo "</div>";

            echo "<div>";
            echo "<a href='crear_etiqueta.php?id_contacto=" . $contacto['id'] . "' class='btn btn-secondary mt-2'> + </a>";
            echo "</div>";

            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>