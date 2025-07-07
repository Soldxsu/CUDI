<?php
include 'conexion.php'; 

$sql_dias = "SELECT d.id_dia, j.dias AS jornada_nombre, i.horario, m.nombre AS materia_nombre, a.numero AS aula_numero
             FROM dias d
             LEFT JOIN jornada j ON d.jornada_id = j.id_jornada 
             LEFT JOIN itinerario i ON d.itinerario_id = i.id_itinerario
             LEFT JOIN materias m ON d.materia_id = m.id_materia
             LEFT JOIN aulas a ON d.aula_id = a.id_aula
             ORDER BY j.dias, i.horario";
$result_dias = $conn->query($sql_dias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión CUDI</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">CUDI</div>
        <div class="nav-links">
            <a href="disposicionaulica.php">Disposición Áulica</a>
            <a href="#">Insumos</a>
        </div>
    </div>

    <br>
    <br>
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
                                <td><?php echo date('H:i', strtotime($row['horario'])); ?></td>
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

    <?php
    $conn->close();
    ?>
</body>
</html>