<?php
include 'conexion.php';
debugFecha(); // Debug de fecha para verificar zona horaria

// Procesar acciones de crear, editar, eliminar tarjetas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_tarjeta') {
            $fecha = $conn->real_escape_string($_POST['fecha']);
            $turno_id = intval($_POST['turno_id']);
            $itinerario_id = intval($_POST['itinerario_id']);
            $materia_id = intval($_POST['materia_id']);
            $aula_id = intval($_POST['aula_id']);
            $profesor_id = intval($_POST['profesor_id']);
            
            // Verificar si el turno está lleno para esa fecha
            // Obtener la capacidad total de todas las aulas activas
            $sql_total_capacity = "SELECT SUM(capacidad) as capacidad_total FROM aulas WHERE estado = 'activa'";
            $result_total = $conn->query($sql_total_capacity);
            $total_capacity = $result_total->fetch_assoc()['capacidad_total'] ?? 0;
            
            // Obtener el número de estudiantes ya asignados en ese turno y fecha
            $sql_estudiantes_asignados = "
                SELECT SUM(t.cantidad_estudiantes) as estudiantes_asignados
                FROM tarjetas_disposicion t
                INNER JOIN itinerario i ON t.itinerario_id = i.id_itinerario
                WHERE t.fecha = ? AND i.id_turno = ?
            ";
            
            $stmt = $conn->prepare($sql_estudiantes_asignados);
            $stmt->bind_param('si', $fecha, $turno_id);
            $stmt->execute();
            $result_estudiantes = $stmt->get_result();
            $estudiantes_asignados = $result_estudiantes->fetch_assoc()['estudiantes_asignados'] ?? 0;
            
            // Verificar si se puede crear más tarjetas en este turno
            if ($estudiantes_asignados >= $total_capacity) {
                header('Location: disposicionaulica.php?error=Turno lleno para esta fecha. Capacidad total: ' . $total_capacity . ', Estudiantes asignados: ' . $estudiantes_asignados);
                exit;
            }
            
            $cantidad_estudiantes = intval($_POST['cantidad_estudiantes']);
            
            $sql = "INSERT INTO tarjetas_disposicion (fecha, turno_id, itinerario_id, materia_id, aula_id, profesor_id, cantidad_estudiantes, estado) 
                    VALUES ('$fecha', $turno_id, $itinerario_id, $materia_id, $aula_id, $profesor_id, $cantidad_estudiantes, 'activa')";
            
            if ($conn->query($sql)) {
                // Calcular capacidad restante después de crear la tarjeta
                $nueva_capacidad_disponible = $total_capacity - ($estudiantes_asignados + $cantidad_estudiantes);
                header('Location: disposicionaulica.php?success=1&capacidad_restante=' . $nueva_capacidad_disponible);
                exit;
            }
        } elseif ($_POST['action'] === 'delete_tarjeta' && isset($_POST['id_tarjeta'])) {
            $id = intval($_POST['id_tarjeta']);
            $conn->query("DELETE FROM tarjetas_disposicion WHERE id_tarjeta = $id");
            header('Location: disposicionaulica.php?deleted=1');
            exit;
        } elseif ($_POST['action'] === 'duplicate_tarjeta' && isset($_POST['id_tarjeta'])) {
            $id = intval($_POST['id_tarjeta']);
            $nueva_fecha = $conn->real_escape_string($_POST['nueva_fecha']);
            
            // Obtener datos de la tarjeta original
            $original = $conn->query("SELECT * FROM tarjetas_disposicion WHERE id_tarjeta = $id")->fetch_assoc();
            
            // Verificar si el turno está lleno para la nueva fecha
            // Obtener la capacidad total de todas las aulas activas
            $sql_total_capacity = "SELECT SUM(capacidad) as capacidad_total FROM aulas WHERE estado = 'activa'";
            $result_total = $conn->query($sql_total_capacity);
            $total_capacity = $result_total->fetch_assoc()['capacidad_total'] ?? 0;
            
            // Obtener el número de estudiantes ya asignados en ese turno y fecha
            $sql_estudiantes_asignados = "
                SELECT SUM(t.cantidad_estudiantes) as estudiantes_asignados
                FROM tarjetas_disposicion t
                INNER JOIN itinerario i ON t.itinerario_id = i.id_itinerario
                WHERE t.fecha = ? AND i.id_turno = ?
            ";
            
            $stmt = $conn->prepare($sql_estudiantes_asignados);
            $stmt->bind_param('si', $nueva_fecha, $original['turno_id']);
            $stmt->execute();
            $result_estudiantes = $stmt->get_result();
            $estudiantes_asignados = $result_estudiantes->fetch_assoc()['estudiantes_asignados'] ?? 0;
            
            // Verificar si se puede crear más tarjetas en este turno
            if ($estudiantes_asignados >= $total_capacity) {
                header('Location: disposicionaulica.php?error=Turno lleno para la fecha seleccionada. Capacidad total: ' . $total_capacity . ', Estudiantes asignados: ' . $estudiantes_asignados);
                exit;
            }
            
            // Crear nueva tarjeta con la nueva fecha
            $sql = "INSERT INTO tarjetas_disposicion (fecha, turno_id, itinerario_id, materia_id, aula_id, profesor_id, cantidad_estudiantes, estado) 
                    VALUES ('$nueva_fecha', {$original['turno_id']}, {$original['itinerario_id']}, {$original['materia_id']}, {$original['aula_id']}, {$original['profesor_id']}, {$original['cantidad_estudiantes']}, 'duplicada')";
            
            if ($conn->query($sql)) {
                header('Location: disposicionaulica.php?duplicated=1');
                exit;
            }
        } elseif ($_POST['action'] === 'update_tarjeta' && isset($_POST['id_tarjeta'])) {
            $id_tarjeta = intval($_POST['id_tarjeta']);
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
                FROM tarjetas_disposicion t
                INNER JOIN itinerario i ON t.itinerario_id = i.id_itinerario
                WHERE t.fecha = ? AND i.id_turno = ? AND t.id_tarjeta != ?
            ";
            
            $stmt = $conn->prepare($sql_estudiantes_asignados);
            $stmt->bind_param('sii', $fecha, $turno_id, $id_tarjeta);
            $stmt->execute();
            $result_estudiantes = $stmt->get_result();
            $estudiantes_asignados = $result_estudiantes->fetch_assoc()['estudiantes_asignados'] ?? 0;
            
            // Verificar si se puede actualizar la tarjeta
            if (($estudiantes_asignados + $cantidad_estudiantes) > $total_capacity) {
                header('Location: disposicionaulica.php?error=Turno lleno para esta fecha. Capacidad total: ' . $total_capacity . ', Estudiantes asignados: ' . ($estudiantes_asignados + $cantidad_estudiantes));
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
    }
}

// Obtener parámetros de filtro
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$turno_filtro = isset($_GET['turno']) ? intval($_GET['turno']) : 0;

// Obtener datos para el calendario
$fecha_inicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
$fecha_fin = date('Y-m-t', strtotime($fecha_inicio));

