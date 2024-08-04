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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['confirmar'] == 'Si') {
           
            $stmt = $conn->prepare("DELETE FROM contactos WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: index.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .message-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            font-size: 16px;
            padding: 10px 20px;
            text-align: center;
            color: #fff;
            background-color: #333;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-box">
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que deseas eliminar el contacto <?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellidos']); ?>?</p>
            <form method="post" action="">
                <input type="submit" name="confirmar" value="Si" class="btn">
                <input type="submit" name="confirmar" value="No" class="btn">
            </form>
        </div>
    </div>
</body>
</html>
