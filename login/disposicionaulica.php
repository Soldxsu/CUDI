<?php
include 'conexion.php';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_dia = $_GET['id'];
    $delete = "DELETE FROM dias WHERE id_dia = $id_dia";
    if ($con->query($delete) === TRUE) {
        echo "<p style='color:green;'>Registro eliminado exitosamente.</p>";
    } else {
        echo "<p style='color:red;'>Error al eliminar el registro: " . $conn->error . "</p>";
    }
}

$aulas = $con->query("SELECT id_aula, numero FROM aulas");
$materias = $con->query("SELECT id_materia, nombre FROM materias");
$itinerarios = $con->query("SELECT id_itinerario, horario FROM itinerario");

$sql = "SELECT d.id_dia, j.dias, i.horario, m.nombre AS materia_nombre, a.numero AS aula_numero
        FROM dias d
        LEFT JOIN jornada j ON d.jornada_id = j.id_jornada
        LEFT JOIN itinerario i ON d.itinerario_id = i.id_itinerario
        LEFT JOIN materias m ON d.materia_id = m.id_materia
        LEFT JOIN aulas a ON d.aula_id = a.id_aula";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Disposición Áulica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 10px; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .edit { background-color: #4CAF50; color: white; }
        .delete { background-color: #f44336; color: white; }
        .add-button { background-color: #008CBA; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Disposición Áulica</h1>

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
                            <a href="form_disposicion.php?id=<?php echo $row['id_dia']; ?>" class="edit">Modificar</a>
                            <a href="disponibilidadaulica.php?action=delete&id=<?php echo $row['id_dia']; ?>" class="delete" onclick="return confirm('¿Está seguro de que desea eliminar este registro?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros de disposición áulica.</p>
    <?php endif; ?>

    <?php $con->close(); ?>
</body>
</html>