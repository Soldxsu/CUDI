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
$itinerarios = $conn->query("SELECT id_itinerario, CONCAT(hora_inicio, ' - ', hora_fin) AS horario FROM itinerario");

$sql = "SELECT d.id_dia, j.dias, CONCAT(i.hora_inicio, ' - ', i.hora_fin) AS horario, 
               m.nombre AS materia_nombre, a.numero AS aula_numero, 
               p.nombre AS profesor_nombre, p.apellido AS profesor_apellido, 
               a.piso, a.cantidad, c.nombre AS carrera_nombre, 
               cpu.nombre_curso AS curso_pre_admision
        FROM dias d
        LEFT JOIN jornada j ON d.jornada_id = j.id_jornada
        LEFT JOIN itinerario i ON d.itinerario_id = i.id_itinerario
        LEFT JOIN materias m ON d.materia_id = m.id_materia
        LEFT JOIN aulas a ON d.aula_id = a.id_aula
        LEFT JOIN profesores p ON d.profesor_id = p.id_profesor
        LEFT JOIN carreras c ON m.carrera_id = c.id_carrera
        LEFT JOIN cursos_pre_admisiones cpu ON m.curso_pre_admision_id = cpu.id_curso_pre_admision";
$result = $conn->query($sql);

// Analizar los datos para saber si hay materias de carrera y/o de curso de pre-admisión
$rows = [];
$hay_carrera = false;
$hay_curso = false;
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
        if (!empty($row['carrera_nombre']) && empty($row['curso_pre_admision'])) {
            $hay_carrera = true;
        }
        if (!empty($row['curso_pre_admision']) && empty($row['carrera_nombre'])) {
            $hay_curso = true;
        }
    }
}
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
    #perfil{
        width: 50px;
        height: 50px;
        margin-left: 10px;
        margin-top:8px;
        border:none;
        border-radius:150px;
    }

    #perfil:hover{
        background: #969696bb;
        transition: 0.80s;
    }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="index.php"><img id="logo" src="img/logo.png" alt="logo"></a>
        <div class="nav-links">
            <a href="disposicionaulica.php">Disposición Áulica</a>
            <a href="#">Insumos</a>
        </div>
        <a href="perfil.php"><img id="perfil" src="img/perfil.webp"></a>
    </nav>
    <br><br><br><br><br>
    <h1 align="center">Disposición Áulica</h1>
    <a href="añadir_aula.php" class="add-button">Añadir Nueva Disposición</a>

    <?php if (count($rows) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Jornada</th>
                    <th>Horario Itinerario</th>
                    <th>Materia</th>
                    <th>Profesor</th>
                    <?php if ($hay_carrera): ?><th>Carrera</th><?php endif; ?>
                    <?php if ($hay_curso): ?><th>Curso Pre-Admisión</th><?php endif; ?>
                    <th>Aula</th>
                    <th>Piso</th>
                    <th>Capacidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo $row['dias']; ?></td>
                        <td><?php echo $row['horario']; ?></td>
                        <td><?php echo $row['materia_nombre']; ?></td>
                        <td><?php echo $row['profesor_nombre'] . ' ' . $row['profesor_apellido']; ?></td>
                        <?php if ($hay_carrera): ?><td><?php echo (!empty($row['carrera_nombre']) && empty($row['curso_pre_admision'])) ? $row['carrera_nombre'] : ''; ?></td><?php endif; ?>
                        <?php if ($hay_curso): ?><td><?php echo (!empty($row['curso_pre_admision']) && empty($row['carrera_nombre'])) ? $row['curso_pre_admision'] : ''; ?></td><?php endif; ?>
                        <td><?php echo $row['aula_numero']; ?></td>
                        <td><?php echo $row['piso']; ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td class="actions">
                            <a href="actualizar.php?id=<?php echo $row['id_dia']; ?>" class="edit">Modificar</a>
                            <a href="disposicionaulica.php?action=delete&id=<?php echo $row['id_dia']; ?>" class="delete" onclick="return confirm('¿Está seguro de que desea eliminar este registro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p id="p" align="center">No hay registros de disposición áulica.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
    <a href="index.php" class="back-button" style="margin: 30px auto 40px auto; display: block; max-width: 350px; background: #6c757d; color: white; text-align: center; text-decoration: none; border-radius: 5px; padding: 12px 0; font-size: 1.1em; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">Volver al Inicio</a>
</body>
</html>