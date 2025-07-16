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

// Obtener todos los profesores y sus materias
$sql = "SELECT p.*, GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materias
        FROM profesores p
        LEFT JOIN materias m ON m.profesor_id = p.id_profesor
        GROUP BY p.id_profesor
        ORDER BY p.apellido, p.nombre";
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
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.3);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            padding: 30px 24px;
            border-radius: 10px;
            min-width: 320px;
            max-width: 95vw;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }
        .modal-content h3 { margin-top: 0; }
        .modal-content .form-group { margin-bottom: 15px; }
        .modal-content label { font-weight: bold; }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .modal-content .acciones-modal {
            display: flex;
            gap: 10px;
            margin-top: 10px;
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
    </style>
    <!-- Agregar Select2 para búsqueda en el select de materias -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="main-container">
        <div class="profesores-header">
            <h2>Profesores</h2>
            <button class="btn-add" onclick="abrirModalAgregar()">+</button>
        </div>
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
            </tbody>
        </table>
    </div>
    <a href="añadir_aula.php" class="back-button" style="margin: 30px auto 0 auto; display: block; max-width: 350px; background: #6c757d; color: white; text-align: center; text-decoration: none; border-radius: 5px; padding: 12px 0; font-size: 1.1em; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">Volver a Añadir Disposición Áulica</a>

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
                    <label>Materia:</label>
                    <select name="id_materia" required id="select-materia-modal">
                        <option value="">Seleccione una materia</option>
                        <?php 
                        $materias->data_seek(0); 
                        while($m = $materias->fetch_assoc()): 
                            $nombre = $m['nombre'];
                            $info = '';
                            // Carrera
                            if (isset($m['carrera_id']) && $m['carrera_id']) {
                                $sql_c = "SELECT nombre FROM carreras WHERE id_carrera = " . intval($m['carrera_id']);
                                $res_c = $conn->query($sql_c);
                                if ($res_c && $row_c = $res_c->fetch_assoc()) {
                                    $info = $row_c['nombre'];
                                }
                            }
                            // Curso pre-admisión
                            elseif (isset($m['curso_pre_admision_id']) && $m['curso_pre_admision_id']) {
                                $sql_cp = "SELECT nombre_curso FROM cursos_pre_admisiones WHERE id_curso_pre_admision = " . intval($m['curso_pre_admision_id']);
                                $res_cp = $conn->query($sql_cp);
                                if ($res_cp && $row_cp = $res_cp->fetch_assoc()) {
                                    $info = $row_cp['nombre_curso'];
                                }
                            }
                            // Diplomatura (si tienes campo diplomatura_id)
                            elseif (isset($m['diplomatura_id']) && $m['diplomatura_id']) {
                                $sql_d = "SELECT nombre FROM carreras WHERE id_carrera = " . intval($m['diplomatura_id']);
                                $res_d = $conn->query($sql_d);
                                if ($res_d && $row_d = $res_d->fetch_assoc()) {
                                    $info = $row_d['nombre'];
                                }
                            }
                            $label = $nombre . ($info ? ' (' . $info . ')' : '');
                        ?>
                        <option value="<?php echo $m['id_materia']; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="acciones-modal">
                    <button type="submit" class="btn-link">Enlazar</button>
                    <button type="button" onclick="cerrarModalEnlazar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
        // Inicializar Select2 cada vez que se abre el modal
        setTimeout(function() {
            if ($.fn.select2 && $('#select-materia-modal').data('select2')) {
                $('#select-materia-modal').select2('destroy');
            }
            $('#select-materia-modal').select2({
                dropdownParent: $('#modal-enlazar'),
                width: '100%',
                placeholder: 'Seleccione una materia',
                minimumResultsForSearch: 0,
                language: {
                    noResults: function() {
                        return 'No se encontraron materias';
                    }
                }
            });
        }, 100);
    }
    function cerrarModalEnlazar() {
        document.getElementById('modal-enlazar').style.display = 'none';
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
    <script>
    // Filtro de materias en el modal de enlazar
    // Elimina la inicialización global de Select2 en $(document).ready()
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>
</html> 