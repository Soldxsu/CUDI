<?php
include 'conexion.php';

// Verificar si se recibió el ID de la tarjeta
if (!isset($_GET['id_tarjeta'])) {
    header('Location: disposicionaulica.php?error=ID de tarjeta no especificado');
    exit;
}

$id_tarjeta = intval($_GET['id_tarjeta']);

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $turno_id = intval($_POST['turno_id']);
    $itinerario_id = intval($_POST['itinerario_id']);
    $materia_id = intval($_POST['materia_id']);
    $aula_id = intval($_POST['aula_id']);
    $profesor_id = intval($_POST['profesor_id']);
    $cantidad_estudiantes = intval($_POST['cantidad_estudiantes']);
    
    // Verificar si el turno está lleno para esa fecha (excluyendo la tarjeta actual)
    $sql_total_capacity = "SELECT SUM(capacidad) as capacidad_total FROM aulas WHERE estado = 'activa'";
    $result_total = $conn->query($sql_total_capacity);
    $total_capacity = $result_total->fetch_assoc()['capacidad_total'] ?? 0;
    
    // Obtener el número de estudiantes ya asignados en ese turno y fecha (excluyendo la tarjeta actual)
    $sql_estudiantes_asignados = "
        SELECT SUM(t.cantidad_estudiantes) as estudiantes_asignados
        FROM tarjetas t
        INNER JOIN itinerarios i ON t.id_itinerario = i.id_itinerario
        WHERE t.fecha = ? AND i.id_turno = ? AND t.id_tarjeta != ?
    ";
    
    $stmt = $conn->prepare($sql_estudiantes_asignados);
    $stmt->bind_param('sii', $fecha, $turno_id, $id_tarjeta);
    $stmt->execute();
    $result_estudiantes = $stmt->get_result();
    $estudiantes_asignados = $result_estudiantes->fetch_assoc()['estudiantes_asignados'] ?? 0;
    
    // Verificar si se puede actualizar la tarjeta
    if (($estudiantes_asignados + $cantidad_estudiantes) > $total_capacity) {
        header('Location: editar_tarjeta.php?id_tarjeta=' . $id_tarjeta . '&error=Turno lleno para esta fecha. Capacidad total: ' . $total_capacity . ', Estudiantes asignados: ' . ($estudiantes_asignados + $cantidad_estudiantes));
        exit;
    }
    
    // Actualizar la tarjeta
    $sql = "UPDATE tarjetas_disposicion SET 
            fecha = ?, turno_id = ?, itinerario_id = ?, materia_id = ?, 
            aula_id = ?, profesor_id = ?, cantidad_estudiantes = ?
            WHERE id_tarjeta = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siiiiiii', $fecha, $turno_id, $itinerario_id, $materia_id, $aula_id, $profesor_id, $cantidad_estudiantes, $id_tarjeta);
    
    if ($stmt->execute()) {
        header('Location: disposicionaulica.php?success=1&mensaje=Tarjeta actualizada exitosamente');
        exit;
    } else {
        $error = 'Error al actualizar la tarjeta: ' . $conn->error;
    }
}

// Obtener datos de la tarjeta
$sql_tarjeta = "SELECT * FROM tarjetas_disposicion WHERE id_tarjeta = ?";
$stmt = $conn->prepare($sql_tarjeta);
$stmt->bind_param('i', $id_tarjeta);
$stmt->execute();
$result_tarjeta = $stmt->get_result();

if ($result_tarjeta->num_rows === 0) {
    header('Location: disposicionaulica.php?error=Tarjeta no encontrada');
    exit;
}

$tarjeta = $result_tarjeta->fetch_assoc();

// Obtener datos relacionados
$sql_itinerario = "SELECT i.*, t.nombre as turno_nombre, t.hora_inicio, t.hora_fin 
                   FROM itinerario i 
                   INNER JOIN turnos t ON i.id_turno = t.id_turno 
                   WHERE i.id_itinerario = ?";
$stmt = $conn->prepare($sql_itinerario);
$stmt->bind_param('i', $tarjeta['itinerario_id']);
$stmt->execute();
$itinerario = $stmt->get_result()->fetch_assoc();

$sql_materia = "SELECT m.*, c.nombre as carrera_nombre 
                FROM materias m 
                LEFT JOIN carreras c ON m.carrera_id = c.id_carrera 
                WHERE m.id_materia = ?";
