<?php
include 'conexion.php';

$id_dia = '';
$jornada_id = ''; 
$itinerario_id = '';
$materia_id = '';
$aula_id = '';
$form_title = 'Añadir Nueva Disposición Áulica';

if (isset($_GET['id'])) {
    $id_dia = $_GET['id'];
    $sql = "SELECT * FROM dias WHERE id_dia = $id_dia";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jornada_id = $row['jornada_id']; 
        $itinerario_id = $row['itinerario_id'];
        $materia_id = $row['materia_id'];
        $aula_id = $row['aula_id'];
        $form_title = 'Modificar Disposición Áulica';
    } else {
        echo "<p style='color:red;'>Registro no encontrado.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jornada_id = $_POST['jornada_id']; 
    $itinerario_id = $_POST['itinerario_id'];
    $materia_id = $_POST['materia_id'];
    $aula_id = $_POST['aula_id'];

    if (isset($_POST['id_dia']) && !empty($_POST['id_dia'])) {
        $id_dia = $_POST['id_dia'];
        $sql = "UPDATE dias SET jornada_id = '$jornada_id', itinerario_id = $itinerario_id, materia_id = $materia_id, aula_id = $aula_id WHERE id_dia = $id_dia";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Registro actualizado exitosamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al actualizar el registro: " . $conn->error . "</p>";
        }
    } else {
        $sql = "INSERT INTO dias (jornada_id, itinerario_id, materia_id, aula_id) VALUES ('$jornada_id', $itinerario_id, $materia_id, $aula_id)";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Nuevo registro creado exitosamente.</p>";
            $jornada_id = '';
            $itinerario_id = '';
            $materia_id = '';
            $aula_id = '';
        } else {
            echo "<p style='color:red;'>Error al crear el registro: " . $conn->error . "</p>";
        }
    }
}

$jornada_options = $conn ->query("SELECT id_jornada, dias FROM jornada");
$aulas_options = $conn->query("SELECT id_aula, numero FROM aulas");
$materias_options = $conn->query("SELECT id_materia, nombre FROM materias");
$itinerarios_options = $conn->query("SELECT id_itinerario, horario FROM itinerario");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $form_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="css/añadir.css">
</head>
<body>
    <div class="form-container">
        <h2><?php echo $form_title; ?></h2>
        <form action="form_disposicion.php" method="POST">
            <?php if (!empty($id_dia)): ?>
                <input type="hidden" name="id_dia" value="<?php echo $id_dia; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="jornada">Jornada:</label>
                <select id="jornada_id" name="jornada_id" required>
                    <option value="">Seleccione un día</option>
                    <?php while ($row = $jornada_options->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_jornada']; ?>" <?php echo ($jornada_id == $row['id_jornada']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['dias']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="itinerario_id">Horario Itinerario:</label>
                <select id="itinerario_id" name="itinerario_id" required>
                    <option value="">Seleccione un horario</option>
                    <?php while ($row = $itinerarios_options->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_itinerario']; ?>" <?php echo ($itinerario_id == $row['id_itinerario']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['horario']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
            </div>

            <div class="form-group">
                <label for="materia_id">Materia:</label>
                <select id="materia_id" name="materia_id" required>
                    <option value="">Seleccione una materia</option>
                    <?php while ($row = $materias_options->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_materia']; ?>" <?php echo ($materia_id == $row['id_materia']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="aula_id">Aula:</label>
                <select id="aula_id" name="aula_id" required>
                    <option value="">Seleccione un aula</option>
                    <?php while ($row = $aulas_options->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_aula']; ?>" <?php echo ($aula_id == $row['id_aula']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['numero']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Guardar Disposición</button>
            </div>
        </form>
        <a href="index.php" class="back-button">Volver al Listado</a>
    </div>
    <?php $conn->close();
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#materia_id').select2({
        placeholder: "Seleccione una materia",
        width: '100%'
        });
    });
    $(document).ready(function() {
        $('#jornada_id').select2({
        placeholder: "Seleccione una jornada",
        width: '100%'
        });
    });

    $(document).ready(function() {
        $('#itinerario_id').select2({
        placeholder: "Seleccione un horario",
        width: '100%'
        });
    });

    $(document).ready(function() {
        $('#aula_id').select2({
        placeholder: "Seleccione un aula",
        width: '100%'
        });
    });
    </script>


</body>
</html>