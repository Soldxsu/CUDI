<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    error_log("get_item_data.php - Tipo: $type, ID: $id");
    
    $table = '';
    $fields = '';
    
    switch($type) {
        case 'jornada':
            $table = 'jornada';
            $fields = 'dias';
            break;
        case 'itinerario':
            $table = 'itinerario';
            $fields = 'horario';
            break;
        case 'materia':
            $table = 'materias';
            $fields = 'm.nombre, m.carrera_id, m.curso_pre_admision_id, m.profesor_id, 
                      c.nombre as carrera_nombre, 
                      cp.nombre_curso as curso_pre_admision_nombre,
                      p.nombre as profesor_nombre, p.apellido as profesor_apellido';
            break;
        case 'aula':
            $table = 'aulas';
            $fields = 'numero, piso, cantidad';
            break;
        case 'profesor':
            $table = 'profesores';
            $fields = 'nombre, apellido, correo, telefono';
            break;
        case 'carrera':
            $table = 'carreras';
            $fields = 'nombre';
            break;
        case 'curso_pre_admision':
            $table = 'cursos_pre_admisiones';
            $fields = 'nombre_curso';
            break;
        case 'diplomatura':
            $table = 'carreras';
            $fields = 'nombre';
            break;
        case 'all_profesores':
            $result = $conn->query("SELECT id_profesor, nombre, apellido FROM profesores");
            if (!$result) {
                echo json_encode(['error' => 'Error en la consulta SQL: ' . $conn->error]);
                $conn->close();
                exit;
            }
            $profesores = array();
            while ($row = $result->fetch_assoc()) {
                $profesores[] = $row;
            }
            if (empty($profesores)) {
                echo json_encode(['error' => 'No hay profesores en la base de datos.']);
            } else {
                echo json_encode($profesores);
            }
            $conn->close();
            exit;
        default:
            echo json_encode(['error' => 'Tipo no válido']);
            exit;
    }
    
    $id_field = 'id_' . $type;
    if ($type === 'curso_pre_admision') {
        $id_field = 'id_curso_pre_admision';
    }
    if ($type === 'carrera' || $type === 'diplomatura') {
        $id_field = 'id_carrera';
    }
    if ($type === 'materia') {
        $sql = "SELECT $fields FROM $table m 
                LEFT JOIN carreras c ON m.carrera_id = c.id_carrera 
                LEFT JOIN cursos_pre_admisiones cp ON m.curso_pre_admision_id = cp.id_curso_pre_admision 
                LEFT JOIN profesores p ON m.profesor_id = p.id_profesor 
                WHERE m.$id_field = $id";
        error_log("get_item_data.php - SQL para materia: $sql");
    } else {
        $sql = "SELECT $fields FROM $table WHERE $id_field = $id";
    }
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        error_log("get_item_data.php - Datos encontrados: " . json_encode($data));
        echo json_encode($data);
    } else {
        error_log("get_item_data.php - No se encontraron datos");
        echo json_encode(['error' => 'Elemento no encontrado']);
    }
}

$conn->close();
?> 