<?php
session_start();
include 'conexion.php';
$_SESSION['nombre_usuario'] = $_SESSION['usuario']; 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
             font-family: Arial, sans-serif;
        }

        .perfil{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
        }
        .card{
            width: 400px;
            height: 500px;
            border: 1px solid;
            border-radius: 10px;
            overflow:hidden;
            display: flex;
            padding:20px;
            flex-direction: column;
        }

        .header-card{
            display: flex;
            height: 160px;
            border-bottom:1px solid;
        }

        #profile{
            width: 100px;
            height: 100px;
            margin: 0 auto;
            margin-top: 20px;
            margin-right: 10px;
        }

        #profile:hover{
            transform: scale(1.1);
            transition: 0.3s;
        }

        #logo{
            width: 150px;
            height: 110px;
            margin: 0 auto;
            margin-top: 20px;
            margin-left: 5px;
        }

        #logo:hover{
            transform: scale(1.1);
            transition: 0.3s;
        }

        #out{
            background-color: #df2424ff;
            color: white;
            width: 110px;
            text-decoration:none;
            padding:20px;
            text-align: center;
            border-radius:4px;
            margin-top: 50%;
            margin-left: 10px;
            display: flex;
            border:1px solid #df2424ff;
        }

        #out:hover {
            border: 1.5px solid #ce1111;   
            background-color: #ffffff;   
            color: #ce1111;              
            transition: 0.1s;            
        }

    </style>
</head>
<body>
    <div class="perfil">
        <div class="card">
            <div class="header-card">
                <a href="index.php"><img id="logo" src="img/logo.png" alt="logo"></a>
                <img id="profile" src="img/perfil.webp" alt="logo">
            </div>
            <h3>Nombre: <?php echo $_SESSION['nombre_usuario']; ?></h3>
            <a id="out" href="logout.php">Cerrar Sesion</a>
        </div>
    </div>
</body>
</html>