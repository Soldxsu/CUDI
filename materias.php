<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Obtener el término de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Construir la consulta SQL con filtro de búsqueda
$sql = "SELECT m.*, c.nombre AS carrera, cp.nombre_curso AS curso_pre_admision, 
               CONCAT(p.nombre, ' ', p.apellido) AS profesor_nombre
        FROM materias m 
        LEFT JOIN carreras c ON m.carrera_id = c.id_carrera 
        LEFT JOIN cursos_pre_admisiones cp ON m.curso_pre_admision_id = cp.id_curso_pre_admision
        LEFT JOIN profesores p ON m.profesor_id = p.id_profesor";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " WHERE m.nombre LIKE '%$busqueda_escaped%' OR c.nombre LIKE '%$busqueda_escaped%' OR p.nombre LIKE '%$busqueda_escaped%' OR p.apellido LIKE '%$busqueda_escaped%'";
}

$sql .= " ORDER BY m.nombre";
$materias = $conn->query($sql);

// Obtener carreras, cursos y profesores para los selects
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre");
$cursos = $conn->query("SELECT id_curso_pre_admision, nombre_curso FROM cursos_pre_admisiones ORDER BY nombre_curso");
$profesores = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores ORDER BY apellido, nombre");
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
    </style>
</head>
<body>
    <div class="main-container">
        <div class="materias-header">
            <div class="header-left">
                <a href="disposicionaulica.php" class="btn-volver">← Volver</a>
                <h2>Materias</h2>
            </div>
            <button class="btn-add" onclick="abrirModalAgregar()">+</button>
        </div>
        
        <!-- Barra de búsqueda -->
        <form method="GET" class="search-container">
            <input type="text" name="busqueda" class="search-input" placeholder="Buscar por nombre de materia, carrera o profesor..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="search-btn">🔍 Buscar</button>
            <?php if (!empty($busqueda)): ?>
                <a href="materias.php" class="clear-btn">Limpiar</a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($busqueda)): ?>
            <div class="results-info">
                <?php 
                $total_resultados = $materias->num_rows;
                echo "Mostrando $total_resultados resultado" . ($total_resultados != 1 ? 's' : '') . " para: \"$busqueda\"";
                ?>
            </div>
        <?php endif; ?>
        
        <table class="materias-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Carrera</th>
                    <th>Curso Pre-Admisión</th>
                    <th>Profesor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($materias->num_rows > 0): ?>
                    <?php while($m = $materias->fetch_assoc()): ?>
                    <tr>
                        <td><span class="avatar-materia">📚</span></td>
                        <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                        <td><?php echo $m['carrera'] ? htmlspecialchars($m['carrera']) : '-'; ?></td>
                        <td><?php echo $m['curso_pre_admision'] ? htmlspecialchars($m['curso_pre_admision']) : '-'; ?></td>
                        <td><?php echo $m['profesor_nombre'] ? htmlspecialchars($m['profesor_nombre']) : '-'; ?></td>
                        <td class="acciones">
                            <button class="btn-edit" onclick="abrirModalEditar(<?php echo $m['id_materia']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', '<?php echo $m['carrera_id']; ?>', '<?php echo $m['curso_pre_admision_id']; ?>')">✏️</button>
                            <button class="btn-delete" onclick="eliminarMateria(<?php echo $m['id_materia']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>')">🗑️</button>
                            <button class="btn-link" onclick="abrirModalEnlazar(<?php echo $m['id_materia']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>')">🔗</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-results">
                            <?php if (!empty($busqueda)): ?>
                                No se encontraron materias que coincidan con "<?php echo htmlspecialchars($busqueda); ?>"
                            <?php else: ?>
                                No hay materias registradas
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

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
            <form id="form-materia" onsubmit="guardarMateria(event)">
                <input type="hidden" name="modo" id="modal-accion" value="agregar">
                <input type="hidden" name="materia_id" id="modal-id-materia">
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

    <!-- Modal para enlazar materia con profesor -->
    <div class="modal" id="modal-enlazar">
        <div class="modal-content">
            <h3>Enlazar Materia a Profesor</h3>
            <form onsubmit="enlazarProfesor(event)">
                <input type="hidden" name="id_materia" id="enlazar-id-materia">
                <div class="form-group">
                    <label for="profesor-modal">Profesor:</label>
                    <div class="select-container">
                        <select id="profesor-modal" name="id_profesor" required>
                            <option value="">Seleccione un profesor</option>
                            <?php $profesores->data_seek(0); while ($row = $profesores->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_profesor']; ?>"><?php echo htmlspecialchars($row['apellido'] . ', ' . $row['nombre']); ?></option>
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

    <script>
    function guardarMateria(event) {
        event.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'gestionar_materia');
        formData.append('modo', document.getElementById('modal-accion').value);
        formData.append('nombre', document.getElementById('modal-nombre').value);
        formData.append('carrera_id', document.getElementById('modal-carrera').value);
        formData.append('curso_pre_admision_id', document.getElementById('modal-curso').value);
        
        if (document.getElementById('modal-accion').value === 'editar') {
            formData.append('materia_id', document.getElementById('modal-id-materia').value);
        }
        
        fetch('gestionar_materias.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la materia');
        });
    }
    
    function abrirModalAgregar() {
        document.getElementById('modal-titulo').textContent = 'Agregar Materia';
        document.getElementById('modal-accion').value = 'agregar';
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
        document.getElementById('modal-accion').value = 'editar';
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
    
    function abrirModalEnlazar(id_materia, nombre) {
        document.getElementById('enlazar-id-materia').value = id_materia;
        document.getElementById('modal-enlazar').style.display = 'flex';
    }
    
    function cerrarModalEnlazar() {
        document.getElementById('modal-enlazar').style.display = 'none';
    }
    
    function enlazarProfesor(event) {
        event.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'actualizar_relacion_materia_profesor');
        formData.append('materia_id', document.getElementById('enlazar-id-materia').value);
        formData.append('profesor_id', document.getElementById('profesor-modal').value);
        
        fetch('gestionar_materias.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enlazar profesor');
        });
    }
    
    function eliminarMateria(id_materia, nombre) {
        if (!confirm('¿Está seguro de eliminar la materia "' + nombre + '"?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'eliminar_materia');
        formData.append('materia_id', id_materia);
        
        fetch('gestionar_materias.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la materia');
        });
    }
    
    window.onclick = function(event) {
        if (event.target === document.getElementById('modal-materia')) cerrarModal();
        if (event.target === document.getElementById('modal-enlazar')) cerrarModalEnlazar();
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