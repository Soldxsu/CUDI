<?php
include 'conexion.php';

$id_dia = '';
$jornada_id = ''; 
$itinerario_id = '';
$materia_id = '';
$aula_id = '';
$profesor_id = '';
$form_title = 'Añadir Nueva Disposición Áulica';
$submit_label = 'Guardar Disposición';
$hora_inicio = '';
$hora_fin = '';

if (isset($_GET['id'])) {
    $id_dia = intval($_GET['id']);
    $sql = "SELECT d.*, i.hora_inicio, i.hora_fin FROM dias d JOIN itinerario i ON d.itinerario_id = i.id_itinerario WHERE d.id_dia = $id_dia";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jornada_id = $row['jornada_id']; 
        $itinerario_id = $row['itinerario_id'];
        $materia_id = $row['materia_id'];
        $aula_id = $row['aula_id'];
        $profesor_id = $row['profesor_id'];
        $hora_inicio = $row['hora_inicio'];
        $hora_fin = $row['hora_fin'];
        $form_title = 'Modificar Disposición Áulica';
        $submit_label = 'Guardar Cambios';
    } else {
        echo "<p style='color:red;'>Registro no encontrado.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jornada_id = $_POST['jornada_id']; 
    $materia_id = $_POST['materia_id'];
    $aula_id = $_POST['aula_id'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Validación: la hora de fin debe ser mayor que la de inicio
    if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
        echo "<p style='color:red;'>La hora de fin debe ser mayor que la hora de inicio.</p>";
    } else {
        // Buscar o crear itinerario
        $itinerario_id = null;
        $sql_it = "SELECT id_itinerario FROM itinerario WHERE hora_inicio = '$hora_inicio' AND hora_fin = '$hora_fin'";
        $result_it = $conn->query($sql_it);
        if ($result_it && $result_it->num_rows > 0) {
            $row_it = $result_it->fetch_assoc();
            $itinerario_id = $row_it['id_itinerario'];
        } else {
            $conn->query("INSERT INTO itinerario (hora_inicio, hora_fin) VALUES ('$hora_inicio', '$hora_fin')");
            $itinerario_id = $conn->insert_id;
        }

        // Obtener profesor_id de la materia seleccionada
        $profesor_id = '';
        if (!empty($materia_id)) {
            $sql_prof = "SELECT profesor_id FROM materias WHERE id_materia = $materia_id";
            $result_prof = $conn->query($sql_prof);
            if ($result_prof && $result_prof->num_rows > 0) {
                $row_prof = $result_prof->fetch_assoc();
                $profesor_id = $row_prof['profesor_id'];
            }
        }

        // Lógica para evitar superposición de reservas de aula
        $sql_check = "
        SELECT d.id_dia, m.nombre AS materia, p.nombre AS profesor, p.apellido AS apellido, i.hora_inicio, i.hora_fin
        FROM dias d
        JOIN itinerario i ON d.itinerario_id = i.id_itinerario
        JOIN materias m ON d.materia_id = m.id_materia
        JOIN profesores p ON d.profesor_id = p.id_profesor
        WHERE d.aula_id = $aula_id
          AND d.jornada_id = $jornada_id
          AND (
                (i.hora_inicio < '$hora_fin' AND i.hora_fin > '$hora_inicio')
              )
        ";
        $result_check = $conn->query($sql_check);
        if ($result_check && $result_check->num_rows > 0) {
            $row = $result_check->fetch_assoc();
            $horario_ocupado = $row['hora_inicio'] . ' - ' . $row['hora_fin'];
            $materia_ocupada = $row['materia'];
            $profe_ocupado = $row['profesor'] . ' ' . $row['apellido'];
            echo "<p style='color:red;'>No es posible reservar el aula: ya está ocupada por $materia_ocupada ($profe_ocupado) en el horario $horario_ocupado. Intente con otro horario.</p>";
        } else if (isset($_POST['id_dia']) && !empty($_POST['id_dia'])) {
            $id_dia = $_POST['id_dia'];
            $sql = "UPDATE dias SET jornada_id = '$jornada_id', itinerario_id = $itinerario_id, materia_id = $materia_id, aula_id = $aula_id, profesor_id = $profesor_id WHERE id_dia = $id_dia";
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color:green;'>Registro actualizado exitosamente.</p>";
            } else {
                echo "<p style='color:red;'>Error al actualizar el registro: " . $conn->error . "</p>";
            }
        } else {
            if (empty($profesor_id)) {
                echo "<p style='color:red;'>No se puede guardar la disposición: la materia seleccionada no tiene profesor asignado.</p>";
            } else {
                $sql = "INSERT INTO dias (jornada_id, itinerario_id, materia_id, aula_id, profesor_id) VALUES ('$jornada_id', $itinerario_id, $materia_id, $aula_id, $profesor_id)";
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
    }
}

$jornada_options = $conn->query("SELECT id_jornada, dias FROM jornada");
$aulas_options = $conn->query("SELECT id_aula, numero FROM aulas");
$materias_options = $conn->query("SELECT id_materia, nombre, carrera_id FROM materias");
$itinerarios_options = $conn->query("SELECT id_itinerario, hora_inicio, hora_fin FROM itinerario");
$profesores_options = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores");
$carreras_options = $conn->query("SELECT id_carrera, nombre FROM carreras");
$cursos_pre_admision_options = $conn->query("SELECT id_curso_pre_admision, nombre_curso FROM cursos_pre_admisiones");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $form_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/añadir.css">
    <style>
        .select-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: nowrap;
            width: 100%;
        }
        .select-container select {
            flex: 1 1 auto;
            min-width: 350px;
            max-width: 500px;
            font-size: 1.13em;
            height: 48px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .select-container .btn-action {
            flex: 0 0 auto;
            width: 48px !important;
            min-width: 48px !important;
            max-width: 48px !important;
            height: 48px !important;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        .select-container input[type="text"] {
            width: 100%;
            min-width: 0;
        }
        .btn-action {
            padding: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 0;
            width: 40px;
            min-width: 40px;
            max-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
        }
        .btn-edit {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            position: relative;
        }
        #profesores-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .profesor-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f3f6fa;
            cursor: pointer;
            transition: background 0.2s;
            border: 1px solid #e0e0e0;
        }
        .profesor-row:hover {
            background: #d0e7ff;
        }
        .profesor-nombre {
            font-size: 1.1em;
            font-weight: 500;
            color: #222;
        }
        .btn-select-profesor {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 14px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-select-profesor:hover {
            background: #0056b3;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .profesor-actions {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }
        .profesor-actions .btn-action {
            flex: 1;
            padding: 8px 12px;
            font-size: 12px;
        }
        select:disabled {
            background-color: #f5f5f5;
            color: #666;
            cursor: not-allowed;
        }
        /* Modal de profesor: unificado y profesional */
        #modal-profesor {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.25);
            align-items: center;
            justify-content: center;
        }
        #modal-profesor .modal-content {
            background: #fff;
            padding: 32px 28px 24px 28px;
            border-radius: 14px;
            min-width: 320px;
            max-width: 95vw;
            box-shadow: 0 8px 32px rgba(30,64,175,0.13);
            margin: 0 auto;
            position: relative;
        }
        #modal-profesor h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 18px;
        }
        #modal-profesor .form-group label {
            font-weight: 600;
            color: #222;
        }
        #modal-profesor .form-group input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #b0c4de;
            border-radius: 6px;
            font-size: 1.08em;
            margin-bottom: 8px;
            background: #f7faff;
        }
        #modal-profesor .acciones-modal {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        #modal-profesor .acciones-modal button {
            border: none;
            border-radius: 6px;
            padding: 9px 22px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
        }
        #modal-profesor .acciones-modal .btn-edit {
            background: #ffc107;
            color: #222;
        }
        #modal-profesor .acciones-modal .btn-edit:hover {
            background: #ffb300;
        }
        #modal-profesor .acciones-modal button[type="button"] {
            background: #f5f5f5;
            color: #222;
            border: 1px solid #b0c4de;
        }
        #modal-profesor .acciones-modal button[type="button"]:hover {
            background: #e3eefd;
        }
    </style>