$stmt = $conn->prepare($sql_materia);
$stmt->bind_param('i', $tarjeta['materia_id']);
$stmt->execute();
$materia = $stmt->get_result()->fetch_assoc();

$sql_aula = "SELECT * FROM aulas WHERE id_aula = ?";
$stmt = $conn->prepare($sql_aula);
$stmt->bind_param('i', $tarjeta['aula_id']);
$stmt->execute();
$aula = $stmt->get_result()->fetch_assoc();

$sql_profesor = "SELECT * FROM profesores WHERE id_profesor = ?";
$stmt = $conn->prepare($sql_profesor);
$stmt->bind_param('i', $tarjeta['profesor_id']);
$stmt->execute();
$profesor = $stmt->get_result()->fetch_assoc();

// Obtener listas para los selects
$turnos = $conn->query("SELECT * FROM turnos ORDER BY hora_inicio");
$itinerarios = $conn->query("SELECT * FROM itinerario ORDER BY hora_inicio");
$aulas = $conn->query("SELECT * FROM aulas WHERE estado = 'activa' ORDER BY nombre");
$profesores = $conn->query("SELECT * FROM profesores ORDER BY apellido, nombre");
$materias = $conn->query("SELECT m.*, c.nombre as carrera_nombre 
                         FROM materias m 
                         LEFT JOIN carreras c ON m.carrera_id = c.id_carrera 
                         ORDER BY m.nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarjeta - Disposición Áulica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        
        .actions {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Tarjeta de Disposición</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha" value="<?php echo $tarjeta['fecha']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Turno:</label>
                <select name="turno_id" required>
                    <?php $turnos->data_seek(0); while ($turno = $turnos->fetch_assoc()): ?>
                        <option value="<?php echo $turno['id_turno']; ?>" <?php echo $turno['id_turno'] == $tarjeta['turno_id'] ? 'selected' : ''; ?>>
                            <?php echo $turno['nombre']; ?> (<?php echo $turno['hora_inicio']; ?> - <?php echo $turno['hora_fin']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Horario:</label>
                <select name="itinerario_id" required>
                    <?php $itinerarios->data_seek(0); while ($itinerario_item = $itinerarios->fetch_assoc()): ?>
                        <option value="<?php echo $itinerario_item['id_itinerario']; ?>" <?php echo $itinerario_item['id_itinerario'] == $tarjeta['itinerario_id'] ? 'selected' : ''; ?>>
                            <?php echo $itinerario_item['hora_inicio']; ?> - <?php echo $itinerario_item['hora_fin']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Materia:</label>
                <select name="materia_id" required>
                    <?php $materias->data_seek(0); while ($materia_item = $materias->fetch_assoc()): ?>
                        <option value="<?php echo $materia_item['id_materia']; ?>" <?php echo $materia_item['id_materia'] == $tarjeta['materia_id'] ? 'selected' : ''; ?>>
                            <?php echo $materia_item['nombre']; ?>
                            <?php if ($materia_item['carrera_nombre']): ?>
                                (<?php echo $materia_item['carrera_nombre']; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Profesor:</label>
                <select name="profesor_id" required>
                    <?php $profesores->data_seek(0); while ($profesor_item = $profesores->fetch_assoc()): ?>
                        <option value="<?php echo $profesor_item['id_profesor']; ?>" <?php echo $profesor_item['id_profesor'] == $tarjeta['profesor_id'] ? 'selected' : ''; ?>>
                            <?php echo $profesor_item['apellido'] . ', ' . $profesor_item['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Aula:</label>
                <select name="aula_id" required>
                    <?php $aulas->data_seek(0); while ($aula_item = $aulas->fetch_assoc()): ?>
                        <option value="<?php echo $aula_item['id_aula']; ?>" <?php echo $aula_item['id_aula'] == $tarjeta['aula_id'] ? 'selected' : ''; ?>>
                            <?php echo $aula_item['nombre']; ?> (Cap: <?php echo $aula_item['cantidad']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Cantidad de Estudiantes:</label>
                <input type="number" name="cantidad_estudiantes" value="<?php echo $tarjeta['cantidad_estudiantes'] ?? 0; ?>" min="1" required>
            </div>
            
            <div class="actions">
                <button type="submit" class="btn btn-primary">Actualizar Tarjeta</button>
                <a href="disposicionaulica.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html> 