<?php
#session_start(); FUNCION END
include 'config.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$mensaje = '';

// Procesar formulario enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crear_etiqueta'])) {
        // Crear nueva etiqueta
        $nombre_etiqueta = htmlspecialchars(trim($_POST['nombre_etiqueta']));
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO etiquetas (nombre_etiqueta, id_usuario) VALUES (?, ?)");
        $stmt->execute([$nombre_etiqueta, $user_id]);
        $mensaje = "Etiqueta creada exitosamente.";
    } elseif (isset($_POST['agregar_etiqueta'])) {
        // Añadir etiqueta a un contacto
        $id_contacto = $_POST['id_contacto'];
        $id_etiqueta = $_POST['id_etiqueta'];

        $stmt = $conn->prepare("INSERT INTO contactos_etiquetas (id_contacto, id_etiqueta) VALUES (?, ?)");
        $stmt->execute([$id_contacto, $id_etiqueta]);
        $mensaje = "Etiqueta añadida al contacto exitosamente.";
    } elseif (isset($_POST['quitar_etiqueta'])) {
        // Quitar etiqueta de un contacto
        $id_contacto = $_POST['id_contacto'];
        $id_etiqueta = $_POST['id_etiqueta'];

        $stmt = $conn->prepare("DELETE FROM contactos_etiquetas WHERE id_contacto = ? AND id_etiqueta = ?");
        $stmt->execute([$id_contacto, $id_etiqueta]);
        $mensaje = "Etiqueta quitada del contacto exitosamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Etiqueta</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="INDEX.PHP">Contactos Actuales</a>
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
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </header>
    <div class="container">
        <h2 class="my-4">Gestión de Etiquetas</h2>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['id_contacto'])) : ?>
            <h3>Añadir o Quitar Etiqueta a un Contacto</h3>
            <?php

            $stmt = $conn->prepare("SELECT * FROM contactos WHERE id = ?");
            $stmt->execute([$_GET['id_contacto']]);
            $contacto = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Contacto: <?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellidos']); ?></h5>
                </div>
                <div class="card-body">
                    <form method="post" action="crear_etiqueta.php">
                        <div class="form-group">
                            <label for="id_etiqueta">Selecciona la Etiqueta:</label>
                            <select class="form-control" id="id_etiqueta" name="id_etiqueta" required>
                                <?php
                                // Obtener lista de etiquetas del usuario
                                $stmt = $conn->prepare("SELECT id_etiqueta, nombre_etiqueta FROM etiquetas WHERE id_usuario = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $etiquetas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($etiquetas as $etiqueta) : ?>
                                    <option value="<?php echo $etiqueta['id_etiqueta']; ?>"><?php echo htmlspecialchars($etiqueta['nombre_etiqueta']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="id_contacto" value="<?php echo $_GET['id_contacto']; ?>">
                        <input type="hidden" name="agregar_etiqueta" value="true">
                        <button type="submit" class="btn btn-primary">Añadir Etiqueta</button>
                    </form>

                    <hr>

                    <form method="post" action="crear_etiqueta.php">
                        <div class="form-group">
                            <label for="id_etiqueta_eliminar">Selecciona la Etiqueta a Quitar:</label>
                            <select class="form-control" id="id_etiqueta_eliminar" name="id_etiqueta" required>
                                <?php
                                // Obtener lista de etiquetas del contacto actual
                                $stmt = $conn->prepare("SELECT e.id_etiqueta, e.nombre_etiqueta FROM etiquetas e
                                                      INNER JOIN contactos_etiquetas ce ON e.id_etiqueta = ce.id_etiqueta
                                                      WHERE ce.id_contacto = ?");
                                $stmt->execute([$contacto['id']]);
                                $etiquetas_asociadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($etiquetas_asociadas as $etiqueta) : ?>
                                    <option value="<?php echo $etiqueta['id_etiqueta']; ?>"><?php echo htmlspecialchars($etiqueta['nombre_etiqueta']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="id_contacto" value="<?php echo $_GET['id_contacto']; ?>">
                        <input type="hidden" name="quitar_etiqueta" value="true">
                        <button type="submit" class="btn btn-danger">Quitar Etiqueta</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Crear Etiqueta Nueva</h3>
            </div>
            <div class="card-body">
                <form method="post" action="crear_etiqueta.php">
                    <div class="form-group">
                        <label for="nombre_etiqueta">Nombre de la Etiqueta:</label>
                        <input type="text" class="form-control" id="nombre_etiqueta" name="nombre_etiqueta" required>
                    </div>
                    <input type="hidden" name="crear_etiqueta" value="true">
                    <button type="submit" class="btn btn-primary">Crear Etiqueta</button>
                </form>
            </div>
        </div>

        <a href="javascript:history.go(-1)" class="btn btn-secondary mt-3">Volver</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>