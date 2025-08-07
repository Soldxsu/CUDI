<?php
include 'conexion.php';

// Procesar acciones de añadir, editar, eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $apellido = $conn->real_escape_string($_POST['apellido']);
            $correo = trim($_POST['correo']) === '' ? '-' : $conn->real_escape_string($_POST['correo']);
            $telefono = trim($_POST['telefono']) === '' ? '-' : $conn->real_escape_string($_POST['telefono']);
            $conn->query("INSERT INTO profesores (nombre, apellido, correo, telefono) VALUES ('$nombre', '$apellido', '$correo', '$telefono')");
        } elseif ($_POST['action'] === 'edit' && isset($_POST['id_profesor'])) {
            $id = intval($_POST['id_profesor']);
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $apellido = $conn->real_escape_string($_POST['apellido']);
            $correo = trim($_POST['correo']) === '' ? '-' : $conn->real_escape_string($_POST['correo']);
            $telefono = trim($_POST['telefono']) === '' ? '-' : $conn->real_escape_string($_POST['telefono']);
            $conn->query("UPDATE profesores SET nombre='$nombre', apellido='$apellido', correo='$correo', telefono='$telefono' WHERE id_profesor=$id");
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id_profesor'])) {
            $id = intval($_POST['id_profesor']);
            $conn->query("DELETE FROM profesores WHERE id_profesor=$id");
        } elseif ($_POST['action'] === 'link' && isset($_POST['id_profesor'], $_POST['id_materia'])) {
            $id_prof = intval($_POST['id_profesor']);
            $id_mat = intval($_POST['id_materia']);
            $conn->query("UPDATE materias SET profesor_id=$id_prof WHERE id_materia=$id_mat");
        } elseif ($_POST['action'] === 'unlink' && isset($_POST['id_materia'])) {
            $id_mat = intval($_POST['id_materia']);
            $conn->query("UPDATE materias SET profesor_id = NULL WHERE id_materia = $id_mat");
        }
        header('Location: profesores.php');
        exit;
    }
}

// Obtener el término de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Construir la consulta SQL con filtro de búsqueda
$sql = "SELECT p.*, GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materias
        FROM profesores p
        LEFT JOIN materias m ON m.profesor_id = p.id_profesor";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " WHERE p.nombre LIKE '%$busqueda_escaped%' OR p.apellido LIKE '%$busqueda_escaped%' OR p.correo LIKE '%$busqueda_escaped%' OR m.nombre LIKE '%$busqueda_escaped%'";
}

$sql .= " GROUP BY p.id_profesor ORDER BY p.apellido, p.nombre";
$profesores = $conn->query($sql);

