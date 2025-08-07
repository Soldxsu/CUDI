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
        body {
            background: #f4f7fb;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .form-container {
            max-width: 700px;
            margin: 48px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(30, 64, 175, 0.13);
            padding: 44px 38px 38px 38px;
        }
        .form-container h2 {
            font-size: 2.2em;
            color: #1a237e;
            font-weight: 800;
            margin-bottom: 32px;
            letter-spacing: 1px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 28px;
        }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 700;
            color: #263238;
            font-size: 1.13em;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 13px 12px;
            border: 1.5px solid #b0c4de;
            border-radius: 7px;
            font-size: 1.13em;
            background: #f7faff;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus, .form-group select:focus {
            border: 1.5px solid #0074ff;
            outline: none;
            box-shadow: 0 0 0 2px #e3eefd;
        }
        .select-container {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: nowrap;
            width: 100%;
        }
        .select-container select {
            min-width: 220px;
            max-width: 400px;
            font-size: 1.13em;
            height: 48px;
        }
        .btn-action {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: none;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0074ff 60%, #28a745 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            cursor: pointer;
            transition: background 0.18s, box-shadow 0.18s;
            position: relative;
        }
        .btn-action:hover {
            background: linear-gradient(135deg, #0056b3 60%, #218838 100%);
            box-shadow: 0 4px 16px rgba(40,167,69,0.18);
        }
        .btn-action[title]:hover:after {
            content: attr(title);
            position: absolute;
            left: 50%;
            top: 110%;
            transform: translateX(-50%);
            background: #222;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.98em;
            white-space: nowrap;
            z-index: 100;
        }
        .btn-edit, .btn-delete, .btn-add {
            font-size: 1em;
            font-weight: 600;
        }
        .btn-edit {
            background: #ffc107;
            color: #222;
        }
        .btn-edit:hover {
            background: #ffb300;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background: #b71c1c;
        }
        .btn-add {
            background: #28a745;
            color: #fff;
        }
        .btn-add:hover {
            background: #218838;
        }
        
        /* Asegurar que los botones de acción sean visibles */
        .select-container .btn-action {
            position: relative !important;
            z-index: 1000 !important;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Estilo específico para el contenedor de materias */
        .form-group:has(#materia_id) .select-container {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
        }
        .form-group input[type="time"] {
            max-width: 180px;
        }
        .acciones-modal {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            justify-content: flex-end;
        }
        .acciones-modal button {
            border: none;
            border-radius: 7px;
            padding: 11px 28px;
            font-size: 1.08em;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s;
        }
        .acciones-modal .btn-edit {
            background: #ffc107;
            color: #222;
        }
        .acciones-modal .btn-edit:hover {
            background: #ffb300;
        }
        .acciones-modal button[type="button"] {
            background: #f5f5f5;
            color: #222;
            border: 1px solid #b0c4de;
        }
        .acciones-modal button[type="button"]:hover {
            background: #e3eefd;
        }
        .back-button {
            display: block;
            margin: 36px auto 0 auto;
            max-width: 350px;
            background: #6c757d;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 7px;
            padding: 15px 0;
            font-size: 1.13em;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background 0.18s;
        }
        .back-button:hover {
            background: #495057;
        }
        @media (max-width: 900px) {
            .form-container { padding: 18px 2vw; }
        }
        @media (max-width: 600px) {
            .form-container { max-width: 100vw; border-radius: 0; box-shadow: none; padding: 8vw 2vw; }
        }
        .mensaje-estado {
            padding: 14px 18px;
            border-radius: 7px;
            font-size: 1.08em;
            margin-bottom: 22px;
            font-weight: 600;
            text-align: center;
        }
        .mensaje-estado.error {
            background: #ffeaea;
            color: #b71c1c;
            border: 1.5px solid #dc3545;
        }
        .mensaje-estado.success {
            background: #e3fcec;
            color: #1b5e20;
            border: 1.5px solid #28a745;
        }
        #modal {
            display: none;
            position: fixed;
            z-index: 2100;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.25);
            align-items: center;
            justify-content: center;
        }
        #modal .modal-content {
            background: #fff;
            padding: 32px 28px 24px 28px;
            border-radius: 14px;
            min-width: 320px;
            max-width: 95vw;
            box-shadow: 0 8px 32px rgba(30,64,175,0.13);
            margin: 0 auto;
            position: relative;
        }
        #modal h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 18px;
        }
        #modal .form-group label {
            font-weight: 600;
            color: #222;
        }
        #modal .form-group input, #modal .form-group select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #b0c4de;
            border-radius: 6px;
            font-size: 1.08em;
            margin-bottom: 8px;
            background: #f7faff;
        }
        #modal .acciones-modal, #modal-form .acciones-modal {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            justify-content: flex-end;
        }
        #modal .acciones-modal button, #modal-form .acciones-modal button, #modal-form button[type='submit'] {
            border: none;
            border-radius: 7px;
            padding: 11px 28px;
            font-size: 1.08em;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s;
        }
        #modal .acciones-modal .btn-edit, #modal-form .acciones-modal .btn-edit, #modal-form button[type='submit'] {
            background: #ffc107;
            color: #222;
        }
        #modal .acciones-modal .btn-edit:hover, #modal-form .acciones-modal .btn-edit:hover, #modal-form button[type='submit']:hover {
            background: #ffb300;
        }
        #modal .acciones-modal button[type="button"], #modal-form .acciones-modal button[type="button"] {
            background: #f5f5f5;
            color: #222;
            border: 1px solid #b0c4de;
        }
        #modal .acciones-modal button[type="button"]:hover, #modal-form .acciones-modal button[type="button"]:hover {
            background: #e3eefd;
        }
        #modal .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 18px;
            top: 10px;
        }
        #modal .close:hover {
            color: #1a237e;
        }
    </style>
