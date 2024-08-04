<?php
#session_start(); FUNCION END
include 'config.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contacto_id = $_POST['contacto_id'];
    $etiqueta_id = $_POST['etiqueta_id'];

    $stmt = $conn->prepare("INSERT INTO contacto_etiqueta (id_contacto, id_etiqueta) VALUES (?, ?)");
    $stmt->execute([$contacto_id, $etiqueta_id]);
    $mensaje = "Etiqueta añadida exitosamente al contacto.";
}

$contactos = $conn->prepare("SELECT * FROM contactos WHERE id_usuario = ?");
$contactos->execute([$user_id]);
$contactos = $contactos->fetchAll(PDO::FETCH_ASSOC);

$etiquetas = $conn->prepare("SELECT * FROM etiquetas WHERE id_usuario = ?");
$etiquetas->execute([$user_id]);
$etiquetas = $etiquetas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Etiqueta a Contacto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Añadir Etiqueta a Contacto</h2>
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post" action="añadir_etiqueta_contacto.php">
            <div class="form-group">
                <label for="contacto_id">Selecciona un Contacto:</label>
                <select class="form-control" id="contacto_id" name="contacto_id" required>
                    <?php foreach ($contactos as $contacto): ?>
                        <option value="<?php echo $contacto['id']; ?>"><?php echo htmlspecialchars($contacto['nombre']) . " " . htmlspecialchars($contacto['apellidos']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="etiqueta_id">Selecciona una Etiqueta:</label>
                <select class="form-control" id="etiqueta_id" name="etiqueta_id" required>
                    <?php foreach ($etiquetas as $etiqueta): ?>
                        <option value="<?php echo $etiqueta['id']; ?>"><?php echo htmlspecialchars($etiqueta['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Añadir Etiqueta</button>
        </form>
    </div>
</body>
</html>
