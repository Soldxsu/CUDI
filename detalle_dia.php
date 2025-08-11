<?php
include 'conexion.php';
debugFecha(); // Debug de fecha para verificar zona horaria

// Obtener la fecha del parámetro GET
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    header('Location: disposicionaulica.php');
    exit;
}

// Procesar acciones de crear tarjetas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create_tarjeta') {
        $fecha_tarjeta = $conn->real_escape_string($_POST['fecha']);
        $turno_id = intval($_POST['turno_id']);
        $itinerario_id = intval($_POST['itinerario_id']);
        $materia_id = intval($_POST['materia_id']);
        $aula_id = intval($_POST['aula_id']);
        $profesor_id = intval($_POST['profesor_id']);
        
        $sql = "INSERT INTO tarjetas_disposicion (fecha, turno_id, itinerario_id, materia_id, aula_id, profesor_id, estado) 
                VALUES ('$fecha_tarjeta', $turno_id, $itinerario_id, $materia_id, $aula_id, $profesor_id, 'activa')";
        
        if ($conn->query($sql)) {
            header("Location: detalle_dia.php?fecha=$fecha&success=1");
            exit;
        }
    }
}

// Obtener datos del día

// 1. Obtener todas las aulas
$sql_aulas = "SELECT * FROM aulas ORDER BY numero";
$aulas_result = $conn->query($sql_aulas);

// 2. Obtener todas las tarjetas de disposición del día, con info de turno y materia
$sql_tarjetas = "SELECT td.*, t.nombre AS turno_nombre, t.hora_inicio, t.hora_fin,
                        m.nombre AS materia_nombre, 
                        CONCAT(p.nombre, ' ', p.apellido) AS profesor_nombre,
                        a.numero AS aula_nombre, a.cantidad AS capacidad,
                        CONCAT(i.hora_inicio, ' - ', i.hora_fin) AS itinerario_nombre,
                        c.nombre AS carrera_nombre,
                        cpu.nombre_curso AS curso_nombre
                 FROM tarjetas_disposicion td
                 LEFT JOIN turnos t ON td.turno_id = t.id_turno
                 LEFT JOIN materias m ON td.materia_id = m.id_materia
                 LEFT JOIN profesores p ON td.profesor_id = p.id_profesor
                 LEFT JOIN aulas a ON td.aula_id = a.id_aula
                 LEFT JOIN itinerario i ON td.itinerario_id = i.id_itinerario
                 LEFT JOIN carreras c ON m.carrera_id = c.id_carrera
                 LEFT JOIN cursos_pre_admisiones cpu ON m.curso_pre_admision_id = cpu.id_curso_pre_admision
                 WHERE td.fecha = '$fecha'
                 ORDER BY a.numero, t.hora_inicio, i.hora_inicio";
$tarjetas_result = $conn->query($sql_tarjetas);

// 3. Agrupar tarjetas por aula y por turno dentro de cada aula
$tarjetas_por_aula = [];
while ($tarjeta = $tarjetas_result->fetch_assoc()) {
    $aula_id = $tarjeta['aula_id'];
    $turno_id = $tarjeta['turno_id'];
    if (!isset($tarjetas_por_aula[$aula_id])) {
        $tarjetas_por_aula[$aula_id] = [
            'aula_nombre' => $tarjeta['aula_nombre'],
            'capacidad' => $tarjeta['capacidad'],
            'turnos' => []
        ];
    }
    if (!isset($tarjetas_por_aula[$aula_id]['turnos'][$turno_id])) {
        $tarjetas_por_aula[$aula_id]['turnos'][$turno_id] = [
            'nombre' => $tarjeta['turno_nombre'],
            'hora_inicio' => $tarjeta['hora_inicio'],
            'hora_fin' => $tarjeta['hora_fin'],
            'tarjetas' => []
        ];
    }
    $tarjetas_por_aula[$aula_id]['turnos'][$turno_id]['tarjetas'][] = $tarjeta;
}

// 4. Obtener todos los turnos disponibles
$sql_turnos = "SELECT * FROM turnos ORDER BY hora_inicio";
$turnos = $conn->query($sql_turnos);
$turnos_array = [];
while ($t = $turnos->fetch_assoc()) {
    $turnos_array[$t['id_turno']] = $t;
}

// Obtener datos para los formularios del modal
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

