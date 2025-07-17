<?php
include 'conexion.php';

// Procesar acciones de añadir, editar, eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Función para capitalizar cada palabra
        function capitalizar_palabras($str) {
            return mb_convert_case(trim($str), MB_CASE_TITLE, "UTF-8");
        }
        if ($_POST['action'] === 'add') {
            $nombre = capitalizar_palabras($conn->real_escape_string($_POST['nombre']));
            $carrera_id = isset($_POST['carrera_id']) ? intval($_POST['carrera_id']) : 'NULL';
            $curso_pre_admision_id = isset($_POST['curso_pre_admision_id']) ? intval($_POST['curso_pre_admision_id']) : 'NULL';
            $conn->query("INSERT INTO materias (nombre, carrera_id, curso_pre_admision_id) VALUES ('$nombre', $carrera_id, $curso_pre_admision_id)");
        } elseif ($_POST['action'] === 'edit' && isset($_POST['id_materia'])) {
            $id = intval($_POST['id_materia']);
            $nombre = capitalizar_palabras($conn->real_escape_string($_POST['nombre']));
            $carrera_id = isset($_POST['carrera_id']) ? intval($_POST['carrera_id']) : 'NULL';
            $curso_pre_admision_id = isset($_POST['curso_pre_admision_id']) ? intval($_POST['curso_pre_admision_id']) : 'NULL';
            $conn->query("UPDATE materias SET nombre='$nombre', carrera_id=$carrera_id, curso_pre_admision_id=$curso_pre_admision_id WHERE id_materia=$id");
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id_materia'])) {
            $id = intval($_POST['id_materia']);
            $conn->query("DELETE FROM materias WHERE id_materia=$id");
        }
        header('Location: materias.php');
        exit;
    }
}

