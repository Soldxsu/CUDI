<?php
session_start();
include 'conexion.php';


if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $conn->real_escape_string($_POST['password'] ?? '');

    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$username' AND contrasena = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $_SESSION['usuario'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credenciales inválidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <title>Inicio de Sesión</title>
</head>
<body>
    <div class="background">
        <svg width="837" height="1024" viewBox="0 0 837 1024" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M837 0C837 0 606.5 210.06 606.5 520.806C606.5 822.722 837 1024 837 1024H0V0H837Z" fill="url(#paint0_linear_103_63)"/>
            <defs>
                <linearGradient id="paint0_linear_103_63" x1="418.5" y1="0" x2="418.5" y2="1024" gradientUnits="userSpaceOnUse">
                <stop stop-color="#5F61D4"/>
                <stop offset="0.5" stop-color="#4CA6F1"/>
                <stop offset="1" stop-color="#65DCD2"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    <div class="imagen">
        <img src="img/Cudi.png" alt="logo del cudi">
    </div>

    <div class="formulario">
        <h1>Inicio de Sesión</h1>
        <?php if (!empty($error)) echo "<h3 style='color:red;'>$error</h3>"; ?>
        <form action="login.php" method="post">
            <input class="input" type="text" name="username" placeholder="Usuario" required>
            <input class="input" type="password" name="password" placeholder="Contraseña" required>
            <input id="enviar" type="submit" value="Iniciar Sesión">
        </form>
    </div>
</body>

</html>