</head>
<body>
    <!-- Iconos apilados a la derecha como links -->
    <div style="position: absolute; top: 32px; right: 48px; display: flex; flex-direction: column; align-items: center; gap: 18px; z-index: 10;">
        <a href="materias.php" style="text-decoration: none;">
            <div style="width: 70px; height: 70px; background: #0074ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                <span style="font-size: 38px;">📚</span>
            </div>
        </a>
        <a href="profesores.php" style="text-decoration: none;">
            <div style="width: 70px; height: 70px; background: #0074ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            <span style="font-size: 38px;">👨‍🏫</span>            </div>
        </a>
    </div>
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

            <div class="form-group">
                <label for="materia_id">Materia:</label>
                <div class="select-container">
                    <select id="materia_id" name="materia_id" required onchange="mostrarInfoMateria()">
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
                        <option value="<?php echo $row['id_materia']; ?>" data-carrera="<?php echo isset($row['carrera_id']) ? $row['carrera_id'] : ''; ?>" data-curso="<?php echo isset($row['curso_pre_admision_id']) ? $row['curso_pre_admision_id'] : ''; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" class="btn-action btn-add" onclick="console.log('Botón agregar materia clickeado'); openModal('materia')" title="Agregar Materia">+</button>
                    <button type="button" class="btn-action btn-edit" onclick="console.log('Botón editar materia clickeado'); editModal('materia')" title="Editar Materia">✏️</button>
                    <button type="button" class="btn-action btn-delete" onclick="console.log('Botón eliminar materia clickeado'); deleteItem('materia')" title="Eliminar Materia">🗑️</button>
                </div>
            </div>

            <!-- Campos de solo lectura que aparecen al seleccionar materia -->
            <div class="form-group" id="carrera_group" style="display:none;">
                <label id="carrera_label" for="carrera_readonly">Carrera:</label>
                <input type="text" id="carrera_readonly" readonly style="background:#f8f9fa; border: 1.5px solid #e9ecef; color: #495057; font-weight: 500;">
            </div>
            <div class="form-group" id="curso_group" style="display:none;">
                <label for="curso_readonly">Curso Pre-Admisión:</label>
                <input type="text" id="curso_readonly" readonly style="background:#f8f9fa; border: 1.5px solid #e9ecef; color: #495057; font-weight: 500;">
            </div>
            <div class="form-group" id="profesor_group" style="display:none;">
                <label for="profesor_readonly">Profesor:</label>
                <div class="select-container" style="display: flex; align-items: center; gap: 10px; padding: 0; background: none;">
                    <input type="text" id="profesor_readonly" readonly style="background:#f8f9fa; border: 1.5px solid #e9ecef; color: #495057; font-weight: 500; flex:1; min-width:0;">
                    <button type="button" id="btn-add-profesor" class="btn-action btn-add" style="width:44px; height:44px;" onclick="abrirModalProfesor()" title="Agregar Profesor">+</button>
                    <button type="button" id="btn-select-profesor" class="btn-action btn-edit" style="display:none; width:44px; height:44px;" onclick="openSelectProfesorModal()" title="Seleccionar Profesor">🔍</button>
                    <button type="button" id="btn-edit-profesor" class="btn-action btn-edit" style="display:none; width:44px; height:44px;" onclick="editModal('profesor')" title="Editar Profesor">✏️</button>
                    <button type="button" id="btn-remove-profesor" class="btn-action btn-delete" style="display:none; width:44px; height:44px;" onclick="removeProfesorFromMateria()" title="Desasignar Profesor">🚫</button>
                </div>
            </div>

            <div class="form-group">
                <button type="submit"><?php echo $submit_label; ?></button>
            </div>
        </form>
        <a href="disposicionaulica.php" class="back-button">Volver al Listado</a>
    </div>

    <!-- Modal para agregar profesor -->
    <div class="modal" id="modal-profesor">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalProfesor()">&times;</span>
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
                
                <div class="acciones-modal">
                    <button type="submit" class="btn-submit">Guardar</button>
                    <button type="button" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        console.log('Página cargada, configurando Select2...');
        
        // Verificar si los botones de materia están presentes ANTES de Select2
        const materiaButtonsBefore = document.querySelectorAll('.btn-action[onclick*="materia"]');
        console.log('Botones de materia encontrados ANTES de Select2:', materiaButtonsBefore.length);
        materiaButtonsBefore.forEach((btn, index) => {
            console.log(`Botón ${index + 1} ANTES:`, btn.outerHTML);
        });
        
        $('#jornada_id, #aula_id').select2({
            placeholder: "Seleccione una opción",
            width: '100%'
        });
        
        // Configurar Select2 para materia con un contenedor específico
        $('#materia_id').select2({
            placeholder: "Seleccione una opción",
            width: 'calc(100% - 150px)', // Dejar espacio para los botones
            dropdownParent: $('#materia_id').parent()
        });
        
        // Verificar si los botones de materia están presentes DESPUÉS de Select2
        setTimeout(function() {
            const materiaButtonsAfter = document.querySelectorAll('.btn-action[onclick*="materia"]');
            console.log('Botones de materia encontrados DESPUÉS de Select2:', materiaButtonsAfter.length);
            materiaButtonsAfter.forEach((btn, index) => {
                console.log(`Botón ${index + 1} DESPUÉS:`, btn.outerHTML);
                console.log('Botón visible:', btn.offsetParent !== null);
                console.log('Botón display:', window.getComputedStyle(btn).display);
                console.log('Botón visibility:', window.getComputedStyle(btn).visibility);
            });
        }, 1000);
    });

    // Funciones para los botones de profesor
    function abrirModalProfesor() {
        document.getElementById('modal-titulo-prof').textContent = 'Agregar Profesor';
        document.getElementById('modal-nombre-prof').value = '';
        document.getElementById('modal-apellido-prof').value = '';
        document.getElementById('modal-correo-prof').value = '';
        document.getElementById('modal-telefono-prof').value = '';
        document.getElementById('modal-profesor').style.display = 'flex';
        // Asegurar que el modal esté centrado
        document.getElementById('modal-profesor').style.alignItems = 'center';
        document.getElementById('modal-profesor').style.justifyContent = 'center';
    }

    function cerrarModalProfesor() {
        document.getElementById('modal-profesor').style.display = 'none';
    }

    function openSelectProfesorModal() {
        // Cargar lista de profesores directamente desde get_item_data.php
        $.ajax({
            url: 'get_item_data.php',
            type: 'POST',
            data: { type: 'all_profesores' },
            success: function(response) {
                try {
                    const profesores = JSON.parse(response);
                    let html = '<div style="max-height: 300px; overflow-y: auto;">';
                    if (profesores.length > 0) {
                        profesores.forEach(function(prof) {
                            html += '<div class="profesor-row" onclick="asignarProfesorAMateria(' + prof.id_profesor + ', \'' + prof.nombre + ' ' + prof.apellido + '\')">';
                            html += '<span class="profesor-nombre">' + prof.nombre + ' ' + prof.apellido + '</span>';
                            html += '<button type="button" class="btn-select-profesor" onclick="event.stopPropagation(); asignarProfesorAMateria(' + prof.id_profesor + ', \'' + prof.nombre + ' ' + prof.apellido + '\')">Seleccionar</button>';
                            html += '</div>';
                        });
                    } else {
                        html += '<div style="padding: 20px; text-align: center; color: #666;">No hay profesores registrados.</div>';
                    }
                    html += '</div>';
                    $('#profesores-list').html(html);
                    $('#selectProfesorModal').css('display', 'flex');
                } catch (e) {
                    $('#profesores-list').html('<div style="padding: 20px; text-align: center; color: #666;">Error al cargar profesores.</div>');
                    $('#selectProfesorModal').css('display', 'flex');
                }
            },
            error: function() {
                $('#profesores-list').html('<div style="padding: 20px; text-align: center; color: #666;">Error al cargar profesores.</div>');
                $('#selectProfesorModal').css('display', 'flex');
            }
        });
    }

    function asignarProfesorAMateria(profesorId, profesorNombre) {
        var materiaId = $('#materia_id').val();
        if (!materiaId) {
            alert('Por favor seleccione una materia primero');
            return;
        }
        
        $.ajax({
            url: 'asignar_profesor.php',
            type: 'POST',
            data: { 
                materia_id: materiaId,
                profesor_id: profesorId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#profesor_readonly').val(profesorNombre);
                        $('#btn-add-profesor').hide();
                        $('#btn-select-profesor').hide();
                        $('#btn-edit-profesor').show();
                        $('#btn-remove-profesor').show();
                        closeSelectProfesorModal();
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo asignar el profesor'));
                    }
                } catch (e) {
                    alert('Error al asignar profesor');
                }
            },
            error: function() {
                alert('Error al asignar profesor');
            }
        });
    }

    function closeSelectProfesorModal() {
        document.getElementById('selectProfesorModal').style.display = 'none';
    }

    function removeProfesorMain() {
        if (confirm('¿Está seguro de eliminar el profesor de esta materia?')) {
            var materiaId = $('#materia_id').val();
            $.ajax({
                url: 'remove_profesor.php',
                type: 'POST',
                data: { materia_id: materiaId },
                success: function(response) {
                    mostrarInfoMateria(); // Recargar información
                },
                error: function() {
                    alert('Error al eliminar profesor');
                }
            });
        }
    }

    function openModal(type) {
        console.log('Abriendo modal para:', type);
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
                        '<div class="form-group"><label>Carrera:</label><select name="carrera_id"><option value="">Seleccione una carrera</option><?php $carreras_options->data_seek(0); while ($row = $carreras_options->fetch_assoc()): ?><option value="<?php echo $row["id_carrera"]; ?>"><?php echo htmlspecialchars($row["nombre"]); ?></option><?php endwhile; ?></select></div>' +
                        '<div class="form-group"><label>Curso Pre-Admisión:</label><select name="curso_pre_admision_id"><option value="">Seleccione un curso</option><?php $cursos_pre_admision_options->data_seek(0); while ($row = $cursos_pre_admision_options->fetch_assoc()): ?><option value="<?php echo $row["id_curso_pre_admision"]; ?>"><?php echo htmlspecialchars($row["nombre_curso"]); ?></option><?php endwhile; ?></select></div>';
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
        
        document.getElementById('modal').style.display = 'flex';
    }

    function editModal(type) {
        console.log('Intentando editar:', type);
        let select;
        
        // Caso especial para profesor - no tiene select en el formulario principal
        if (type === 'profesor') {
            // Para profesor, necesitamos obtener el ID del profesor de la materia seleccionada
            const materiaSelect = document.getElementById('materia_id');
            if (!materiaSelect || !materiaSelect.value) {
                alert('Por favor seleccione una materia primero para editar su profesor');
                return;
            }
            
            // Obtener el profesor de la materia seleccionada via AJAX
            $.ajax({
                url: 'get_item_data.php',
                type: 'POST',
                data: {
                    type: 'materia',
                    id: materiaSelect.value
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.profesor_id && data.profesor_id !== null) {
                            // Si la materia tiene profesor, proceder con la edición
                            proceedWithEdit(type, data.profesor_id);
                        } else {
                            alert('Esta materia no tiene un profesor asignado para editar');
                        }
                    } catch (e) {
                        alert('Error al obtener datos de la materia');
                    }
                },
                error: function() {
                    alert('Error al obtener datos de la materia');
                }
            });
            return;
        }
        
        // Para otros tipos, buscar el select correspondiente
        select = document.getElementById(type + '_id');
        
        // Verificar si el select existe
        if (!select) {
            alert('No se encontró el elemento para editar');
            return;
        }
        
        // Verificar si hay un valor seleccionado
        if (!select.value) {
            alert('Por favor seleccione un elemento para editar');
            return;
        }
        
        proceedWithEdit(type, select.value);
    }
    
    function proceedWithEdit(type, id) {
        console.log('Procediendo con edición:', type, id);
        document.getElementById('modal-type').value = type;
        document.getElementById('modal-id').value = id;
        document.getElementById('modal-title').textContent = 'Editar ' + getTypeName(type);
        
        // Cargar datos actuales via AJAX
        $.ajax({
            url: 'get_item_data.php',
            type: 'POST',
            data: {
                type: type,
                id: id
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
                                '<div class="form-group"><label>Curso Pre-Admisión:</label><select name="curso_pre_admision_id" disabled><option value="">Seleccione un curso</option><?php $cursos_pre_admision_options->data_seek(0); while ($row = $cursos_pre_admision_options->fetch_assoc()): ?><option value="<?php echo $row["id_curso_pre_admision"]; ?>"><?php echo htmlspecialchars($row["nombre_curso"]); ?></option><?php endwhile; ?></select></div>';
                        break;
                    case 'aula':
                        fields = '<div class="form-group"><label>Número:</label><input type="text" name="numero" value="' + data.numero + '" required></div>' +
                                '<div class="form-group"><label>Piso:</label><input type="number" name="piso" value="' + data.piso + '" required></div>' +
                                '<div class="form-group"><label>Cantidad:</label><input type="number" name="cantidad" value="' + data.cantidad + '" required></div>';
                        break;
                    case 'profesor':
                        fields = '<div class="form-group"><label>Nombre:</label><input type="text" name="nombre" value="' + (data.nombre || '') + '" required></div>' +
                                '<div class="form-group"><label>Apellido:</label><input type="text" name="apellido" value="' + (data.apellido || '') + '" required></div>' +
                                '<div class="form-group"><label>Correo:</label><input type="email" name="correo" value="' + (data.correo || '') + '" placeholder="dejar vacío en caso de no tener/usar"></div>' +
                                '<div class="form-group"><label>Teléfono:</label><input type="text" name="telefono" value="' + (data.telefono || '') + '" placeholder="dejar vacío en caso de no tener/usar"></div>';
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
                
                document.getElementById('modal').style.display = 'flex';
            },
            error: function() {
                alert('Error al cargar los datos');
            }
        });
    }

    function deleteItem(type) {
        console.log('Intentando eliminar:', type);
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
        console.log('Cerrando modal...');
        document.getElementById('modal').style.display = 'none';
    }

    function getTypeName(type) {
        console.log('Obteniendo nombre para tipo:', type);
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
        
        console.log('Enviando datos al servidor...');
        $.ajax({
            url: 'save_item.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
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
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', xhr, status, error);
                alert('Error al guardar el elemento');
            }
        });
    });

    // Función para manejar la lógica de bloqueo mutuo entre carrera y curso pre-admisión
    function setupMateriaFieldLogic() {
        console.log('Configurando lógica de campos de materia...');
        const carreraSelect = document.querySelector('select[name="carrera_id"]');
        const cursoSelect = document.querySelector('select[name="curso_pre_admision_id"]');
        
        if (!carreraSelect || !cursoSelect) {
            console.log('No se encontraron los selects de carrera o curso');
            return;
        }
        
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
    function mostrarInfoMateria() {
        var materiaId = $('#materia_id').val();
        var carreraId = $('#materia_id').find('option:selected').data('carrera');
        var cursoId = $('#materia_id').find('option:selected').data('curso');

        // Ocultar todos los campos de solo lectura al cambiar
        $('#carrera_group').hide();
        $('#curso_group').hide();
        $('#profesor_group').hide();
        $('#btn-add-profesor').hide();
        $('#btn-edit-profesor').hide();
        $('#btn-remove-profesor').hide(); // Ocultar botón de desasignar
        $('#btn-select-profesor').hide();

        if (materiaId) {
            $.ajax({
                url: 'get_item_data.php',
                type: 'POST',
                data: {
                    type: 'materia',
                    id: materiaId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    
                    // Mostrar información de carrera o curso pre-admisión
                    if (data.carrera_id && data.carrera_id !== null) {
                        $('#carrera_readonly').val(data.carrera_nombre);
                        $('#carrera_group').show();
                    } else if (data.curso_pre_admision_id && data.curso_pre_admision_id !== null) {
                        $('#curso_readonly').val(data.curso_pre_admision_nombre);
                        $('#curso_group').show();
                    }
                    
                    // Mostrar información del profesor
                    if (data.profesor_id && data.profesor_id !== null) {
                        $('#profesor_readonly').val(data.profesor_nombre + ' ' + data.profesor_apellido);
                        $('#profesor_group').show();
                        $('#btn-edit-profesor').show();
                        $('#btn-remove-profesor').show(); // Mostrar botón de desasignar
                    } else {
                        $('#profesor_readonly').val('Sin profesor asignado');
                        $('#profesor_group').show();
                        $('#btn-add-profesor').show();
                        $('#btn-select-profesor').show();
                        $('#btn-remove-profesor').hide(); // Ocultar botón de desasignar
                    }
                },
                error: function() {
                    console.log('Error al cargar información de la materia');
                }
            });
        }
    }

    // Disparar la función al cargar la página si ya hay materia seleccionada
    $(document).ready(function() {
        if ($('#materia_id').val()) {
            mostrarInfoMateria();
        }
    });

    // Eliminar profesor desde el formulario principal
    function removeProfesorFromMateria() {
        if (confirm('¿Está seguro de que desea desasignar el profesor de esta materia?')) {
            var materiaId = $('#materia_id').val();
            $.ajax({
                url: 'remove_profesor.php',
                type: 'POST',
                data: { materia_id: materiaId },
                success: function(response) {
                    mostrarInfoMateria(); // Recargar información
                },
                error: function() {
                    alert('Error al desasignar profesor');
                }
            });
        }
    }

    // Función para abrir el modal de selección de profesor
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

    // Guardar profesor vía AJAX y actualizar select
    $('#form-profesor').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'save_profesor.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Asignar automáticamente el profesor a la materia
                        var materiaId = $('#materia_id').val();
                        if (materiaId) {
                            $.ajax({
                                url: 'asignar_profesor.php',
                                type: 'POST',
                                data: { 
                                    materia_id: materiaId,
                                    profesor_id: result.profesor_id
                                },
                                success: function(resp) {
                                    try {
                                        const asignacion = JSON.parse(resp);
                                        if (asignacion.success) {
                                            $('#profesor_readonly').val(result.nombre + ' ' + result.apellido);
                                            $('#btn-add-profesor').hide();
                                            $('#btn-select-profesor').hide();
                                            $('#btn-edit-profesor').show();
                                            $('#btn-remove-profesor').show();
                                            cerrarModalProfesor();
                                        }
                                    } catch (e) {
                                        cerrarModalProfesor();
                                        location.reload();
                                    }
                                },
                                error: function() {
                                    cerrarModalProfesor();
                                    location.reload();
                                }
                            });
                        } else {
                            cerrarModalProfesor();
                            location.reload();
                        }
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo guardar el profesor'));
                    }
                } catch (e) {
                    alert('Error al guardar profesor');
                }
            },
            error: function() {
                alert('Error al guardar profesor');
            }
        });
    });
    </script>
</body>
</html>