</head>
<body>
    <a href="profesores.php" target="_blank" class="icono-profesores" title="Ver Profesores" style="position: fixed; top: 40px; right: 30px; background: #007bff; color: #fff; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; font-size: 2em; box-shadow: 0 2px 8px rgba(0,0,0,0.12); z-index: 2100; cursor: pointer; text-decoration: none;">
        👨‍🏫
    </a>
    <div class="form-container">
        <h2><?php echo $form_title; ?></h2>
        <form action="añadir_aula.php" method="POST">
            <?php if (!empty($id_dia)): ?>
                <input type="hidden" name="id_dia" value="<?php echo $id_dia; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="jornada">Jornada:</label>
                <div class="select-container">
                    <select id="jornada_id" name="jornada_id" required>
                        <option value="">Seleccione un día</option>
                        <?php while ($row = $jornada_options->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_jornada']; ?>" <?php echo ($jornada_id == $row['id_jornada']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['dias']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn-action btn-add" onclick="openModal('jornada')">+</button>
                    <button type="button" class="btn-action btn-edit" onclick="editModal('jornada')">✏️</button>
                    <button type="button" class="btn-action btn-delete" onclick="deleteItem('jornada')">🗑️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="itinerario_id">Horario Itinerario:</label>
                <div class="select-container">
                    <input type="time" id="hora_inicio" name="hora_inicio" required value="<?php echo isset($hora_inicio) ? $hora_inicio : ''; ?>">
                    <span style="margin: 0 5px;">a</span>
                    <input type="time" id="hora_fin" name="hora_fin" required value="<?php echo isset($hora_fin) ? $hora_fin : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="aula_id">Aula:</label>
                <div class="select-container">
                    <select id="aula_id" name="aula_id" required>
                        <option value="">Seleccione un aula</option>
                        <?php while ($row = $aulas_options->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_aula']; ?>" <?php echo ($aula_id == $row['id_aula']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['numero']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn-action btn-add" onclick="openModal('aula')">+</button>
                    <button type="button" class="btn-action btn-edit" onclick="editModal('aula')">✏️</button>
                    <button type="button" class="btn-action btn-delete" onclick="deleteItem('aula')">🗑️</button>
                </div>
            </div>

            <!-- Eliminar el select de profesor aquí -->

            <!-- Al final del formulario: Materia y campos de solo lectura -->
            <div class="form-group">
                <label for="materia_id">Materia:</label>
                <div class="select-container">
                    <select id="materia_id" name="materia_id" required>
                        <option value="">Seleccione una materia</option>
                        <?php $materias_options->data_seek(0); while ($row = $materias_options->fetch_assoc()): 
                            $nombre = $row['nombre'];
                            $info = '';
                            // Carrera
                            if (isset($row['carrera_id']) && $row['carrera_id']) {
                                $sql_c = "SELECT nombre FROM carreras WHERE id_carrera = " . intval($row['carrera_id']);
                                $res_c = $conn->query($sql_c);
                                if ($res_c && $row_c = $res_c->fetch_assoc()) {
                                    $info = $row_c['nombre'];
                                }
                            }
                            // Curso pre-admisión
                            elseif (isset($row['curso_pre_admision_id']) && $row['curso_pre_admision_id']) {
                                $sql_cp = "SELECT nombre_curso FROM cursos_pre_admisiones WHERE id_curso_pre_admision = " . intval($row['curso_pre_admision_id']);
                                $res_cp = $conn->query($sql_cp);
                                if ($res_cp && $row_cp = $res_cp->fetch_assoc()) {
                                    $info = $row_cp['nombre_curso'];
                                }
                            }
                            // Diplomatura (si tienes campo diplomatura_id)
                            elseif (isset($row['diplomatura_id']) && $row['diplomatura_id']) {
                                $sql_d = "SELECT nombre FROM carreras WHERE id_carrera = " . intval($row['diplomatura_id']);
                                $res_d = $conn->query($sql_d);
                                if ($res_d && $row_d = $res_d->fetch_assoc()) {
                                    $info = $row_d['nombre'];
                                }
                            }
                            $label = $nombre . ($info ? ' (' . $info . ')' : '');
                        ?>
                        <option value="<?php echo $row['id_materia']; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn-action btn-add" onclick="openModal('materia')">+</button>
                    <button type="button" class="btn-action btn-edit" onclick="editModal('materia')">✏️</button>
                    <button type="button" class="btn-action btn-delete" onclick="deleteItem('materia')">🗑️</button>
                </div>
            </div>
            <div class="form-group" id="carrera_group" style="display:none;">
                <label id="carrera_label" for="carrera_readonly">Carrera:</label>
                <input type="text" id="carrera_readonly" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group" id="curso_group" style="display:none;">
                <label for="curso_readonly">Curso Pre-Admisión:</label>
                <input type="text" id="curso_readonly" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group" id="diplomatura_group" style="display:none;">
                <label for="diplomatura_readonly">Diplomatura:</label>
                <input type="text" id="diplomatura_readonly" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group" id="profesor_group" style="display:none;">
                <label for="profesor_readonly">Profesor:</label>
                <div class="select-container" style="display: flex; align-items: center; gap: 10px; padding: 0; background: none;">
                    <input type="text" id="profesor_readonly" readonly style="background:#f5f5f5; flex:1; min-width:0;">
                    <button type="button" id="btn-add-profesor" class="btn-action btn-add" style="width:40px;" onclick="abrirModalProfesor()">+</button>
                    <button type="button" id="btn-select-profesor" class="btn-action btn-edit" style="display:none; width:40px;" onclick="openSelectProfesorModal()">🔍</button>
                    <button type="button" id="btn-edit-profesor" class="btn-action btn-edit" style="display:none; width:40px;" onclick="editModal('profesor')">✏️</button>
                    <button type="button" id="btn-delete-profesor" class="btn-action btn-delete" style="display:none; width:40px;" onclick="removeProfesorMain()">🗑️</button>
                </div>
            </div>

            <!-- Modal para seleccionar profesor existente -->
            <div id="selectProfesorModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeSelectProfesorModal()">&times;</span>
                    <h3>Seleccionar Profesor</h3>
                    <div id="profesores-list">
                        <!-- Aquí se cargará la lista de profesores -->
                    </div>
                </div>
            </div>

            <!-- Modal para agregar profesor -->
            <div class="modal" id="modal-profesor">
                <div class="modal-content">
                    <h3 id="modal-titulo-prof">Agregar Profesor</h3>
                    <form id="form-profesor">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" id="modal-nombre-prof" required>
                        </div>
                        <div class="form-group">
                            <label>Apellido:</label>
                            <input type="text" name="apellido" id="modal-apellido-prof" required>
                        </div>
                        <div class="form-group">
                            <label>Correo:</label>
                            <input type="email" name="correo" id="modal-correo-prof" placeholder="dejar vacío en caso de no tener/usar">
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" id="modal-telefono-prof" placeholder="dejar vacío en caso de no tener/usar">
                        </div>
                        <div class="acciones-modal">
                            <button type="submit" class="btn-edit">Guardar</button>
                            <button type="button" onclick="cerrarModalProfesor()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="form-group">
                <button type="submit"><?php echo $submit_label; ?></button>
            </div>
        </form>
        <a href="disposicionaulica.php" class="back-button">Volver al Listado</a>
    </div>

    <!-- Modal para agregar/editar -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modal-title">Agregar Nuevo</h3>
            <form id="modal-form">
                <input type="hidden" id="modal-type" name="type">
                <input type="hidden" id="modal-id" name="id">
                
                <div id="modal-fields">
                    <!-- Los campos se cargarán dinámicamente -->
                </div>
                
                <button type="submit" class="btn-submit">Guardar</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#materia_id, #jornada_id, #aula_id').select2({
            placeholder: "Seleccione una opción",
            width: '100%'
        });
    });

    function openModal(type) {
        document.getElementById('modal-type').value = type;
        document.getElementById('modal-id').value = '';
        document.getElementById('modal-title').textContent = 'Agregar Nuevo ' + getTypeName(type);
        
        let fields = '';
        switch(type) {
            case 'jornada':
                fields = '<div class="form-group"><label>Día:</label><input type="text" name="dias" required></div>';
                break;
            case 'itinerario':
                fields = '<div class="form-group"><label>Horario:</label><input type="time" name="horario" required></div>';
                break;
            case 'materia':
                fields = '<div class="form-group"><label>Nombre:</label><input type="text" name="nombre" required></div>' +
                        '<div class="form-group"><label>Carrera:</label><select name="carrera_id" disabled><option value="">Seleccione una carrera</option><?php $carreras_options->data_seek(0); while ($row = $carreras_options->fetch_assoc()): ?><option value="<?php echo $row["id_carrera"]; ?>"><?php echo htmlspecialchars($row["nombre"]); ?></option><?php endwhile; ?></select></div>' +
                        '<div class="form-group"><label>Curso Pre-Admisión:</label><select name="curso_pre_admision_id" disabled><option value="">Seleccione un curso</option><?php $cursos_pre_admision_options->data_seek(0); while ($row = $cursos_pre_admision_options->fetch_assoc()): ?><option value="<?php echo $row["id_curso_pre_admision"]; ?>"><?php echo htmlspecialchars($row["nombre_curso"]); ?></option><?php endwhile; ?></select></div>' +
                        '<div class="form-group"><label>Profesor:</label><select name="profesor_id" disabled><option value="">Se debe asignar un profesor</option><?php $profesores_options->data_seek(0); while ($row = $profesores_options->fetch_assoc()): ?><option value="<?php echo $row["id_profesor"]; ?>"><?php echo htmlspecialchars($row["nombre"] . " " . $row["apellido"]); ?></option><?php endwhile; ?></select></div>' +
                        '<div class="form-group"><label>Acciones Profesor:</label><div class="profesor-actions"><button type="button" class="btn-action btn-edit" onclick="editProfesor()">✏️ Editar</button><button type="button" class="btn-action btn-delete" onclick="removeProfesor()">🗑️ Eliminar</button></div></div>';
                break;
            case 'aula':
                fields = '<div class="form-group"><label>Número:</label><input type="text" name="numero" required></div>' +
                        '<div class="form-group"><label>Piso:</label><input type="number" name="piso" required></div>' +
                        '<div class="form-group"><label>Cantidad:</label><input type="number" name="cantidad" required></div>';
                break;
            case 'profesor':
                fields = '<div class="form-group"><label>Nombre:</label><input type="text" name="nombre" required></div>' +
                        '<div class="form-group"><label>Apellido:</label><input type="text" name="apellido" required></div>' +
                        '<div class="form-group"><label>Correo:</label><input type="email" name="correo" required></div>' +
                        '<div class="form-group"><label>Teléfono:</label><input type="tel" name="telefono" required></div>';
                break;
        }
        
                        document.getElementById('modal-fields').innerHTML = fields;
                
                // Aplicar lógica de bloqueo mutuo para materias
                if (type === 'materia') {
                    setTimeout(function() {
                        setupMateriaFieldLogic();
                    }, 100);
                }
                
                document.getElementById('modal').style.display = 'block';
    }

    function editModal(type) {
        const select = document.getElementById(type + '_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!select.value) {
            alert('Por favor seleccione un elemento para editar');
            return;
        }
        
        document.getElementById('modal-type').value = type;
        document.getElementById('modal-id').value = select.value;
        document.getElementById('modal-title').textContent = 'Editar ' + getTypeName(type);
        
        // Cargar datos actuales via AJAX
        $.ajax({
            url: 'get_item_data.php',
            type: 'POST',
            data: {
                type: type,
                id: select.value
            },
            success: function(response) {
                const data = JSON.parse(response);
                let fields = '';
                
                switch(type) {
                    case 'jornada':
                        fields = '<div class="form-group"><label>Día:</label><input type="text" name="dias" value="' + data.dias + '" required></div>';
                        break;
                    case 'itinerario':
                        fields = '<div class="form-group"><label>Horario:</label><input type="time" name="horario" value="' + data.horario + '" required></div>';
                        break;
                    case 'materia':
                        fields = '<div class="form-group"><label>Nombre:</label><input type="text" name="nombre" id="modal-materia-nombre" value="' + (data.nombre ? data.nombre : '') + '" required></div>' +
                                '<div class="form-group"><label>Carrera:</label><select name="carrera_id" disabled><option value="">Seleccione una carrera</option><?php $carreras_options->data_seek(0); while ($row = $carreras_options->fetch_assoc()): ?><option value="<?php echo $row["id_carrera"]; ?>"><?php echo htmlspecialchars($row["nombre"]); ?></option><?php endwhile; ?></select></div>' +
                                '<div class="form-group"><label>Curso Pre-Admisión:</label><select name="curso_pre_admision_id" disabled><option value="">Seleccione un curso</option><?php $cursos_pre_admision_options->data_seek(0); while ($row = $cursos_pre_admision_options->fetch_assoc()): ?><option value="<?php echo $row["id_curso_pre_admision"]; ?>"><?php echo htmlspecialchars($row["nombre_curso"]); ?></option><?php endwhile; ?></select></div>' +
                                '<div class="form-group"><label>Profesor:</label><select name="profesor_id" disabled><option value="">Se debe asignar un profesor</option><?php $profesores_options->data_seek(0); while ($row = $profesores_options->fetch_assoc()): ?><option value="<?php echo $row["id_profesor"]; ?>"><?php echo htmlspecialchars($row["nombre"] . " " . $row["apellido"]); ?></option><?php endwhile; ?></select></div>' +
                                '<div class="form-group"><label>Acciones Profesor:</label><div class="profesor-actions"><button type="button" class="btn-action btn-edit" onclick="editProfesor()">✏️ Editar</button><button type="button" class="btn-action btn-delete" onclick="removeProfesor()">🗑️ Eliminar</button></div></div>';
                        break;
                    case 'aula':
                        fields = '<div class="form-group"><label>Número:</label><input type="text" name="numero" value="' + data.numero + '" required></div>' +
                                '<div class="form-group"><label>Piso:</label><input type="number" name="piso" value="' + data.piso + '" required></div>' +
                                '<div class="form-group"><label>Cantidad:</label><input type="number" name="cantidad" value="' + data.cantidad + '" required></div>';
                        break;
                    case 'profesor':
                        fields = '<div class="form-group"><label>Nombre:</label><input type="text" name="nombre" value="' + data.nombre + '" required></div>' +
                                '<div class="form-group"><label>Apellido:</label><input type="text" name="apellido" value="' + data.apellido + '" required></div>' +
                                '<div class="form-group"><label>Correo:</label><input type="email" name="correo" value="' + data.correo + '" required></div>' +
                                '<div class="form-group"><label>Teléfono:</label><input type="tel" name="telefono" value="' + data.telefono + '" required></div>';
                        break;
                }
                
                document.getElementById('modal-fields').innerHTML = fields;
                
                // Establecer valores seleccionados para materias
                if (type === 'materia') {
                    setTimeout(function() {
                        if (data.carrera_id) {
                            document.querySelector('select[name="carrera_id"]').value = data.carrera_id;
                        }
                        if (data.curso_pre_admision_id) {
                            document.querySelector('select[name="curso_pre_admision_id"]').value = data.curso_pre_admision_id;
                        }
                        if (data.profesor_id && data.profesor_id !== null) {
                            document.querySelector('select[name="profesor_id"]').value = data.profesor_id;
                        } else {
                            // Si no tiene profesor, mostrar el mensaje por defecto
                            document.querySelector('select[name="profesor_id"]').value = '';
                        }
                        
                        // Aplicar lógica de bloqueo mutuo
                        setupMateriaFieldLogic();
                        
                        // Aplicar estado inicial basado en los datos existentes
                        if (data.carrera_id && data.carrera_id !== null) {
                            // Si tiene carrera, bloquear curso pre-admisión
                            const cursoSelect = document.querySelector('select[name="curso_pre_admision_id"]');
                            if (cursoSelect) {
                                cursoSelect.disabled = true;
                                cursoSelect.style.backgroundColor = '#f5f5f5';
                                cursoSelect.style.cursor = 'not-allowed';
                            }
                        } else if (data.curso_pre_admision_id && data.curso_pre_admision_id !== null) {
                            // Si tiene curso pre-admisión, bloquear carrera
                            const carreraSelect = document.querySelector('select[name="carrera_id"]');
                            if (carreraSelect) {
                                carreraSelect.disabled = true;
                                carreraSelect.style.backgroundColor = '#f5f5f5';
                                carreraSelect.style.cursor = 'not-allowed';
                            }
                        }
                        
                        // Aplicar estado inicial del profesor
                        const profesorSelect = document.querySelector('select[name="profesor_id"]');
                        if (profesorSelect) {
                            if (data.profesor_id && data.profesor_id !== null) {
                                // Si tiene profesor, mantener habilitado para edición
                                profesorSelect.disabled = false;
                                profesorSelect.style.backgroundColor = '';
                                profesorSelect.style.cursor = '';
                            } else {
                                // Si no tiene profesor, mantener deshabilitado
                                profesorSelect.disabled = true;
                                profesorSelect.style.backgroundColor = '#f5f5f5';
                                profesorSelect.style.cursor = 'not-allowed';
                            }
                        }
                    }, 100);
                }
                
                document.getElementById('modal').style.display = 'block';
            },
            error: function() {
                alert('Error al cargar los datos');
            }
        });
    }

    function deleteItem(type) {
        const select = document.getElementById(type + '_id');
        
        if (!select.value) {
            alert('Por favor seleccione un elemento para eliminar');
            return;
        }
        
        if (confirm('¿Está seguro de que desea eliminar este elemento?')) {
            $.ajax({
                url: 'delete_item.php',
                type: 'POST',
                data: {
                    type: type,
                    id: select.value
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Elemento eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error al eliminar: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error al eliminar el elemento');
                }
            });
        }
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    function getTypeName(type) {
        const names = {
            'jornada': 'Jornada',
            'itinerario': 'Horario',
            'materia': 'Materia',
            'aula': 'Aula',
            'profesor': 'Profesor'
        };
        return names[type] || type;
    }

    // Manejar envío del formulario modal
    document.getElementById('modal-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'save_item.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    // Si se guardó un profesor nuevo, asociarlo a la materia seleccionada
                    if (document.getElementById('modal-type').value === 'profesor' && result.new_profesor_id) {
                        var materiaId = $('#materia_id').val();
                        if (materiaId) {
                            $.ajax({
                                url: 'save_item.php',
                                type: 'POST',
                                data: { type: 'materia', id: materiaId, profesor_id: result.new_profesor_id },
                                success: function(resp2) {
                                    closeModal();
                                    $('#materia_id').trigger('change');
                                }
                            });
                            return;
                        }
                    }
                    alert('Elemento guardado exitosamente');
                    closeModal();
                    if (document.getElementById('modal-type').value === 'profesor' || document.getElementById('modal-type').value === 'materia') {
                        $('#materia_id').trigger('change');
                    }
                } else {
                    alert('Error al guardar: ' + result.message);
                }
            },
            error: function() {
                alert('Error al guardar el elemento');
            }
        });
    });

    // Cerrar modal al hacer clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // Función para manejar la lógica de bloqueo mutuo entre carrera y curso pre-admisión
    function setupMateriaFieldLogic() {
        const carreraSelect = document.querySelector('select[name="carrera_id"]');
        const cursoSelect = document.querySelector('select[name="curso_pre_admision_id"]');
        
        if (!carreraSelect || !cursoSelect) return;
        
        // Función para actualizar el estado de los campos
        function updateFieldStates() {
            const carreraValue = carreraSelect.value;
            const cursoValue = cursoSelect.value;
            
            if (carreraValue && carreraValue !== '') {
                // Si se seleccionó una carrera, bloquear curso pre-admisión
                cursoSelect.disabled = true;
                cursoSelect.value = '';
                cursoSelect.style.backgroundColor = '#f5f5f5';
                cursoSelect.style.cursor = 'not-allowed';
            } else if (cursoValue && cursoValue !== '') {
                // Si se seleccionó un curso pre-admisión, bloquear carrera
                carreraSelect.disabled = true;
                carreraSelect.value = '';
                carreraSelect.style.backgroundColor = '#f5f5f5';
                carreraSelect.style.cursor = 'not-allowed';
            } else {
                // Si no hay selección, habilitar ambos
                carreraSelect.disabled = false;
                cursoSelect.disabled = false;
                carreraSelect.style.backgroundColor = '';
                cursoSelect.style.backgroundColor = '';
                carreraSelect.style.cursor = '';
                cursoSelect.style.cursor = '';
            }
        }
        
        // Aplicar estado inicial
        updateFieldStates();
        
        // Agregar event listeners
        carreraSelect.addEventListener('change', updateFieldStates);
        cursoSelect.addEventListener('change', updateFieldStates);
    }

    // Función para editar profesor
    function editProfesor() {
        const profesorSelect = document.querySelector('select[name="profesor_id"]');
        if (profesorSelect) {
            profesorSelect.disabled = false;
            profesorSelect.style.backgroundColor = '';
            profesorSelect.style.cursor = '';
            profesorSelect.focus();
        }
    }

    // Función para eliminar profesor
    function removeProfesor() {
        const profesorSelect = document.querySelector('select[name="profesor_id"]');
        if (profesorSelect) {
            profesorSelect.value = '';
            profesorSelect.disabled = true;
            profesorSelect.style.backgroundColor = '#f5f5f5';
            profesorSelect.style.cursor = 'not-allowed';
        }
    }

    // Al cambiar la materia, actualizar los campos de solo lectura
    $(document).ready(function() {
        $('#materia_id').on('change', function() {
            var materiaId = $(this).val();
            // Ocultar todos los campos de solo lectura al cambiar
            $('#carrera_group').hide();
            $('#curso_group').hide();
            $('#diplomatura_group').hide();
            $('#profesor_group').hide();
            $('#btn-add-profesor').hide();
            $('#btn-edit-profesor').hide();
            $('#btn-delete-profesor').hide();
            $('#btn-select-profesor').hide();
            if (materiaId) {
                $.ajax({
                    url: 'get_item_data.php',
                    type: 'POST',
                    data: { type: 'materia', id: materiaId },
                    success: function(response) {
                        var data = JSON.parse(response);
                        // Mostrar solo el campo correspondiente
                        if (data.carrera_id && data.carrera_id !== 'null') {
                            $.ajax({
                                url: 'get_item_data.php',
                                type: 'POST',
                                data: { type: 'carrera', id: data.carrera_id },
                                success: function(resp2) {
                                    var carrera = JSON.parse(resp2);
                                    // Si el nombre contiene 'Diplomatura', mostrar como diplomatura
                                    if (carrera.nombre && carrera.nombre.toLowerCase().includes('diplomatura')) {
                                        $('#carrera_label').text('Diplomatura:');
                                    } else {
                                        $('#carrera_label').text('Carrera:');
                                    }
                                    $('#carrera_readonly').val(carrera.nombre);
                                    $('#carrera_group').show();
                                }
                            });
                        } else if (data.curso_pre_admision_id && data.curso_pre_admision_id !== 'null') {
                            $.ajax({
                                url: 'get_item_data.php',
                                type: 'POST',
                                data: { type: 'curso_pre_admision', id: data.curso_pre_admision_id },
                                success: function(resp3) {
                                    var curso = JSON.parse(resp3);
                                    $('#curso_readonly').val(curso.nombre_curso);
                                    $('#curso_group').show();
                                }
                            });
                        } else if (data.carrera_id === null && data.curso_pre_admision_id === null && data.diplomatura_id && data.diplomatura_id !== 'null') {
                            // Si tiene diplomatura
                            $.ajax({
                                url: 'get_item_data.php',
                                type: 'POST',
                                data: { type: 'diplomatura', id: data.diplomatura_id },
                                success: function(resp4) {
                                    var diplo = JSON.parse(resp4);
                                    $('#diplomatura_readonly').val(diplo.nombre);
                                    $('#diplomatura_group').show();
                                }
                            });
                        }
                        // Profesor (siempre mostrar si hay materia)
                        if (data.profesor_id && data.profesor_id !== 'null') {
                            $.ajax({
                                url: 'get_item_data.php',
                                type: 'POST',
                                data: { type: 'profesor', id: data.profesor_id },
                                success: function(resp5) {
                                    var profe = JSON.parse(resp5);
                                    $('#profesor_readonly').val(profe.nombre + ' ' + profe.apellido);
                                    $('#profesor_group').show();
                                    $('#btn-add-profesor').hide();
                                    $('#btn-edit-profesor').show();
                                    $('#btn-delete-profesor').show();
                                    $('#btn-select-profesor').hide();
                                }
                            });
                        } else {
                            $('#profesor_readonly').val('Se debe asignar un profesor');
                            $('#profesor_group').show();
                            $('#btn-add-profesor').show();
                            $('#btn-edit-profesor').hide();
                            $('#btn-delete-profesor').hide();
                            $('#btn-select-profesor').show();
                        }
                    }
                });
            } else {
                $('#carrera_readonly').val('');
                $('#curso_readonly').val('');
                $('#diplomatura_readonly').val('');
                $('#profesor_readonly').val('Se debe asignar un profesor');
                $('#btn-add-profesor').hide();
                $('#btn-edit-profesor').hide();
                $('#btn-delete-profesor').hide();
                $('#btn-select-profesor').hide();
            }
        });
        // Disparar el evento al cargar la página si ya hay materia seleccionada
        $('#materia_id').trigger('change');
    });

    // Eliminar profesor desde el formulario principal
    function removeProfesorMain() {
        var materiaId = $('#materia_id').val();
        if (!materiaId) return;
        if (confirm('¿Está seguro de que desea eliminar el profesor asignado a esta materia?')) {
            $.ajax({
                url: 'save_item.php',
                type: 'POST',
                data: { type: 'materia', id: materiaId, profesor_id: '' },
                success: function(resp) {
                    $('#materia_id').trigger('change');
                }
            });
        }
    }

    // Función para abrir el modal de selección de profesor
    function openSelectProfesorModal() {
        // Cargar la lista de profesores existentes vía AJAX
        $.ajax({
            url: 'get_item_data.php',
            type: 'POST',
            data: { type: 'all_profesores', id: 0 },
            success: function(response) {
                var data = JSON.parse(response);
                var html = '';
                if (data.length > 0) {
                    data.forEach(function(prof) {
                        html += '<div class="profesor-row" onclick="asignarProfesorExistente(' + prof.id_profesor + ', \'' + prof.nombre + ' ' + prof.apellido + '\')">'
                            + '<span class="profesor-nombre">' + prof.nombre + ' ' + prof.apellido + '</span>'
                            + '<button type="button" class="btn-select-profesor" onclick="event.stopPropagation(); asignarProfesorExistente(' + prof.id_profesor + ', \'' + prof.nombre + ' ' + prof.apellido + '\')">Seleccionar</button>'
                            + '</div>';
                    });
                } else {
                    html += '<div style="padding:10px;">No hay profesores registrados.</div>';
                }
                $('#profesores-list').html(html);
                $('#selectProfesorModal').show();
            }
        });
    }
    function closeSelectProfesorModal() {
        $('#selectProfesorModal').hide();
    }
    function asignarProfesorExistente(id, nombre) {
        var materiaId = $('#materia_id').val();
        if (!materiaId) return;
        $.ajax({
            url: 'save_item.php',
            type: 'POST',
            data: { type: 'materia', id: materiaId, profesor_id: id },
            success: function(resp) {
                closeSelectProfesorModal();
                $('#materia_id').trigger('change');
            }
        });
    }

    // Abrir modal de profesor desde el botón +
    function abrirModalProfesor() {
        document.getElementById('modal-titulo-prof').textContent = 'Agregar Profesor';
        var form = document.getElementById('form-profesor');
        if (form) form.reset();
        document.getElementById('modal-profesor').style.display = 'flex';
    }
    function cerrarModalProfesor() {
        document.getElementById('modal-profesor').style.display = 'none';
    }
    // Guardar profesor vía AJAX y actualizar select
    $('#form-profesor').on('submit', function(e) {
        e.preventDefault();
        var datos = $(this).serialize() + '&action=add';
        $.post('profesores.php', datos, function() {
            // Actualizar el select de profesores
            $.get('get_item_data.php', { type: 'all_profesores', id: 0 }, function(data) {
                var profs = JSON.parse(data);
                var $select = $('#profesor_id');
                if ($select.length) {
                    $select.empty();
                    $select.append('<option value="">Seleccione un profesor</option>');
                    profs.forEach(function(p) {
                        $select.append('<option value="' + p.id_profesor + '">' + p.nombre + ' ' + p.apellido + '</option>');
                    });
                }
            });
            cerrarModalProfesor();
        });
    });
    </script>
</body>
</html>