// Consulta de tarjetas con filtros
$sql_tarjetas = "SELECT td.*, t.nombre AS turno_nombre, t.hora_inicio, t.hora_fin,
                        m.nombre AS materia_nombre, 
                        CONCAT(p.nombre, ' ', p.apellido) AS profesor_nombre,
                        a.numero AS aula_nombre, a.cantidad AS capacidad,
                        CONCAT(i.hora_inicio, ' - ', i.hora_fin) AS itinerario_nombre
                 FROM tarjetas_disposicion td
                 LEFT JOIN turnos t ON td.turno_id = t.id_turno
                 LEFT JOIN materias m ON td.materia_id = m.id_materia
                 LEFT JOIN profesores p ON td.profesor_id = p.id_profesor
                 LEFT JOIN aulas a ON td.aula_id = a.id_aula
                 LEFT JOIN itinerario i ON td.itinerario_id = i.id_itinerario
                 WHERE td.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

if ($turno_filtro > 0) {
    $sql_tarjetas .= " AND td.turno_id = $turno_filtro";
}

$sql_tarjetas .= " ORDER BY td.fecha, t.hora_inicio";
$tarjetas = $conn->query($sql_tarjetas);

// Organizar tarjetas por fecha
$tarjetas_por_fecha = [];
while ($tarjeta = $tarjetas->fetch_assoc()) {
    $fecha = $tarjeta['fecha'];
    if (!isset($tarjetas_por_fecha[$fecha])) {
        $tarjetas_por_fecha[$fecha] = [];
    }
    $tarjetas_por_fecha[$fecha][] = $tarjeta;
}

