<?php
include 'conexion.php';

// Verificar si viene el ID
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>ID no proporcionado.</p>";
    exit;
}

$id_dia = intval($_GET['id']);

// Obtener datos actuales
$sql = "SELECT * FROM dias WHERE id_dia = $id_dia";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>Registro no encontrado.</p>";
    exit;
}

$row = $result->fetch_assoc();

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jornada_id = $_POST['jornada_id'];
    $itinerario_id = $_POST['itinerario_id'];
    $materia_id = $_POST['materia_id'];
    $aula_id = $_POST['aula_id'];

    $update_sql = "UPDATE dias SET jornada_id = '$jornada_id', itinerario_id = '$itinerario_id', materia_id = '$materia_id', aula_id = '$aula_id' WHERE id_dia = $id_dia";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: disposicionaulica.php?updated=1");
        exit;
    } else {
        echo "<p style='color:red;'>Error al actualizar el registro: " . $conn->error . "</p>";
    }
}

// Opciones para selects
$jornadas = $conn->query("SELECT id_jornada, dias FROM jornada");
$itinerarios = $conn->query("SELECT id_itinerario, horario FROM itinerario");
$materias = $conn->query("SELECT id_materia, nombre FROM materias");
$aulas = $conn->query("SELECT id_aula, numero FROM aulas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Disposición Áulica</title>
    <link rel="stylesheet" href="css/añadir.css">
</head>
<body>
    <div class="form-container">
        <h2>Actualizar Disposición Áulica</h2>
        <form method="POST">
            <div class="form-group">
                <label for="jornada_id">Jornada:</label>
                <select name="jornada_id" required>
                    <option value="">Seleccione un día</option>
                    <?php while ($j = $jornadas->fetch_assoc()): ?>
                        <option value="<?php echo $j['id_jornada']; ?>" <?php echo ($row['jornada_id'] == $j['id_jornada']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($j['dias']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="itinerario_id">Horario:</label>
                <select name="itinerario_id" required>
                    <option value="">Seleccione un horario</option>
                    <?php while ($i = $itinerarios->fetch_assoc()): ?>
                        <option value="<?php echo $i['id_itinerario']; ?>" <?php echo ($row['itinerario_id'] == $i['id_itinerario']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($i['horario']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="materia_id">Materia:</label>
                <select name="materia_id" required>
                    <option value="">Seleccione una materia</option>
                    <?php while ($m = $materias->fetch_assoc()): ?>
                        <option value="<?php echo $m['id_materia']; ?>" <?php echo ($row['materia_id'] == $m['id_materia']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($m['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="aula_id">Aula:</label>
                <select name="aula_id" required>
                    <option value="">Seleccione un aula</option>
                    <?php while ($a = $aulas->fetch_assoc()): ?>
                        <option value="<?php echo $a['id_aula']; ?>" <?php echo ($row['aula_id'] == $a['id_aula']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($a['numero']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Guardar Cambios</button>
            </div>
        </form>
        <a href="disposicionaulica.php" class="back-button">Volver al listado</a>
    </div>
    <?php $conn->close(); ?>
</body>
</html>