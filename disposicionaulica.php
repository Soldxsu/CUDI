<?php
include 'conexion.php';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_dia = $_GET['id'];
    $delete = "DELETE FROM dias WHERE id_dia = $id_dia";
    if ($conn->query($delete) === TRUE) {
    } else {
        echo "<p style='color:red;'>Error al eliminar el registro: " . $conn->error . "</p>";
    }
}

$aulas = $conn->query("SELECT id_aula, numero FROM aulas");
$materias = $conn->query("SELECT id_materia, nombre FROM materias");
$itinerarios = $conn->query("SELECT id_itinerario, horario FROM itinerario");

$sql = "SELECT d.id_dia, j.dias, i.horario, m.nombre AS materia_nombre, a.numero AS aula_numero
        FROM dias d
        LEFT JOIN jornada j ON d.jornada_id = j.id_jornada
        LEFT JOIN itinerario i ON d.itinerario_id = i.id_itinerario
        LEFT JOIN materias m ON d.materia_id = m.id_materia
        LEFT JOIN aulas a ON d.aula_id = a.id_aula";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Disposición Áulica</title>
    <link rel="stylesheet" href="css/disposicion.css">
    <style>
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

    <h1 align="center">Disposición Áulica</h1>
    <a href="añadir_aula.php" class="add-button">Añadir Nueva Disposición</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Jornada</th>
                    <th>Horario Itinerario</th>
                    <th>Materia</th>
                    <th>Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['dias']; ?></td>
                        <td><?php echo $row['horario']; ?></td>
                        <td><?php echo $row['materia_nombre']; ?></td>
                        <td><?php echo $row['aula_numero']; ?></td>
                        <td class="actions">
                            <a href="actualizar.php?id=<?php echo $row['id_dia']; ?>" class="edit">Modificar</a>
                            <a href="disposicionaulica.php?action=delete&id=<?php echo $row['id_dia']; ?>" class="delete" onclick="return confirm('¿Está seguro de que desea eliminar este registro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p id="p" align="center">No hay registros de disposición áulica.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>