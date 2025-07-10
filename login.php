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
    <title>Inicio de Sesión</title>
    <style>
        body {
            background: linear-gradient(to right, #3B6CDC, #6BD4E2);
            justify-content: center;
            justify-items: center;
            font-family: 'Josefin Sans';
        }

        img {
            position: relative;
            width: 190px;
            height: 135px;
            margin-top: 20px;
        }

        .formulario {
            background-color: white;
            border-radius: 8px;
            width: 486px;
            height: 261px;
            justify-content: center;
            justify-items: center;
            flex-direction: column;
            padding: 8px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, 1fr);
        }

        .label {
            color: rgb(0, 0, 0);
        }

        .input {
            border-radius: 4px;
            width: 450px;
            height: 30px;
            position: relative;
            margin-top: 5px;
            border: 1px solid;
        }

        .h1 {
            font-style: 'Josefin Sans';
            color: white;
            font-size: 50px;
        }

        #enviar {
            background-color: #2a5ed6;
            width: 458px;
            position: relative;
            top: 35px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid;
            border-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <img src="img/logo.png" alt="logo">
    <h1 class="h1">Inicio de Sesión</h1>
    <?php if (!empty($error)) echo "<h3 style='color:red;'>$error</h3>"; ?>
    <div class="formulario">
        <form action="login.php" method="post">
            <br>
            <label class="label">Usuario</label>
            <br>
            <input class="input" type="text" name="username" placeholder="Valor" required>
            <br><br><br>
            <label class="label">Contraseña</label>
            <br>
            <input class="input" type="password" name="password" placeholder="Valor" required>
            <br>
            <input id="enviar" type="submit" value="Iniciar Sesión">
        </form>
    </div>
</body>
</html>