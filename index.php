<?php
include 'conexion.php'; 
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
$sql_dias = "SELECT d.id_dia, j.dias AS jornada_nombre, CONCAT(i.hora_inicio, ' - ', i.hora_fin) AS horario, m.nombre AS materia_nombre, a.numero AS aula_numero
             FROM dias d
             LEFT JOIN jornada j ON d.jornada_id = j.id_jornada 
             LEFT JOIN itinerario i ON d.itinerario_id = i.id_itinerario
             LEFT JOIN materias m ON d.materia_id = m.id_materia
             LEFT JOIN aulas a ON d.aula_id = a.id_aula
             ORDER BY j.dias, i.hora_inicio, i.hora_fin";
$result_dias = $conn->query($sql_dias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión CUDI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        nav.nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: rgb(255, 255, 255);
        width: 100%;
        height: 80px;
        padding: 30px;
        box-sizing: border-box;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }

    nav.nav::after {
        content: "";
        position: absolute;
        top: 90px;
        right: 0px;
        left: 0px;
        height: 10px;
        width: 100%;
        background: linear-gradient(to right, #3B6CDC, #6BD4E2);
    }

    #logo {
        width: 100px;
        font-weight: bold;
        color: rgb(0, 0, 0);
        font-family: 'Segoe UI';
        text-decoration: none;
    }
    .nav-links a {
            color: black;
            text-decoration: none;
            margin-left: 20px;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    .nav-links {
        display: flex;
        list-style: none;
        gap: 35px;
        margin: 0;
        margin-left: 700px;
        padding: 0;
    }

    .nav-links {
        color: rgb(0, 0, 0);
        cursor: pointer;
        font-size: 20px;
        font-family: 'Segoe UI';
    }
    .nav-links a:hover {
        background: linear-gradient(to right, #3B6CDC, #6BD4E2);
        color:white;
        }
    
        .content-sections {
            margin-top:170px;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            flex-wrap: wrap; 
        }
        .section-box {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            width: 45%;
            min-width: 300px;
            box-sizing: border-box; 
        }
        .section-box h2 {
            margin-top: 0;
            color: #004080;
            text-align: center;
        }
        .section-box .section-button {
            display: block;
            width: fit-content;
            margin: 15px auto 0 auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .section-box .section-button:hover {
            background-color: #218838;
        }
        .aulas-ocupadas table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .aulas-ocupadas th, .aulas-ocupadas td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
            font-size: 0.9em;
        }
        .aulas-ocupadas th {
            background-color: #f2f2f2;
        }
        .aulas-ocupadas tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .dispocision{
            margin-top: 200px;
        }
        #out{
            background-color: #df2424ff;
            color: white;
            width: 110px;
            text-decoration:none;
            padding:20px;
            padding-bottom:10px;
            padding-top:10px;
            text-align: center;
            border-radius:4px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="index.html"><img id="logo" src="img/logo.png" alt="logo"></a>
        <div class="nav-links">
            <a href="disposicionaulica.php">Disposición Áulica</a>
            <a href="#">Insumos</a>
        </div>
    </nav>

    <div class="content-sections">
        <div class="section-box aulas-ocupadas">
            <h2>Disposición Áulica</h2>
            <a href="disposicionaulica.php" class="section-button">Gestionar Disposición Áulica</a>
            <br>
            <h3 align="center">Aulas Ocupadas Recientes</h3>
            <?php if ($result_dias->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Horario</th>
                            <th>Materia</th>
                            <th>Aula</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_dias->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['jornada_nombre']; ?></td>
                                <td><?php echo $row['horario']; ?></td>
                                <td><?php echo $row['materia_nombre']; ?></td>
                                <td><?php echo $row['aula_numero']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>
                <p id="p" align="center">No hay aulas ocupadas registradas.</p>
            <?php endif; ?>
        </div>

        <div class="section-box insumos-section">
            <h2>Insumos</h2>
            <a href="#" class="section-button">Gestionar Insumos</a>
            <br>
            <p align="center">Contenido relacionado con insumos (próximamente).</p>
        </div>
    </div>
    <br><br><br><br><br><br>
    <a id="out" href="logout.php">Cerrar Sesion</a>
    <?php
    $conn->close();
    ?>
</body>
</html>