// Nombres de días y formato de fecha
$dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$fecha_obj = new DateTime($fecha);
$nombre_dia = $dias_semana[$fecha_obj->format('w')];
$fecha_formateada = $fecha_obj->format('d/m/Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Día - <?php echo $fecha_formateada; ?></title>
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

        .header-subtitle {
            margin-top: 10px;
            font-size: 1.2em;
            opacity: 0.9;
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .day-info {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
        }

        .day-date {
            font-size: 2em;
            font-weight: 600;
            color: #3B6CDC;
            margin-bottom: 10px;
        }

        .day-name {
            font-size: 1.5em;
            color: #666;
            margin-bottom: 20px;
        }

        .add-tarjeta-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .add-tarjeta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1em;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .add-tarjeta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        /* Estilos para el botón de agregar más tarjetas */
        .add-more-tarjetas {
            border-top: 1px solid #e3eefd;
            padding-top: 20px;
        }

        .add-more-tarjetas .btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .add-more-tarjetas .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        .add-more-tarjetas .btn span {
            font-size: 16px;
            margin-right: 5px;
        }

        .turno-info {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #e3eefd;
            display: inline-block;
        }

        .turno-lleno {
            border-top: 1px solid #e3eefd;
            padding-top: 20px;
        }

        .turno-section {
            background: white;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .turno-header {
            background: linear-gradient(135deg, #3B6CDC 0%, #6BD4E2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .turno-info h3 {
            margin: 0;
            font-size: 1.5em;
            font-weight: 600;
        }

        .turno-horario {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .turno-stats {
            text-align: right;
        }

        .turno-count {
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .turno-label {
            font-size: 0.9em;
            opacity: 0.8;
        }

        .turno-content {
            padding: 20px;
        }

        .turno-vacio {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
        }

        .tarjetas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .tarjeta-card {
            background: white;
            border: 2px solid #e3eefd;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .tarjeta-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #3B6CDC;
        }

        .tarjeta-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }

        .tarjeta-card.duplicada::before {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        }

        .tarjeta-card.programada::before {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        }

        .tarjeta-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .tarjeta-horario {
            background: #3B6CDC;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .tarjeta-estado {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .tarjeta-estado.activa {
            background: #d4edda;
            color: #155724;
        }

        .tarjeta-estado.duplicada {
            background: #fff3cd;
            color: #856404;
        }

        .tarjeta-estado.programada {
            background: #d1ecf1;
            color: #0c5460;
        }

        .tarjeta-materia {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .tarjeta-detalles {
            margin-bottom: 15px;
        }

        .tarjeta-detalle {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .tarjeta-detalle i {
            width: 20px;
            margin-right: 10px;
            color: #3B6CDC;
        }

        .tarjeta-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #3B6CDC;
            color: white;
        }

        .btn-primary:hover {
            background: #2a5bb8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
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

        .add-tarjeta-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
        }

        .add-tarjeta-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .add-tarjeta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .day-date {
                font-size: 1.5em;
            }
            
            .turno-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .tarjetas-grid {
                grid-template-columns: 1fr;
            }
            
            .tarjeta-actions {
                flex-direction: column;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #3B6CDC 0%, #6BD4E2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5em;
            font-weight: 300;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .close:hover {
            opacity: 0.7;
        }

        .form-group {
            margin: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3B6CDC;
        }

        .form-group input:disabled,
        .form-group select:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        /* Fix para select dropdowns que aparecen arriba */
        .form-group select {
            position: relative;
            z-index: 1;
        }

        .form-group select option {
            position: relative;
            z-index: 1001;
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

        .modal-actions {
            padding: 20px;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            border-top: 1px solid #e1e5e9;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #3B6CDC;
            color: white;
        }

        .btn-primary:hover {
            background: #2d5bb8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalle del Día</h1>
        <div class="header-subtitle"><?php echo $nombre_dia . ', ' . $fecha_formateada; ?></div>
        <div class="header-links">
            <a href="disposicionaulica.php" class="header-link">← Volver al Calendario</a>
            <a href="materias.php" class="header-link">Materias</a>
            <a href="profesores.php" class="header-link">Profesores</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ✅ Tarjeta creada exitosamente
            </div>
        <?php endif; ?>
        <div class="day-info">
            <div class="day-date"><?php echo $fecha_formateada; ?></div>
            <div class="day-name"><?php echo $nombre_dia; ?></div>
        </div>

        <div class="aulas-grid">
            <table class="tabla-aulas" style="width:100%;background:white;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.08);overflow:hidden;">
                <thead>
                    <tr>
                        <th>Aula</th>
                        <th>Capacidad</th>
                        <th>Mañana</th>
                        <th>Tarde</th>
                        <th>Noche</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Función para determinar el estado del turno (solo una vez, fuera de cualquier bucle)
                if (!function_exists('estadoTurno')) {
                    function estadoTurno($turno_id, $turnos_aula, $capacidad) {
                        if (!isset($turnos_aula[$turno_id]) || empty($turnos_aula[$turno_id]['tarjetas'])) {
                            return ['Sin uso', '#888']; // gris
                        }
                        $ocupadas = count($turnos_aula[$turno_id]['tarjetas']);
                        if ($ocupadas >= $capacidad) {
                            return ['Lleno', '#dc3545']; // rojo
                        }
                        return ['Ocupado', '#28a745']; // verde
                    }
                }
                // Buscar los turnos de mañana, tarde, noche
                $turno_manana = $turno_tarde = $turno_noche = null;
                foreach ($turnos_array as $turno_id => $turno) {
                    $nombre = mb_strtolower($turno['nombre']);
                    if (strpos($nombre, 'mañana') !== false) $turno_manana = $turno_id;
                    if (strpos($nombre, 'tarde') !== false) $turno_tarde = $turno_id;
                    if (strpos($nombre, 'noche') !== false) $turno_noche = $turno_id;
                }
                $aulas_result->data_seek(0);
                while ($aula = $aulas_result->fetch_assoc()):
                    $aula_id = $aula['id_aula'];
                    $aula_nombre = $aula['numero'];
                    $capacidad = $aula['cantidad'];
                    $turnos_aula = isset($tarjetas_por_aula[$aula_id]['turnos']) ? $tarjetas_por_aula[$aula_id]['turnos'] : [];
                    list($txt_manana, $color_manana) = $turno_manana ? estadoTurno($turno_manana, $turnos_aula, $capacidad) : ['-', '#888'];
                    list($txt_tarde, $color_tarde) = $turno_tarde ? estadoTurno($turno_tarde, $turnos_aula, $capacidad) : ['-', '#888'];
                    list($txt_noche, $color_noche) = $turno_noche ? estadoTurno($turno_noche, $turnos_aula, $capacidad) : ['-', '#888'];
                ?>
                    <tr class="fila-aula" style="cursor:pointer;" onclick="mostrarModalAula('<?php echo $aula_id; ?>')">
                        <td><strong><?php echo $aula_nombre; ?></strong></td>
                        <td><?php echo $capacidad; ?></td>
                        <td><span style="font-weight:600;color:<?php echo $color_manana; ?>;"><?php echo $txt_manana; ?></span></td>
                        <td><span style="font-weight:600;color:<?php echo $color_tarde; ?>;"><?php echo $txt_tarde; ?></span></td>
                        <td><span style="font-weight:600;color:<?php echo $color_noche; ?>;"><?php echo $txt_noche; ?></span></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de info de aula -->
    <div id="modalInfoAula" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalAulaTitulo"></h2>
                <span class="close" onclick="cerrarModalAula()">&times;</span>
            </div>
            <div id="modalAulaBody" style="padding:24px;"></div>
        </div>
    </div>

    <script>
        // MODAL DE INFO DE AULA
        const aulasData = {};
        <?php
        $aulas_result->data_seek(0);
        while ($aula = $aulas_result->fetch_assoc()):
            $aula_id = $aula['id_aula'];
            $aula_nombre = $aula['numero'];
            $capacidad = $aula['cantidad'];
            $turnos_aula = isset($tarjetas_por_aula[$aula_id]['turnos']) ? $tarjetas_por_aula[$aula_id]['turnos'] : [];
        ?>
        aulasData[<?php echo json_encode($aula_id); ?>] = {
            nombre: <?php echo json_encode($aula_nombre); ?>,
            capacidad: <?php echo json_encode($capacidad); ?>,
            turnos: {
                <?php foreach ($turnos_array as $turno_id => $turno):
                    $datos_turno = isset($turnos_aula[$turno_id]) ? $turnos_aula[$turno_id] : null;
                    $tarjetas_del_turno = $datos_turno ? $datos_turno['tarjetas'] : [];
                ?>
                <?php echo json_encode($turno_id); ?>: {
                    nombre: <?php echo json_encode($turno['nombre']); ?>,
                    hora_inicio: <?php echo json_encode($turno['hora_inicio']); ?>,
                    hora_fin: <?php echo json_encode($turno['hora_fin']); ?>,
                    tarjetas: <?php echo json_encode($tarjetas_del_turno); ?>
                },
                <?php endforeach; ?>
            }
        };
        <?php endwhile; ?>

        function mostrarModalAula(aulaId) {
            const modal = document.getElementById('modalInfoAula');
            const body = document.getElementById('modalAulaBody');
            const titulo = document.getElementById('modalAulaTitulo');
            const aula = aulasData[aulaId];
            if (!aula) return;
            titulo.textContent = `Aula ${aula.nombre}`;
            let html = `<div style='font-size:1.1em;margin-bottom:10px;'><strong>Capacidad:</strong> ${aula.capacidad}</div>`;
            html += `<div style='margin-bottom:10px;'><strong>Turnos:</strong></div>`;
            html += `<div style='display:flex;flex-direction:column;gap:12px;'>`;
            for (const turnoId in aula.turnos) {
                const turno = aula.turnos[turnoId];
                html += `<div style='background:#f8faff;border-radius:8px;padding:10px 14px;'>`;
                html += `<div style='color:#1976D2;font-weight:600;'>${turno.nombre} (${turno.hora_inicio} - ${turno.hora_fin})</div>`;
                if (!turno.tarjetas || turno.tarjetas.length === 0) {
                    html += `<div style='color:#888;font-size:0.98em;margin:6px 0 0 0;'>No hay tarjetas programadas</div>`;
                } else {
                    html += `<div style='margin-top:6px;'>`;
                    turno.tarjetas.forEach(tarjeta => {
                        html += `<div style='background:#fff;border-radius:6px;padding:8px 10px;margin-bottom:6px;box-shadow:0 1px 4px rgba(59,108,220,0.07);'>`;
                        html += `<div style='font-weight:600;color:#3B6CDC;'>${tarjeta.materia_nombre}</div>`;
                        html += `<div style='font-size:0.97em;color:#333;'>Profesor: ${tarjeta.profesor_nombre || '-'}</div>`;
                        html += `<div style='font-size:0.97em;color:#333;'>Carrera/Curso: ${tarjeta.carrera_nombre || tarjeta.curso_nombre || '-'}</div>`;
                        html += `<div style='font-size:0.97em;color:#333;'>Horario: ${tarjeta.itinerario_nombre || '-'}</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                }
                html += `</div>`;
            }
            html += `</div>`;
            body.innerHTML = html;
            modal.style.display = 'block';
        }
        function cerrarModalAula() {
            document.getElementById('modalInfoAula').style.display = 'none';
        }
    </script>
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
                <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                <input type="hidden" name="turno_id" id="turno_id_modal">
                
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha_display" value="<?php echo $fecha; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Turno:</label>
                    <select name="turno_display" id="turno_display" disabled>
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

    <a href="disposicionaulica.php" class="back-button">← Volver al Calendario</a>

    <script>
        function openModal(turnoId) {
            // Establecer el turno seleccionado
            document.getElementById('turno_id_modal').value = turnoId;
            
            // Actualizar el display del turno
            const turnoSelect = document.getElementById('turno_display');
            const turnoOption = turnoSelect.querySelector(`option[value="${turnoId}"]`);
            if (turnoOption) {
                turnoOption.selected = true;
            }
            
            // Actualizar el título del modal
            document.querySelector('#modalNuevaTarjeta .modal-header h2').textContent = 
                `Nueva Tarjeta - ${turnoOption ? turnoOption.textContent : 'Turno'}`;
            
            // Mostrar el modal
            document.getElementById('modalNuevaTarjeta').style.display = 'block';
            
            // Habilitar el select de itinerarios y filtrar por turno
            filtrarItinerariosPorTurno(turnoId);
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
            
            // Restaurar título original
            document.querySelector('#modalNuevaTarjeta .modal-header h2').textContent = 'Nueva Tarjeta de Disposición';
        }

        // Función para filtrar itinerarios por turno
        function filtrarItinerariosPorTurno(turnoId) {
            const itinerarioSelect = document.getElementById('itinerario_select');
            
            // Habilitar el select
            itinerarioSelect.disabled = false;
            
            // Limpiar opciones actuales
            itinerarioSelect.innerHTML = '<option value="">Seleccionar horario</option>';
            
            // Obtener itinerarios del turno seleccionado
            fetch(`get_itinerarios_por_turno.php?turno_id=${turnoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.itinerarios.forEach(itinerario => {
                            const option = document.createElement('option');
                            option.value = itinerario.id_itinerario;
                            option.textContent = itinerario.nombre;
                            itinerarioSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al obtener itinerarios:', error);
                });
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
            const itinerarioSelect = document.getElementById('itinerario_select');
            const buscarMateria = document.getElementById('buscar_materia');
            const buscarProfesor = document.getElementById('buscar_profesor');
            
            // Búsqueda de materias
            if (buscarMateria) {
                buscarMateria.addEventListener('input', function() {
                    filtrarMaterias(this.value);
                });
            }
            
            // Búsqueda de profesores
            if (buscarProfesor) {
                buscarProfesor.addEventListener('input', function() {
                    filtrarProfesores(this.value);
                });
            }
            
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
            
            // Event listener para el formulario de profesores
            const formProfesor = document.getElementById('formProfesor');
            if (formProfesor) {
                formProfesor.addEventListener('submit', function(e) {
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
            }
        });

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const modalNueva = document.getElementById('modalNuevaTarjeta');
            const modalProfesor = document.getElementById('modalProfesor');
            
            if (event.target === modalNueva) {
                closeModal();
            }
            if (event.target === modalProfesor) {
                closeProfesorModal();
            }
        }
    </script>
</body>
</html>