// Obtener materias para enlazar
$materias = $conn->query("SELECT id_materia, nombre, carrera_id, curso_pre_admision_id FROM materias ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="css/añadir.css">
    <!-- jQuery y Select2 -->
    <style>
        body {
            background: #f4f7fb;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .main-container {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 36px 32px 32px 32px;
        }
        .profesores-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            border-bottom: 2px solid #e3eefd;
            padding-bottom: 12px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .btn-volver {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .btn-volver:hover {
            background: #5a6268;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .profesores-header h2 {
            margin: 0;
            font-size: 2.3em;
            color: #1a237e;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .profesores-header .btn-add {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 1.7em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(40,167,69,0.10);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .profesores-header .btn-add:hover {
            background: #218838;
            box-shadow: 0 4px 16px rgba(40,167,69,0.18);
        }
        .search-container {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .search-input {
            flex: 1;
            max-width: 400px;
            padding: 12px 16px;
            border: 2px solid #e3eefd;
            border-radius: 8px;
            font-size: 1em;
            background: #f7faff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-input:focus {
            outline: none;
            border-color: #0074ff;
            box-shadow: 0 0 0 3px rgba(0,116,255,0.1);
        }
        .search-btn {
            background: #0074ff;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .search-btn:hover {
            background: #0056b3;
            box-shadow: 0 4px 12px rgba(0,116,255,0.2);
        }
        .clear-btn {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .clear-btn:hover {
            background: #5a6268;
        }
        .profesores-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(30, 64, 175, 0.08);
        }
        .profesores-table th, .profesores-table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 1.05em;
        }
        .profesores-table th {
            background: #e3eefd;
            color: #263238;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .profesores-table tr:last-child td {
            border-bottom: none;
        }
        .profesores-table tr:hover {
            background: #f0f6ff;
            transition: background 0.15s;
        }
        .avatar-profesor {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #b3c6ff 60%, #e3eefd 100%);
            color: #1a237e;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1em;
            margin-right: 10px;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(30,64,175,0.07);
        }
        .profesores-table td.materias-cell {
            max-width: 260px;
            min-width: 120px;
            vertical-align: top;
            background: #f7faff;
            border-radius: 6px;
            font-size: 0.98em;
            line-height: 1.4;
            padding: 10px 8px;
            overflow: hidden;
        }
        .materias-list {
            max-height: 90px;
            overflow-y: auto;
            padding-right: 4px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px 8px;
        }
        .materia-chip {
            background: #e3eefd;
            color: #1a237e;
            border-radius: 16px;
            padding: 3px 22px 3px 12px;
            font-size: 0.97em;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 2px;
            box-shadow: 0 1px 2px rgba(30,64,175,0.04);
            position: relative;
            margin-right: 6px;
            transition: background 0.2s;
        }
        .materia-chip:hover {
            background: #c7dbfa;
            cursor: pointer;
        }
        .materia-chip .chip-remove {
            display: none;
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1em;
            color: #1a237e;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0 2px;
            z-index: 2;
        }
        .materia-chip:hover .chip-remove {
            display: inline;
        }
        .acciones {
            display: flex;
            gap: 8px;
        }
        .acciones button {
            border: none;
            border-radius: 4px;
            padding: 7px 12px;
            font-size: 1em;
            cursor: pointer;
        }
        .btn-edit { background: #ffc107; color: #222; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-link { background: #007bff; color: #fff; }
        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            font-size: 1.1em;
        }
        .results-info {
            margin-bottom: 16px;
            color: #6c757d;
            font-size: 0.95em;
            font-weight: 500;
        }
        /* --- MODALES: estilos copiados de añadir.css para unificar diseño --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 2100;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.25);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            padding: 32px 28px 24px 28px;
            border-radius: 14px;
            min-width: 320px;
            max-width: 95vw;
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.13);
            margin: 0 auto;
            position: relative;
        }
        .modal-content h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .modal-content .form-group {
            margin-bottom: 18px;
        }
        .modal-content label {
            font-weight: 600;
            color: #222;
        }
        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #b0c4de;
            border-radius: 6px;
            font-size: 1.08em;
            margin-bottom: 8px;
            background: #f7faff;
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
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 18px;
            top: 10px;
        }
        .close:hover {
            color: #1a237e;
        }
        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 900px) {
            .main-container { padding: 12px 2vw; }
        }
        @media (max-width: 600px) {
            .main-container { max-width: 100vw; border-radius: 0; box-shadow: none; }
        }
        .select2-container--default .select2-selection--single {
            font-size: 1.15em;
            min-height: 44px;
        }
        .select2-dropdown {
            font-size: 1.15em;
        }
        .select2-search--dropdown .select2-search__field {
            font-size: 1.1em;
            min-height: 36px;
        }
        /* Forzar z-index alto en el dropdown de select2 */
        .select2-container--open .select2-dropdown {
            z-index: 10000 !important;
        }
        /* Forzar que siempre aparezca el campo de búsqueda */
        .select2earch--dropdown {
            display: block !important;
        }
        .select2search--dropdown .select2ield {
            display: block !important;
            width:100rtant;
            padding: 8px !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            font-size:14important;
        }
    </style>
    <!-- Agregar Select2 para búsqueda en el select de materias -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="main-container">
        <div class="profesores-header">
            <div class="header-left">
                <a href="disposicionaulica.php" class="btn-volver">← Volver</a>
                <h2>Profesores</h2>
            </div>
            <button class="btn-add" onclick="abrirModalAgregar()">+</button>
        </div>
        
        <!-- Barra de búsqueda -->
        <form method="GET" class="search-container">
            <input type="text" name="busqueda" class="search-input" placeholder="Buscar por nombre, apellido, correo o materia..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="search-btn">🔍 Buscar</button>
            <?php if (!empty($busqueda)): ?>
                <a href="profesores.php" class="clear-btn">Limpiar</a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($busqueda)): ?>
            <div class="results-info">
                <?php 
                $total_resultados = $profesores->num_rows;
                echo "Mostrando $total_resultados resultado" . ($total_resultados != 1 ? 's' : '') . " para: \"$busqueda\"";
                ?>
            </div>
        <?php endif; ?>
        
        <table class="profesores-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Materias</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($profesores->num_rows > 0): ?>
                    <?php while($p = $profesores->fetch_assoc()): ?>
                    <tr>
                        <td><span class="avatar-profesor"><?php echo strtoupper(mb_substr($p['nombre'],0,1).mb_substr($p['apellido'],0,1)); ?></span></td>
                        <td><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($p['correo']); ?></td>
                        <td><?php echo ($p['telefono'] === '0' || $p['telefono'] === '' || $p['telefono'] === '-') ? '-' : htmlspecialchars($p['telefono']); ?></td>
                        <td class="materias-cell">
                            <div class="materias-list">
                                <?php 
                                $mats = array_map('trim', explode(',', $p['materias']));
                                $hay_materias = false;
                                // Obtener ids de materias para desenlazar
                                $sql_ids = "SELECT id_materia, nombre FROM materias WHERE profesor_id = " . intval($p['id_profesor']);
                                $res_ids = $conn->query($sql_ids);
                                $ids_map = [];
                                while($row_id = $res_ids->fetch_assoc()) {
                                    $ids_map[$row_id['nombre']] = $row_id['id_materia'];
                                }
                                foreach($mats as $mat) {
                                    if ($mat !== '') {
                                        $hay_materias = true;
                                        $id_materia = isset($ids_map[$mat]) ? $ids_map[$mat] : '';
                                        echo '<span class="materia-chip">' . htmlspecialchars($mat);
                                        if ($id_materia) {
                                            echo ' <button class="chip-remove" title="Desenlazar" onclick="return desenlazarMateria(' . $p['id_profesor'] . ',' . $id_materia . ',\'' . htmlspecialchars($mat, ENT_QUOTES) . '\')">✖</button>';
                                        }
                                        echo '</span>';
                                    }
                                }
                                if (!$hay_materias) {
                                    echo '<span style="color:#888;font-size:0.97em;">Enlaza el profesor a una materia para que aparezca aquí</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td class="acciones">
                            <button class="btn-edit" onclick="abrirModalEditar(<?php echo $p['id_profesor']; ?>, '<?php echo htmlspecialchars($p['nombre']); ?>', '<?php echo htmlspecialchars($p['apellido']); ?>', '<?php echo htmlspecialchars($p['correo']); ?>', '<?php echo htmlspecialchars($p['telefono']); ?>')">✏️</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que desea eliminar este profesor?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_profesor" value="<?php echo $p['id_profesor']; ?>">
                                <button class="btn-delete" type="submit">🗑️</button>
                            </form>
                            <button class="btn-link" onclick="abrirModalEnlazar(<?php echo $p['id_profesor']; ?>)">🔗</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-results">
                            <?php if (!empty($busqueda)): ?>
                                No se encontraron profesores que coincidan con "<?php echo htmlspecialchars($busqueda); ?>"
                            <?php else: ?>
                                No hay profesores registrados
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar/editar -->
    <div class="modal" id="modal-profesor">
        <div class="modal-content">
            <h3 id="modal-titulo">Agregar Profesor</h3>
            <form method="POST" id="form-profesor">
                <input type="hidden" name="action" id="modal-accion" value="add">
                <input type="hidden" name="id_profesor" id="modal-id-profesor">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" id="modal-nombre" required>
                </div>
                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido" id="modal-apellido" required>
                </div>
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" id="modal-correo" placeholder="dejar vacío en caso de no tener/usar">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" id="modal-telefono" placeholder="dejar vacío en caso de no tener/usar">
                </div>
                <div class="acciones-modal">
                    <button type="submit" class="btn-edit">Guardar</button>
                    <button type="button" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para enlazar profesor con materia -->
    <div class="modal" id="modal-enlazar">
        <div class="modal-content">
            <h3>Enlazar Profesor a Materia</h3>
            <form method="POST">
                <input type="hidden" name="action" value="link">
                <input type="hidden" name="id_profesor" id="enlazar-id-profesor">
                <div class="form-group">
                    <label for="materia-modal">Materia:</label>
                    <div class="select-container">
                        <select id="materia-modal" name="id_materia" required>
                            <option value="">Seleccione una materia</option>
                            <?php $materias->data_seek(0); while ($row = $materias->fetch_assoc()): 
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
                            <option value="<?php echo $row['id_materia']; ?>" data-carrera="<?php echo isset($row['carrera_id']) ? $row['carrera_id'] : '' ?>" data-curso="<?php echo isset($row['curso_pre_admision_id']) ? $row['curso_pre_admision_id'] : '' ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="acciones-modal">
                    <button type="submit" class="btn-link">Enlazar</button>
                    <button type="button" onclick="cerrarModalEnlazar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Solo inicializar Select2 para elementos que existen en el DOM
        $('#jornada_id, #aula_id').select2({
            placeholder: "Seleccione una opción",
            width: '100%'
        });
    });

    function abrirModalAgregar() {
        document.getElementById('modal-titulo').textContent = 'Agregar Profesor';
        document.getElementById('modal-accion').value = 'add';
        document.getElementById('modal-id-profesor').value = '';
        document.getElementById('modal-nombre').value = '';
        document.getElementById('modal-apellido').value = '';
        document.getElementById('modal-correo').value = '';
        document.getElementById('modal-telefono').value = '';
        document.getElementById('modal-profesor').style.display = 'flex';
    }
    function abrirModalEditar(id, nombre, apellido, correo, telefono) {
        document.getElementById('modal-titulo').textContent = 'Editar Profesor';
        document.getElementById('modal-accion').value = 'edit';
        document.getElementById('modal-id-profesor').value = id;
        document.getElementById('modal-nombre').value = nombre;
        document.getElementById('modal-apellido').value = apellido;
        document.getElementById('modal-correo').value = correo;
        document.getElementById('modal-telefono').value = telefono;
        document.getElementById('modal-profesor').style.display = 'flex';
    }
    function cerrarModal() {
        document.getElementById('modal-profesor').style.display = 'none';
    }
    function abrirModalEnlazar(id_profesor) {
        document.getElementById('enlazar-id-profesor').value = id_profesor;
        document.getElementById('modal-enlazar').style.display = 'flex';
        // Inicializar Select2 para el select del modal con búsqueda habilitada
        setTimeout(function() {
            $('#materia-modal').select2({
                dropdownParent: $('#modal-enlazar'),
                placeholder: "Seleccione una opción",
                width: '100%',
                minimumResultsForSearch: 0
            });
        }, 100);
    }
    function cerrarModalEnlazar() {
        document.getElementById('modal-enlazar').style.display = 'none';
        // Destruir Select2 al cerrar el modal
        if ($('#materia-modal').data('select2')) {
            $('#materia-modal').select2('destroy');
        }
    }
    function desenlazarMateria(id_profesor, id_materia, nombre) {
        if (!confirm('¿Está seguro de desenlazar la materia "' + nombre + '" de este profesor?')) return false;
        // Crear formulario oculto y enviarlo
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        form.style.display = 'none';
        var inputAction = document.createElement('input');
        inputAction.name = 'action';
        inputAction.value = 'unlink';
        form.appendChild(inputAction);
        var inputMateria = document.createElement('input');
        inputMateria.name = 'id_materia';
        inputMateria.value = id_materia;
        form.appendChild(inputMateria);
        document.body.appendChild(form);
        form.submit();
        return false;
    }
    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        if (event.target === document.getElementById('modal-profesor')) cerrarModal();
        if (event.target === document.getElementById('modal-enlazar')) cerrarModalEnlazar();
    }
    </script>
</body>
</html> 