// Obtener datos para los formularios
$turnos = $conn->query("SELECT * FROM turnos ORDER BY hora_inicio");
$itinerarios = $conn->query("SELECT *, CONCAT(hora_inicio, ' - ', hora_fin) AS nombre FROM itinerario ORDER BY hora_inicio");
$materias = $conn->query("SELECT DISTINCT m.id_materia, m.nombre, m.carrera_id, m.curso_pre_admision_id, m.profesor_id,
                                c.nombre AS carrera_nombre, cpu.nombre_curso AS curso_nombre,
                                CONCAT(p.nombre, ' ', p.apellido) AS profesor_nombre
                         FROM materias m 
                         LEFT JOIN carreras c ON m.carrera_id = c.id_carrera 
                         LEFT JOIN cursos_pre_admisiones cpu ON m.curso_pre_admision_id = cpu.id_curso_pre_admision 
                         LEFT JOIN profesores p ON m.profesor_id = p.id_profesor
                         ORDER BY m.nombre");
$aulas = $conn->query("SELECT *, numero AS nombre, cantidad AS capacidad FROM aulas ORDER BY numero");
$profesores = $conn->query("SELECT * FROM profesores ORDER BY apellido, nombre");

// Nombres de meses
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Nombres de días
$dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Disposición Áulica</title>
    <link rel="stylesheet" href="css/disposicion.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fb;
        }

        .header {
            background: linear-gradient(135deg, #3B6CDC 0%, #6BD4E2 100%);
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }

        .header-links {
            margin-top: 15px;
        display: flex;
            gap: 20px;
            justify-content: center;
        }

        .header-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s;
            font-weight: 500;
        }

        .header-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .controls {
            background: white;
            padding: 20px;
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
        justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .month-navigation {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .month-navigation button {
            background: #3B6CDC;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .month-navigation button:hover {
            background: #2a5bb8;
        }

        .current-month {
            font-size: 1.5em;
            font-weight: 600;
            color: #333;
            min-width: 200px;
            text-align: center;
        }

        .filters {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filters select {
            padding: 8px 12px;
            border: 2px solid #e3eefd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
        }

        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background: #218838;
        }

        .calendar {
            margin: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #3B6CDC;
            color: white;
            font-weight: 600;
        }

        .calendar-header div {
            padding: 15px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .calendar-header div:last-child {
            border-right: none;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendar-day {
            min-height: 120px;
            border: 1px solid #e3eefd;
            padding: 10px;
            position: relative;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .calendar-day:hover {
            background: #f8faff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 108, 220, 0.15);
            border-color: #3B6CDC;
        }

        .calendar-day:active {
            transform: translateY(0);
        }

        .calendar-day.other-month {
            background: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day.today {
            background: #e3f2fd;
            border-color: #3B6CDC;
        }

        .day-number {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333;
            position: relative;
        }



        .tarjeta {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 6px 8px;
            margin: 2px 0;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tarjeta:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .tarjeta.duplicada {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        }

        .tarjeta.programada {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        }

        .add-tarjeta-day {
            position: absolute;
            bottom: 5px;
            right: 5px;
            z-index: 10;
        }

        .btn-add-day {
            background: #28a745;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .btn-add-day:hover {
            background: #218838;
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .btn-add-day:active {
            transform: scale(0.95);
        }

        .day-capacity-info {
            position: absolute;
            bottom: 40px;
            left: 5px;
            right: 5px;
            font-size: 10px;
            color: #666;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 4px;
            padding: 2px 4px;
            border: 1px solid #e3eefd;
        }

        .day-capacity-info.full {
            background: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #dc3545;
        }

        .day-capacity-info.available {
            background: rgba(40, 167, 69, 0.1);
            border-color: #28a745;
            color: #28a745;
        }

        /* Estilos para el modal de edición */
        #modalEditarTarjeta .modal-content {
            max-width: 600px;
        }

        #modalEditarTarjeta .form-group {
            margin-bottom: 15px;
        }

        #modalEditarTarjeta label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        #modalEditarTarjeta input,
        #modalEditarTarjeta select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        #modalEditarTarjeta input:focus,
        #modalEditarTarjeta select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
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
            background-color: white;
            margin: 1% auto 2% auto;
        padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            scrollbar-width: thin;
            scrollbar-color: #3B6CDC #f0f0f0;
        }

        .modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #3B6CDC;
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #2a5bb8;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e3eefd;
            border-radius: 6px;
            font-size: 14px;
        box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3B6CDC;
        }

        .search-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e3eefd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            margin-bottom: 8px;
            background: #f8faff;
        }

        .search-input:focus {
            outline: none;
            border-color: #3B6CDC;
            background: white;
        }

        .search-input::placeholder {
            color: #6c757d;
            font-style: italic;
        }

        /* Custom Select Styles */
        .custom-select-container {
            position: relative;
            width: 100%;
        }

        .custom-select-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 2px solid #e3eefd;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .custom-select-header:hover {
            border-color: #3B6CDC;
        }

        .custom-select-arrow {
            font-size: 12px;
            color: #6c757d;
            transition: transform 0.3s;
        }

        .custom-select-container.open .custom-select-arrow {
            transform: rotate(180deg);
        }

        .custom-select-dropdown {
        position: absolute;
            top: 100%;
        left: 0;
        right: 0;
            background: white;
            border: 2px solid #3B6CDC;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 300px;
            overflow: hidden;
            z-index: 1000;
            display: none;
        }

        .custom-select-container.open .custom-select-dropdown {
            display: block;
        }

        .custom-select-dropdown .search-input {
            margin: 0;
            border: none;
            border-bottom: 1px solid #e3eefd;
            border-radius: 0;
            background: #f8faff;
        }

        .custom-select-options {
            max-height: 250px;
            overflow-y: auto;
        }

        .custom-select-option {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .custom-select-option:hover {
            background-color: #f8faff;
        }

        .custom-select-option.selected {
            background-color: #3B6CDC;
            color: white;
        }

        .custom-select-option.hidden {
            display: none;
        }

        /* Estilos para las opciones de materia */
        .materia-option-content {
        display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .materia-nombre {
            font-weight: 600;
            color: #333;
        }

        .materia-tipo {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }

        .materia-profesor {
            font-size: 11px;
            color: #28a745;
            font-weight: 500;
        }



        /* Sección de profesor */
        .profesor-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .profesor-actual {
            background: #e3f2fd;
            border: 1px solid #3B6CDC;
            border-radius: 6px;
            padding: 15px;
        }

        .profesor-info {
            margin-bottom: 10px;
        }

        .profesor-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .profesor-selector {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Estilos para el dropdown de profesores */
        .profesor-dropdown-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 10px;
            border-bottom: 1px solid #e3eefd;
        }

        .profesor-dropdown-header .search-input {
            flex: 1;
        margin: 0;
            border: none;
            background: transparent;
        }

        .profesor-dropdown-actions {
            display: flex;
            gap: 5px;
        }

        .btn-icon {
            background: #3B6CDC;
            color: white;
            border: none;
            border-radius: 4px;
            width: 24px;
            height: 24px;
        cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .btn-icon:hover {
            background: #2a5bb8;
        }

        .profesor-option-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .profesor-nombre {
            flex: 1;
        }

        .profesor-option-actions {
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .profesor-option:hover .profesor-option-actions {
            opacity: 1;
        }

        .btn-icon-small {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
            padding: 2px;
            border-radius: 2px;
            transition: background 0.2s;
        }

        .btn-icon-small:hover {
            background: rgba(59, 108, 220, 0.1);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #3B6CDC;
            color: white;
        }

        .btn-primary:hover {
            background: #2a5bb8;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 20px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .info-box strong {
            color: #0056b3;
            display: block;
            margin-bottom: 10px;
        }

        .info-box span {
            color: #333;
            line-height: 1.6;
        }

        .form-group input[type="number"] {
            transition: border-color 0.3s ease;
        }

        .form-group input[type="number"]:focus {
            outline: none;
            border-color: #007bff;
        }

        .back-button {
            display: block;
            margin: 30px auto;
            max-width: 200px;
            background: #6c757d;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .month-navigation {
                justify-content: center;
            }
            
            .filters {
                justify-content: center;
            }
            
            .calendar-header div,
            .calendar-day {
                padding: 8px;
                font-size: 12px;
            }
            
            .modal-content {
                margin: 2% auto 2% auto;
                max-height: 80vh;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Calendario de Disposición Áulica</h1>
        <div class="header-links">
            <a href="materias.php" class="header-link">Materias</a>
            <a href="profesores.php" class="header-link">Profesores</a>
            <a href="detalle_dia.php?fecha=<?php echo date('Y-m-d'); ?>" class="header-link">Ver Hoy</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            Tarjeta creada exitosamente
            <?php if (isset($_GET['capacidad_restante'])): ?>
                <br><small>Capacidad restante en el turno: <?php echo intval($_GET['capacidad_restante']); ?> estudiantes</small>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success-message">Tarjeta eliminada exitosamente</div>
    <?php endif; ?>

    <?php if (isset($_GET['duplicated'])): ?>
        <div class="success-message">Tarjeta duplicada exitosamente</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="controls">
        <div class="month-navigation">
            <button onclick="changeMonth(-1)">Anterior</button>
            <div class="current-month"><?php echo $meses[$mes] . ' ' . $anio; ?></div>
            <button onclick="changeMonth(1)">Siguiente</button>
        </div>

        <div class="filters">
            <select onchange="filterByTurno(this.value)">
                <option value="0">Todos los turnos</option>
                <?php $turnos->data_seek(0); while ($turno = $turnos->fetch_assoc()): ?>
                    <option value="<?php echo $turno['id_turno']; ?>" <?php echo $turno_filtro == $turno['id_turno'] ? 'selected' : ''; ?>>
                        <?php echo $turno['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn-add" onclick="openModal()"> Nueva Tarjeta</button>
        </div>
    </div>

    <div class="calendar">

        <div class="calendar-header">
            <?php foreach ($dias_semana as $dia): ?>
                <div><?php echo $dia; ?></div>
                <?php endforeach; ?>
        </div>
        
        <div class="calendar-grid">
            <?php
            $primer_dia = new DateTime("$anio-$mes-01");
            $ultimo_dia = new DateTime("$anio-$mes-" . $primer_dia->format('t'));
            
            $inicio_calendario = clone $primer_dia;
            $inicio_calendario->modify('-' . $primer_dia->format('w') . ' days');
            
            $fin_calendario = clone $ultimo_dia;
            $fin_calendario->modify('+' . (6 - $ultimo_dia->format('w')) . ' days');
            
            $fecha_actual = clone $inicio_calendario;
            
            while ($fecha_actual <= $fin_calendario):
                $fecha_str = $fecha_actual->format('Y-m-d');
                $es_otro_mes = $fecha_actual->format('n') != $mes;
                $es_hoy = $fecha_actual->format('Y-m-d') == date('Y-m-d');
                $clase_dia = $es_otro_mes ? 'other-month' : '';
                if ($es_hoy) $clase_dia .= ' today';
            ?>
                <div class="calendar-day <?php echo $clase_dia; ?>" data-fecha="<?php echo $fecha_str; ?>" onclick="openDayDetail('<?php echo $fecha_str; ?>')">
                    <div class="day-number"><?php echo $fecha_actual->format('j'); ?></div>
                    
                    <?php if (isset($tarjetas_por_fecha[$fecha_str])): ?>
                        <?php foreach ($tarjetas_por_fecha[$fecha_str] as $tarjeta): ?>
                            <div class="tarjeta <?php echo $tarjeta['estado']; ?>" 
                                 onclick="event.stopPropagation(); showTarjetaDetails(<?php echo htmlspecialchars(json_encode($tarjeta)); ?>)">
                                <strong><?php echo $tarjeta['turno_nombre']; ?></strong><br>
                                <?php echo $tarjeta['materia_nombre']; ?><br>
                                <small><?php echo $tarjeta['aula_nombre']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Información de capacidad del día -->
                    <?php if (!$es_otro_mes): ?>
                        <div class="day-capacity-info" id="capacity-<?php echo $fecha_str; ?>">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                        
                        <!-- Botón para crear tarjeta en este día específico - SIEMPRE visible -->
                        <div class="add-tarjeta-day" onclick="event.stopPropagation(); openModalForDate('<?php echo $fecha_str; ?>')">
                            <button class="btn-add-day" title="Crear tarjeta para este día">
                                <span>+</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
                $fecha_actual->modify('+1 day');
            endwhile;
            ?>
        </div>
    </div>

    <!-- Modal para crear nueva tarjeta -->
    <div id="modalNuevaTarjeta" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nueva Tarjeta de Disposición</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_tarjeta">
                
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" required>
                </div>
                
                <div class="form-group">
                    <label>Turno:</label>
                    <select name="turno_id" required>
                        <option value="">Seleccionar turno</option>
                        <?php $turnos->data_seek(0); while ($turno = $turnos->fetch_assoc()): ?>
                            <option value="<?php echo $turno['id_turno']; ?>">
                                <?php echo $turno['nombre']; ?> (<?php echo $turno['hora_inicio']; ?> - <?php echo $turno['hora_fin']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Horario:</label>
                    <select name="itinerario_id" id="itinerario_select" required disabled>
                        <option value="">Primero selecciona un turno</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Materia:</label>
                    <div class="custom-select-container">
                        <div class="custom-select-header" onclick="toggleMateriaSelect()">
                            <span id="materia_selected_text">Seleccionar materia</span>
                            <span class="custom-select-arrow">▼</span>
                        </div>
                        <div class="custom-select-dropdown" id="materia_dropdown">
                            <input type="text" id="buscar_materia" placeholder="Buscar materia..." class="search-input">
                            <div class="custom-select-options" id="materia_options">
                                <?php $materias->data_seek(0); while ($materia = $materias->fetch_assoc()): ?>
                                    <div class="custom-select-option" 
                                         data-value="<?php echo $materia['id_materia']; ?>" 
                                         data-nombre="<?php echo htmlspecialchars($materia['nombre']); ?>"
                                         data-carrera="<?php echo htmlspecialchars($materia['carrera_nombre'] ?? ''); ?>"
                                         data-curso="<?php echo htmlspecialchars($materia['curso_nombre'] ?? ''); ?>"
                                         data-profesor-id="<?php echo $materia['profesor_id'] ?? ''; ?>"
                                         data-profesor-nombre="<?php echo htmlspecialchars($materia['profesor_nombre'] ?? ''); ?>">
                                        <div class="materia-option-content">
                                            <div class="materia-nombre"><?php echo $materia['nombre']; ?></div>
                                            <?php if ($materia['carrera_nombre']): ?>
                                                <?php 
                                                $esDiplomatura = stripos($materia['carrera_nombre'], 'diplomatura') !== false;
                                                $tipo = $esDiplomatura ? 'Diplomatura' : 'Carrera';
                                                ?>
                                                <div class="materia-tipo"><?php echo $tipo; ?>: <?php echo $materia['carrera_nombre']; ?></div>
                                            <?php elseif ($materia['curso_nombre']): ?>
                                                <div class="materia-tipo">Curso: <?php echo $materia['curso_nombre']; ?></div>
                                            <?php endif; ?>
                                            <?php if ($materia['profesor_nombre']): ?>
                                                <div class="materia-profesor">Profesor: <?php echo $materia['profesor_nombre']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <input type="hidden" name="materia_id" id="materia_hidden" required>
                    </div>
                </div>



                <!-- Sección de profesor (inicialmente oculta) -->
                <div id="profesor_section" class="form-group" style="display: none;">
                    <label>Profesor:</label>
                    <div class="profesor-options">
                        <div id="profesor_actual" class="profesor-actual" style="display: none;">
                            <div class="profesor-info">
                                <strong>Profesor actual:</strong> <span id="profesor_nombre_actual"></span>
                            </div>
                            <div class="profesor-actions">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="cambiarProfesor()">Cambiar Profesor</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarRelacionProfesor()">Eliminar Profesor</button>
                            </div>
                        </div>
                        
                        <div id="profesor_selector" class="profesor-selector" style="display: none;">
                            <div class="custom-select-container">
                                <div class="custom-select-header" onclick="toggleProfesorSelect()">
                                    <span id="profesor_selected_text">Seleccionar profesor</span>
                                    <span class="custom-select-arrow">▼</span>
                                </div>
                                <div class="custom-select-dropdown" id="profesor_dropdown">
                                    <div class="profesor-dropdown-header">
                                        <input type="text" id="buscar_profesor" placeholder="Buscar profesor..." class="search-input">
                                        <div class="profesor-dropdown-actions">
                                            <button type="button" class="btn-icon" onclick="agregarNuevoProfesor()" title="Agregar profesor">
                                                <span>+</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="custom-select-options" id="profesor_options">
                                        <?php $profesores->data_seek(0); while ($profesor = $profesores->fetch_assoc()): ?>
                                            <div class="custom-select-option profesor-option" 
                                                 data-value="<?php echo $profesor['id_profesor']; ?>" 
                                                 data-nombre="<?php echo htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']); ?>"
                                                 data-profesor='<?php echo json_encode($profesor); ?>'>
                                                <div class="profesor-option-content">
                                                    <span class="profesor-nombre"><?php echo $profesor['nombre'] . ' ' . $profesor['apellido']; ?></span>
                                                    <div class="profesor-option-actions">
                                                        <button type="button" class="btn-icon-small" onclick="editarProfesor(<?php echo $profesor['id_profesor']; ?>)" title="Editar">
                                                            ✏️
                                                        </button>
                                                        <button type="button" class="btn-icon-small" onclick="eliminarProfesor(<?php echo $profesor['id_profesor']; ?>)" title="Eliminar">
                                                            🗑️
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <input type="hidden" name="profesor_id" id="profesor_hidden" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Aula:</label>
                    <select name="aula_id" required>
                        <option value="">Seleccionar aula</option>
                        <?php $aulas->data_seek(0); while ($aula = $aulas->fetch_assoc()): ?>
                            <option value="<?php echo $aula['id_aula']; ?>">
                                <?php echo $aula['nombre']; ?> (Cap: <?php echo $aula['capacidad']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cantidad de Estudiantes:</label>
                    <input type="number" name="cantidad_estudiantes" min="1" required>
                </div>

                <!-- Información de capacidad del turno -->
                <div id="info_capacidad_turno" class="form-group" style="display: none;">
                    <div class="info-box">
                        <strong>Información del Turno:</strong>
                        <div id="capacidad_info">
                            <span>Capacidad total del turno: <span id="capacidad_total">0</span> estudiantes</span><br>
                            <span>Estudiantes ya asignados: <span id="estudiantes_asignados">0</span></span><br>
                            <span>Capacidad disponible: <span id="capacidad_disponible">0</span> estudiantes</span>
                        </div>
                        <div style="margin-top: 10px; font-size: 14px; color: #666;">
                            <em>💡 Puedes crear múltiples tarjetas en el mismo turno hasta que se alcance la capacidad total.</em>
                        </div>
                    </div>
                </div>
                

                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarjeta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para gestionar profesores -->
    <div id="modalProfesor" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalProfesorTitulo">Agregar Nuevo Profesor</h2>
                <span class="close" onclick="closeProfesorModal()">&times;</span>
            </div>
            <form id="formProfesor" method="POST">
                <input type="hidden" name="action" value="gestionar_profesor">
                <input type="hidden" name="profesor_id" id="profesor_id_modal">
                
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" id="profesor_nombre" required>
                </div>
                
                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido" id="profesor_apellido" required>
                </div>
                
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" id="profesor_correo">
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" name="telefono" id="profesor_telefono">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeProfesorModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarProfesor">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para detalles de tarjeta -->
    <div id="modalDetallesTarjeta" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles de la Tarjeta</h2>
                <span class="close" onclick="closeDetallesModal()">&times;</span>
            </div>
            <div id="detallesTarjeta">
                <!-- Los detalles se cargarán aquí -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeDetallesModal()">Cerrar</button>
                <button class="btn btn-warning" onclick="editarTarjeta()">Editar</button>
                <button class="btn btn-primary" onclick="duplicarTarjeta()">Duplicar</button>
                <button class="btn btn-danger" onclick="eliminarTarjeta()">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Modal para editar tarjeta -->
    <div id="modalEditarTarjeta" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Tarjeta</h2>
                <span class="close" onclick="closeEditarModal()">&times;</span>
            </div>
            <form id="formEditarTarjeta" method="POST">
                <input type="hidden" name="action" value="update_tarjeta">
                <input type="hidden" name="id_tarjeta" id="edit_id_tarjeta">
                
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" id="edit_fecha" required>
                </div>
                
                <div class="form-group">
                    <label>Turno:</label>
                    <select name="turno_id" id="edit_turno_id" required>
                        <option value="">Seleccionar turno</option>
                        <?php $turnos->data_seek(0); while ($turno = $turnos->fetch_assoc()): ?>
                            <option value="<?php echo $turno['id_turno']; ?>">
                                <?php echo $turno['nombre']; ?> (<?php echo $turno['hora_inicio']; ?> - <?php echo $turno['hora_fin']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Horario:</label>
                    <select name="itinerario_id" id="edit_itinerario_id" required>
                        <option value="">Seleccionar horario</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Materia:</label>
                    <select name="materia_id" id="edit_materia_id" required>
                        <option value="">Seleccionar materia</option>
                        <?php $materias->data_seek(0); while ($materia = $materias->fetch_assoc()): ?>
                            <option value="<?php echo $materia['id_materia']; ?>">
                                <?php echo $materia['nombre']; ?>
                                <?php if ($materia['carrera_nombre']): ?>
                                    (<?php echo $materia['carrera_nombre']; ?>)
                                <?php endif; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Profesor:</label>
                    <select name="profesor_id" id="edit_profesor_id" required>
                        <option value="">Seleccionar profesor</option>
                        <?php $profesores->data_seek(0); while ($profesor = $profesores->fetch_assoc()): ?>
                            <option value="<?php echo $profesor['id_profesor']; ?>">
                                <?php echo $profesor['nombre'] . ' ' . $profesor['apellido']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Aula:</label>
                    <select name="aula_id" id="edit_aula_id" required>
                        <option value="">Seleccionar aula</option>
                        <?php $aulas->data_seek(0); while ($aula = $aulas->fetch_assoc()): ?>
                            <option value="<?php echo $aula['id_aula']; ?>">
                                <?php echo $aula['nombre']; ?> (Cap: <?php echo $aula['capacidad']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Cantidad de Estudiantes:</label>
                    <input type="number" name="cantidad_estudiantes" id="edit_cantidad_estudiantes" min="1" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditarModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Tarjeta</button>
                </div>
            </form>
        </div>
    </div>

    <a href="index.php" class="back-button">← Volver al Inicio</a>

    <script>
        let tarjetaActual = null;
        let itinerariosData = <?php 
            $itinerarios_array = [];
            $itinerarios->data_seek(0);
            while ($itinerario = $itinerarios->fetch_assoc()) {
                $itinerarios_array[] = $itinerario;
            }
            echo json_encode($itinerarios_array);
        ?>;

        function openDayDetail(fecha) {
            window.location.href = `detalle_dia.php?fecha=${fecha}`;
        }

        function changeMonth(delta) {
            const urlParams = new URLSearchParams(window.location.search);
            let mes = parseInt(urlParams.get('mes')) || new Date().getMonth() + 1;
            let anio = parseInt(urlParams.get('anio')) || new Date().getFullYear();
            
            mes += delta;
            if (mes > 12) {
                mes = 1;
                anio++;
            } else if (mes < 1) {
                mes = 12;
                anio--;
            }
            
            urlParams.set('mes', mes);
            urlParams.set('anio', anio);
            window.location.search = urlParams.toString();
        }

        function filterByTurno(turnoId) {
            const urlParams = new URLSearchParams(window.location.search);
            if (turnoId > 0) {
                urlParams.set('turno', turnoId);
            } else {
                urlParams.delete('turno');
            }
            window.location.search = urlParams.toString();
        }

        function openModal() {
            document.getElementById('modalNuevaTarjeta').style.display = 'block';
        }

        function openModalForDate(fecha) {
            document.getElementById('modalNuevaTarjeta').style.display = 'block';
            document.querySelector('input[name="fecha"]').value = fecha;
        }

        function closeModal() {
            document.getElementById('modalNuevaTarjeta').style.display = 'none';
            
            // Limpiar búsqueda de materias
            document.getElementById('buscar_materia').value = '';
            // Resetear select de materias
            document.getElementById('materia_hidden').value = '';
            document.getElementById('materia_selected_text').textContent = 'Seleccionar materia';
            // Mostrar todas las materias
            document.querySelectorAll('#materia_options .custom-select-option').forEach(option => {
                option.classList.remove('hidden', 'selected');
            });
            
            // Limpiar búsqueda de profesores
            document.getElementById('buscar_profesor').value = '';
            // Resetear select de profesores
            document.getElementById('profesor_hidden').value = '';
            document.getElementById('profesor_selected_text').textContent = 'Seleccionar profesor';
            // Mostrar todos los profesores
            document.querySelectorAll('#profesor_options .custom-select-option').forEach(option => {
                option.classList.remove('hidden', 'selected');
            });
            
            // Ocultar secciones dinámicas
            document.getElementById('profesor_section').style.display = 'none';
            document.getElementById('profesor_actual').style.display = 'none';
            document.getElementById('profesor_selector').style.display = 'none';
            
            // Cerrar todos los dropdowns
            document.querySelectorAll('.custom-select-container').forEach(container => {
                container.classList.remove('open');
                const dropdown = container.querySelector('.custom-select-dropdown');
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function showTarjetaDetails(tarjeta) {
            tarjetaActual = tarjeta;
            const detalles = document.getElementById('detallesTarjeta');
            
            detalles.innerHTML = `
                <div style="margin-bottom: 15px;">
                    <strong>Fecha:</strong> ${new Date(tarjeta.fecha).toLocaleDateString('es-ES')}
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Turno:</strong> ${tarjeta.turno_nombre} (${tarjeta.hora_inicio} - ${tarjeta.hora_fin})
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Itinerario:</strong> ${tarjeta.itinerario_nombre}
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Materia:</strong> ${tarjeta.materia_nombre}
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Profesor:</strong> ${tarjeta.profesor_nombre}
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Aula:</strong> ${tarjeta.aula_nombre} (Capacidad: ${tarjeta.capacidad})
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Estado:</strong> <span style="text-transform: capitalize;">${tarjeta.estado}</span>
                </div>
            `;
            
            document.getElementById('modalDetallesTarjeta').style.display = 'block';
        }

        function closeDetallesModal() {
            document.getElementById('modalDetallesTarjeta').style.display = 'none';
            tarjetaActual = null;
        }

        function closeEditarModal() {
            document.getElementById('modalEditarTarjeta').style.display = 'none';
        }

        function filtrarItinerariosEdicion(turnoId) {
            const itinerarioSelect = document.getElementById('edit_itinerario_id');
            itinerarioSelect.innerHTML = '<option value="">Seleccionar horario</option>';
            
            if (turnoId) {
                const itinerariosFiltrados = itinerariosData.filter(item => item.turno_id == turnoId);
                itinerariosFiltrados.forEach(itinerario => {
                    const option = document.createElement('option');
                    option.value = itinerario.id_itinerario;
                    option.textContent = `${itinerario.hora_inicio} - ${itinerario.hora_fin}`;
                    itinerarioSelect.appendChild(option);
                });
                
                // Seleccionar el itinerario actual si existe
                if (tarjetaActual && tarjetaActual.itinerario_id) {
                    itinerarioSelect.value = tarjetaActual.itinerario_id;
                }
            }
        }

        function duplicarTarjeta() {
            if (!tarjetaActual) return;
            
            const nuevaFecha = prompt('Ingrese la nueva fecha (YYYY-MM-DD):', tarjetaActual.fecha);
            if (nuevaFecha) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
                form.innerHTML = `
                    <input type="hidden" name="action" value="duplicate_tarjeta">
                    <input type="hidden" name="id_tarjeta" value="${tarjetaActual.id_tarjeta}">
                    <input type="hidden" name="nueva_fecha" value="${nuevaFecha}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editarTarjeta() {
            if (!tarjetaActual) return;
            
            // Llenar el formulario de edición con los datos actuales
            document.getElementById('edit_id_tarjeta').value = tarjetaActual.id_tarjeta;
            document.getElementById('edit_fecha').value = tarjetaActual.fecha;
            document.getElementById('edit_turno_id').value = tarjetaActual.turno_id;
            document.getElementById('edit_materia_id').value = tarjetaActual.materia_id;
            document.getElementById('edit_profesor_id').value = tarjetaActual.profesor_id;
            document.getElementById('edit_aula_id').value = tarjetaActual.aula_id;
            document.getElementById('edit_cantidad_estudiantes').value = tarjetaActual.cantidad_estudiantes || 0;
            
            // Cargar itinerarios para el turno seleccionado
            filtrarItinerariosEdicion(tarjetaActual.turno_id);
            
            // Cerrar modal de detalles y abrir modal de edición
            closeDetallesModal();
            document.getElementById('modalEditarTarjeta').style.display = 'block';
        }

        function eliminarTarjeta() {
            if (!tarjetaActual) return;
            
            if (confirm('¿Está seguro de que desea eliminar esta tarjeta?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_tarjeta">
                    <input type="hidden" name="id_tarjeta" value="${tarjetaActual.id_tarjeta}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Filtrar itinerarios por turno seleccionado
        function filtrarItinerariosPorTurno(turnoId) {
            const itinerarioSelect = document.getElementById('itinerario_select');
            itinerarioSelect.innerHTML = '<option value="">Seleccionar horario</option>';
            
            if (turnoId) {
                const itinerariosFiltrados = itinerariosData.filter(item => item.turno_id == turnoId);
                itinerariosFiltrados.forEach(itinerario => {
                    const option = document.createElement('option');
                    option.value = itinerario.id_itinerario;
                    option.textContent = `${itinerario.hora_inicio} - ${itinerario.hora_fin}`;
                    itinerarioSelect.appendChild(option);
                });
                itinerarioSelect.disabled = false;
            } else {
                itinerarioSelect.disabled = true;
            }
        }

        // Verificar disponibilidad de aula
        function verificarDisponibilidadAula() {
            const fecha = document.querySelector('input[name="fecha"]').value;
            const turnoId = document.querySelector('select[name="turno_id"]').value;
            const itinerarioId = document.querySelector('select[name="itinerario_id"]').value;
            const aulaId = document.querySelector('select[name="aula_id"]').value;
            
            if (fecha && turnoId && itinerarioId && aulaId) {
                // Aquí podrías hacer una llamada AJAX para verificar disponibilidad
                // Por ahora solo mostramos un mensaje
                console.log('Verificando disponibilidad para:', {fecha, turnoId, itinerarioId, aulaId});
            }
        }

        // Función para abrir/cerrar el select de materias
        function toggleMateriaSelect() {
            const container = document.querySelector('.custom-select-container');
            const dropdown = document.getElementById('materia_dropdown');
            
            if (container.classList.contains('open')) {
                container.classList.remove('open');
                dropdown.style.display = 'none';
            } else {
                container.classList.add('open');
                dropdown.style.display = 'block';
                document.getElementById('buscar_materia').focus();
            }
        }

        // Función para normalizar texto (remover acentos)
        function normalizarTexto(texto) {
            return texto.normalize('NFD')
                       .replace(/[\u0300-\u036f]/g, '') // Remover diacríticos
                       .toLowerCase();
        }

        // Función para filtrar materias por búsqueda
        function filtrarMaterias(busqueda) {
            const options = document.querySelectorAll('.custom-select-option');
            const textoBusquedaNormalizado = normalizarTexto(busqueda);
            
            options.forEach(option => {
                const nombre = option.getAttribute('data-nombre');
                const nombreNormalizado = normalizarTexto(nombre);
                
                if (nombreNormalizado.includes(textoBusquedaNormalizado)) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        }

        // Función para seleccionar una materia
        function selectMateria(element) {
            const value = element.getAttribute('data-value');
            const nombre = element.getAttribute('data-nombre');
            const carrera = element.getAttribute('data-carrera');
            const curso = element.getAttribute('data-curso');
            const profesorId = element.getAttribute('data-profesor-id');
            
            document.getElementById('materia_hidden').value = value;
            document.getElementById('materia_selected_text').textContent = nombre;
            
            // Remover selección anterior
            document.querySelectorAll('#materia_options .custom-select-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Marcar como seleccionada
            element.classList.add('selected');
            
            // Cerrar dropdown
            document.querySelector('.custom-select-container').classList.remove('open');
            document.getElementById('materia_dropdown').style.display = 'none';
            
            // Manejar la sección de profesor
            manejarSeccionProfesor(profesorId);
        }



        // Función para manejar la sección de profesor
        function manejarSeccionProfesor(profesorId) {
            const profesorSection = document.getElementById('profesor_section');
            const profesorActual = document.getElementById('profesor_actual');
            const profesorSelector = document.getElementById('profesor_selector');
            
            profesorSection.style.display = 'block';
            
            if (profesorId && profesorId !== '') {
                // La materia tiene un profesor asignado
                mostrarProfesorActual(profesorId);
                profesorActual.style.display = 'block';
                profesorSelector.style.display = 'none';
            } else {
                // La materia no tiene profesor asignado
                profesorActual.style.display = 'none';
                profesorSelector.style.display = 'block';
                document.getElementById('profesor_hidden').value = '';
            }
        }

        // Función para mostrar el profesor actual
        function mostrarProfesorActual(profesorId) {
            // Buscar el profesor en las opciones disponibles
            const profesorOption = document.querySelector(`#profesor_options .custom-select-option[data-value="${profesorId}"]`);
            if (profesorOption) {
                const nombreProfesor = profesorOption.getAttribute('data-nombre');
                document.getElementById('profesor_nombre_actual').textContent = nombreProfesor;
                document.getElementById('profesor_hidden').value = profesorId;
            }
        }

        // Función para cambiar profesor
        function cambiarProfesor() {
            document.getElementById('profesor_actual').style.display = 'none';
            document.getElementById('profesor_selector').style.display = 'block';
            document.getElementById('profesor_hidden').value = '';
        }

        // Función para eliminar profesor
        function eliminarProfesor() {
            document.getElementById('profesor_actual').style.display = 'none';
            document.getElementById('profesor_selector').style.display = 'block';
            document.getElementById('profesor_hidden').value = '';
            document.getElementById('profesor_nombre_actual').textContent = '';
        }

        // Variables globales para gestión de profesores
        let modoProfesor = 'agregar'; // 'agregar' o 'editar'
        let profesorActual = null;

        // Función para agregar nuevo profesor
        function agregarNuevoProfesor() {
            modoProfesor = 'agregar';
            document.getElementById('modalProfesorTitulo').textContent = 'Agregar Nuevo Profesor';
            document.getElementById('btnGuardarProfesor').textContent = 'Guardar';
            
            // Limpiar formulario
            document.getElementById('formProfesor').reset();
            document.getElementById('profesor_id_modal').value = '';
            
            // Mostrar modal
            document.getElementById('modalProfesor').style.display = 'block';
        }

        // Función para editar profesor
        function editarProfesor(profesorId) {
            modoProfesor = 'editar';
            document.getElementById('modalProfesorTitulo').textContent = 'Editar Profesor';
            document.getElementById('btnGuardarProfesor').textContent = 'Actualizar';
            
            // Buscar datos del profesor
            const profesorOption = document.querySelector(`.profesor-option[data-value="${profesorId}"]`);
            if (profesorOption) {
                const profesorData = JSON.parse(profesorOption.getAttribute('data-profesor'));
                profesorActual = profesorData;
                
                // Llenar formulario
                document.getElementById('profesor_id_modal').value = profesorData.id_profesor;
                document.getElementById('profesor_nombre').value = profesorData.nombre;
                document.getElementById('profesor_apellido').value = profesorData.apellido;
                document.getElementById('profesor_correo').value = profesorData.correo || '';
                document.getElementById('profesor_telefono').value = profesorData.telefono || '';
                
                // Mostrar modal
                document.getElementById('modalProfesor').style.display = 'block';
            }
        }

        // Función para eliminar profesor
        function eliminarProfesor(profesorId) {
            if (confirm('¿Está seguro de que desea eliminar este profesor?')) {
                // Crear formulario para eliminar
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="eliminar_profesor">
                    <input type="hidden" name="profesor_id" value="${profesorId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Función para eliminar relación profesor-materia (desde la sección de profesor actual)
        function eliminarRelacionProfesor() {
            const materiaId = document.getElementById('materia_hidden').value;
            
            if (materiaId) {
                const formData = new FormData();
                formData.append('action', 'eliminar_relacion_materia_profesor');
                formData.append('materia_id', materiaId);
                
                fetch('gestionar_profesores.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar selector de profesores
                        document.getElementById('profesor_actual').style.display = 'none';
                        document.getElementById('profesor_selector').style.display = 'block';
                        document.getElementById('profesor_hidden').value = '';
                        document.getElementById('profesor_nombre_actual').textContent = '';
                    } else {
                        alert('Error al eliminar relación: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error en la petición:', error);
                    alert('Error al eliminar la relación');
                });
            }
        }

        // Función para cerrar modal de profesor
        function closeProfesorModal() {
            document.getElementById('modalProfesor').style.display = 'none';
            profesorActual = null;
        }

        // Función para abrir/cerrar el select de profesores
        function toggleProfesorSelect() {
            const container = document.querySelectorAll('.custom-select-container')[1]; // El segundo container
            const dropdown = document.getElementById('profesor_dropdown');
            
            if (container.classList.contains('open')) {
                container.classList.remove('open');
                dropdown.style.display = 'none';
            } else {
                container.classList.add('open');
                dropdown.style.display = 'block';
                document.getElementById('buscar_profesor').focus();
            }
        }

        // Función para filtrar profesores por búsqueda
        function filtrarProfesores(busqueda) {
            const options = document.querySelectorAll('#profesor_options .custom-select-option');
            const textoBusquedaNormalizado = normalizarTexto(busqueda);
            
            options.forEach(option => {
                const nombre = option.getAttribute('data-nombre');
                const nombreNormalizado = normalizarTexto(nombre);
                
                if (nombreNormalizado.includes(textoBusquedaNormalizado)) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        }

        // Función para seleccionar un profesor
        function selectProfesor(element) {
            const value = element.getAttribute('data-value');
            const nombre = element.getAttribute('data-nombre');
            const materiaId = document.getElementById('materia_hidden').value;
            
            document.getElementById('profesor_hidden').value = value;
            document.getElementById('profesor_selected_text').textContent = nombre;
            
            // Remover selección anterior
            document.querySelectorAll('#profesor_options .custom-select-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Marcar como seleccionada
            element.classList.add('selected');
            
            // Cerrar dropdown
            document.querySelectorAll('.custom-select-container')[1].classList.remove('open');
            document.getElementById('profesor_dropdown').style.display = 'none';
            
            // Actualizar la relación materia-profesor en la base de datos
            if (materiaId && value) {
                actualizarRelacionMateriaProfesor(materiaId, value);
            }
        }

        // Función para actualizar la relación materia-profesor
        function actualizarRelacionMateriaProfesor(materiaId, profesorId) {
            const formData = new FormData();
            formData.append('action', 'actualizar_relacion_materia_profesor');
            formData.append('materia_id', materiaId);
            formData.append('profesor_id', profesorId);
            
            fetch('gestionar_profesores.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Relación materia-profesor actualizada correctamente');
                    // Opcional: mostrar mensaje de éxito
                    // alert('Profesor asignado a la materia correctamente');
                } else {
                    console.error('Error al actualizar relación:', data.message);
                    // Opcional: mostrar mensaje de error
                    // alert('Error al asignar profesor: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
            });
        }

        // Función para ocultar mensajes automáticamente
        function ocultarMensajes() {
            const mensajes = document.querySelectorAll('.success-message');
            mensajes.forEach(mensaje => {
                setTimeout(() => {
                    mensaje.style.opacity = '0';
                    setTimeout(() => {
                        mensaje.style.display = 'none';
                    }, 300);
                }, 3000);
            });
        }

        // Event listeners para el formulario
        document.addEventListener('DOMContentLoaded', function() {
            const turnoSelect = document.querySelector('select[name="turno_id"]');
            const itinerarioSelect = document.getElementById('itinerario_select');
            const fechaInput = document.querySelector('input[name="fecha"]');
            const aulaSelect = document.querySelector('select[name="aula_id"]');
            const buscarMateria = document.getElementById('buscar_materia');
            const buscarProfesor = document.getElementById('buscar_profesor');
            
            turnoSelect.addEventListener('change', function() {
                filtrarItinerariosPorTurno(this.value);
            });
            
            itinerarioSelect.addEventListener('change', verificarDisponibilidadAula);
            aulaSelect.addEventListener('change', verificarDisponibilidadAula);
            fechaInput.addEventListener('change', verificarDisponibilidadAula);
            
            // Búsqueda de materias
            buscarMateria.addEventListener('input', function() {
                filtrarMaterias(this.value);
            });
            
            // Búsqueda de profesores
            buscarProfesor.addEventListener('input', function() {
                filtrarProfesores(this.value);
            });
            
            // Event listeners para las opciones de materia
            document.querySelectorAll('#materia_options .custom-select-option').forEach(option => {
                option.addEventListener('click', function() {
                    selectMateria(this);
                });
            });
            
            // Event listeners para las opciones de profesor
            document.querySelectorAll('#profesor_options .custom-select-option').forEach(option => {
                option.addEventListener('click', function() {
                    selectProfesor(this);
                });
            });
            
            // Cerrar dropdowns al hacer clic fuera
            document.addEventListener('click', function(e) {
                const containers = document.querySelectorAll('.custom-select-container');
                containers.forEach(container => {
                    if (!container.contains(e.target)) {
                        container.classList.remove('open');
                        const dropdown = container.querySelector('.custom-select-dropdown');
                        if (dropdown) {
                            dropdown.style.display = 'none';
                        }
                    }
                });
            });
            
            // Ocultar mensajes automáticamente
            ocultarMensajes();

            // Cargar información de capacidad de todos los días
            cargarCapacidadDias();

            // Event listener para calcular capacidad del turno
            turnoSelect.addEventListener('change', function() {
                calcularCapacidadTurno();
            });
            
            fechaInput.addEventListener('change', function() {
                if (turnoSelect.value) {
                    calcularCapacidadTurno();
                }
            });

            // Event listener para el turno en el modal de edición
            document.getElementById('edit_turno_id').addEventListener('change', function() {
                filtrarItinerariosEdicion(this.value);
            });
            
            // Event listener para el formulario de creación de tarjetas
            document.querySelector('form[action=""]').addEventListener('submit', function(e) {
                const cantidadEstudiantes = parseInt(document.querySelector('input[name="cantidad_estudiantes"]').value);
                const capacidadDisponible = parseInt(document.getElementById('capacidad_disponible').textContent);
                
                if (cantidadEstudiantes > capacidadDisponible) {
                    e.preventDefault();
                    alert(`No se puede crear la tarjeta. La cantidad de estudiantes (${cantidadEstudiantes}) excede la capacidad disponible (${capacidadDisponible}) en este turno.`);
                    return false;
                }
            });

            // Event listener para la cantidad de estudiantes
            document.querySelector('input[name="cantidad_estudiantes"]').addEventListener('input', function() {
                if (this.value && document.getElementById('info_capacidad_turno').style.display !== 'none') {
                    const cantidadEstudiantes = parseInt(this.value) || 0;
                    const capacidadDisponible = parseInt(document.getElementById('capacidad_disponible').textContent);
                    
                    if (cantidadEstudiantes > capacidadDisponible) {
                        this.style.borderColor = '#dc3545';
                        this.title = `La cantidad excede la capacidad disponible (${capacidadDisponible})`;
                    } else {
                        this.style.borderColor = '#28a745';
                        this.title = '';
                    }
                }
            });

            // Event listener para el formulario de profesores
            document.getElementById('formProfesor').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('modo', modoProfesor);
                
                fetch('gestionar_profesores.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeProfesorModal();
                        // Recargar la página para actualizar la lista
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            });
        });

        // Función para calcular la capacidad del turno
        function calcularCapacidadTurno() {
            const fecha = document.querySelector('input[name="fecha"]').value;
            const turnoId = document.querySelector('select[name="turno_id"]').value;
            
            if (!fecha || !turnoId) {
                document.getElementById('info_capacidad_turno').style.display = 'none';
                return;
            }
            
            // Hacer petición AJAX para obtener la capacidad del turno
            fetch('get_capacidad_turno.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `fecha=${fecha}&turno_id=${turnoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('capacidad_total').textContent = data.capacidad_total;
                    document.getElementById('estudiantes_asignados').textContent = data.estudiantes_asignados;
                    document.getElementById('capacidad_disponible').textContent = data.capacidad_disponible;
                    document.getElementById('info_capacidad_turno').style.display = 'block';
                } else {
                    console.error('Error al obtener capacidad:', data.message);
                    document.getElementById('info_capacidad_turno').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('info_capacidad_turno').style.display = 'none';
            });
        }

        // Función para cargar la información de capacidad de todos los días del mes
        function cargarCapacidadDias() {
            const dias = document.querySelectorAll('.calendar-day:not(.other-month)');
            dias.forEach(dia => {
                const fecha = dia.getAttribute('data-fecha');
                if (fecha) {
                    cargarCapacidadDia(fecha);
                }
            });
        }

        // Función para cargar la información de capacidad de un día específico
        function cargarCapacidadDia(fecha) {
            // Obtener todos los turnos disponibles
            const turnos = <?php 
                $turnos_array = [];
                $turnos->data_seek(0);
                while ($turno = $turnos->fetch_assoc()) {
                    $turnos_array[] = $turno;
                }
                echo json_encode($turnos_array);
            ?>;
            
            let capacidadTotal = 0;
            let estudiantesAsignados = 0;
            
            // Calcular capacidad total de todas las aulas
            fetch('get_capacidad_turno.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `fecha=${fecha}&turno_id=0`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const capacityElement = document.getElementById(`capacity-${fecha}`);
                    if (capacityElement) {
                        if (data.capacidad_disponible <= 0) {
                            capacityElement.innerHTML = '<strong>Turno lleno</strong>';
                            capacityElement.className = 'day-capacity-info full';
                        } else {
                            capacityElement.innerHTML = `<strong>Disponible: ${data.capacidad_disponible}</strong>`;
                            capacityElement.className = 'day-capacity-info available';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar capacidad del día:', error);
            });
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const modalNueva = document.getElementById('modalNuevaTarjeta');
            const modalDetalles = document.getElementById('modalDetallesTarjeta');
            const modalProfesor = document.getElementById('modalProfesor');
            
            if (event.target === modalNueva) {
                closeModal();
            }
            if (event.target === modalDetalles) {
                closeDetallesModal();
            }
            if (event.target === modalProfesor) {
                closeProfesorModal();
            }
        }
    </script>
</body>
</html>