// Obtener todas las materias y su información relacionada
$sql = "SELECT m.*, c.nombre AS carrera, cp.nombre_curso AS curso_pre_admision FROM materias m LEFT JOIN carreras c ON m.carrera_id = c.id_carrera LEFT JOIN cursos_pre_admisiones cp ON m.curso_pre_admision_id = cp.id_curso_pre_admision ORDER BY m.nombre";
$materias = $conn->query($sql);
// Obtener carreras y cursos para los selects
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre");
$cursos = $conn->query("SELECT id_curso_pre_admision, nombre_curso FROM cursos_pre_admisiones ORDER BY nombre_curso");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materias</title>
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
        .materias-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            border-bottom: 2px solid #e3eefd;
            padding-bottom: 12px;
        }
        .materias-header h2 {
            margin: 0;
            font-size: 2.3em;
            color: #1a237e;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .materias-header .btn-add {
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
        .materias-header .btn-add:hover {
            background: #218838;
            box-shadow: 0 4px 16px rgba(40,167,69,0.18);
        }
        .materias-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(30, 64, 175, 0.08);
        }
        .materias-table th, .materias-table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 1.05em;
        }
        .materias-table th {
            background: #e3eefd;
            color: #263238;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .materias-table tr:last-child td {
            border-bottom: none;
        }
        .materias-table tr:hover {
            background: #f0f6ff;
            transition: background 0.15s;
        }
        .avatar-materia {
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
        .icon-stack {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 18px;
        }
        .icon-circle {
            width: 70px;
            height: 70px;
            background: #0074ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-bottom: 10px;
        }
        .icon-circle span {
            font-size: 38px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="materias-header">
            <h2>Materias</h2>
            <button class="btn-add" onclick="abrirModalAgregar()">+</button>
        </div>
        <table class="materias-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Carrera</th>
                    <th>Curso Pre-Admisión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($m = $materias->fetch_assoc()): ?>
                <tr>
                    <td><span class="avatar-materia">📚</span></td>
                    <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                    <td><?php echo $m['carrera'] ? htmlspecialchars($m['carrera']) : '-'; ?></td>
                    <td><?php echo $m['curso_pre_admision'] ? htmlspecialchars($m['curso_pre_admision']) : '-'; ?></td>
                    <td class="acciones">
                        <button class="btn-edit" onclick="abrirModalEditar(<?php echo $m['id_materia']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', '<?php echo $m['carrera_id']; ?>', '<?php echo $m['curso_pre_admision_id']; ?>')">✏️</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que desea eliminar esta materia?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_materia" value="<?php echo $m['id_materia']; ?>">
                            <button class="btn-delete" type="submit">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <a href="añadir_aula.php" class="back-button" style="margin: 30px auto 0 auto; display: block; max-width: 350px; background: #6c757d; color: white; text-align: center; text-decoration: none; border-radius: 5px; padding: 12px 0; font-size: 1.1em; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">Volver a Añadir Disposición Áulica</a>

    <!-- Modal profesional para agregar/editar materia -->
    <style>
    #modal-materia {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.25);
        align-items: center;
        justify-content: center;
    }
    #modal-materia .modal-content {
        background: #fff;
        padding: 32px 28px 24px 28px;
        border-radius: 14px;
        min-width: 320px;
        max-width: 95vw;
        box-shadow: 0 8px 32px rgba(30,64,175,0.13);
        margin: 0 auto;
        position: relative;
    }
    #modal-materia h3 {
        margin-top: 0;
        font-size: 1.5em;
        color: #1a237e;
        font-weight: 700;
        margin-bottom: 18px;
    }
    #modal-materia .form-group label {
        font-weight: 600;
        color: #222;
    }
    #modal-materia .form-group input, #modal-materia .form-group select {
        width: 100%;
        padding: 9px 10px;
        border: 1px solid #b0c4de;
        border-radius: 6px;
        font-size: 1.08em;
        margin-bottom: 8px;
        background: #f7faff;
    }
    #modal-materia .acciones-modal {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    #modal-materia .acciones-modal button {
        border: none;
        border-radius: 6px;
        padding: 9px 22px;
        font-size: 1em;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s;
    }
    #modal-materia .acciones-modal .btn-edit {
        background: #ffc107;
        color: #222;
    }
    #modal-materia .acciones-modal .btn-edit:hover {
        background: #ffb300;
    }
    #modal-materia .acciones-modal button[type="button"] {
        background: #f5f5f5;
        color: #222;
        border: 1px solid #b0c4de;
    }
    #modal-materia .acciones-modal button[type="button"]:hover {
        background: #e3eefd;
    }
    </style>
    <div class="modal" id="modal-materia">
        <div class="modal-content">
            <h3 id="modal-titulo">Agregar Materia</h3>
            <form method="POST" id="form-materia">
                <input type="hidden" name="action" id="modal-accion" value="add">
                <input type="hidden" name="id_materia" id="modal-id-materia">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" id="modal-nombre" required>
                </div>
                <div class="form-group">
                    <label>Carrera:</label>
                    <select name="carrera_id" id="modal-carrera">
                        <option value="">Seleccione una carrera</option>
                        <?php $carreras->data_seek(0); while($c = $carreras->fetch_assoc()): ?>
                        <option value="<?php echo $c['id_carrera']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Curso Pre-Admisión:</label>
                    <select name="curso_pre_admision_id" id="modal-curso">
                        <option value="">Seleccione un curso</option>
                        <?php $cursos->data_seek(0); while($cp = $cursos->fetch_assoc()): ?>
                        <option value="<?php echo $cp['id_curso_pre_admision']; ?>"><?php echo htmlspecialchars($cp['nombre_curso']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="acciones-modal">
                    <button type="submit" class="btn-edit">Guardar</button>
                    <button type="button" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function abrirModalAgregar() {
        document.getElementById('modal-titulo').textContent = 'Agregar Materia';
        document.getElementById('modal-accion').value = 'add';
        document.getElementById('modal-id-materia').value = '';
        document.getElementById('modal-nombre').value = '';
        document.getElementById('modal-carrera').value = '';
        document.getElementById('modal-curso').value = '';
        document.getElementById('modal-carrera').disabled = false;
        document.getElementById('modal-curso').disabled = false;
        document.getElementById('modal-materia').style.display = 'flex';
    }
    function abrirModalEditar(id, nombre, carrera_id, curso_id) {
        document.getElementById('modal-titulo').textContent = 'Editar Materia';
        document.getElementById('modal-accion').value = 'edit';
        document.getElementById('modal-id-materia').value = id;
        document.getElementById('modal-nombre').value = nombre;
        document.getElementById('modal-carrera').value = carrera_id;
        document.getElementById('modal-curso').value = curso_id;
        document.getElementById('modal-carrera').disabled = false;
        document.getElementById('modal-curso').disabled = false;
        document.getElementById('modal-materia').style.display = 'flex';
        // Aplicar bloqueo mutuo si ya hay uno seleccionado
        if (carrera_id && carrera_id !== '') {
            document.getElementById('modal-curso').value = '';
            document.getElementById('modal-curso').disabled = true;
        } else if (curso_id && curso_id !== '') {
            document.getElementById('modal-carrera').value = '';
            document.getElementById('modal-carrera').disabled = true;
        }
    }
    function cerrarModal() {
        document.getElementById('modal-materia').style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target === document.getElementById('modal-materia')) cerrarModal();
    }
    // Lógica de bloqueo mutuo entre carrera y curso pre-admisión
    window.addEventListener('DOMContentLoaded', function() {
        var carrera = document.getElementById('modal-carrera');
        var curso = document.getElementById('modal-curso');
        if (carrera && curso) {
            carrera.addEventListener('change', function() {
                if (this.value) {
                    curso.value = '';
                    curso.disabled = true;
                } else {
                    curso.disabled = false;
                }
            });
            curso.addEventListener('change', function() {
                if (this.value) {
                    carrera.value = '';
                    carrera.disabled = true;
                } else {
                    carrera.disabled = false;
                }
            });
        }
    });
    </script>
</body>
</html> 