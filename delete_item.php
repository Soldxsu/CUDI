<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];
    
    error_log("delete_item.php - Tipo: $type, ID: $id");
    
    $table = '';
    $id_field = 'id_' . $type;
    
    switch($type) {
        case 'jornada':
            $table = 'jornada';
            break;
        case 'itinerario':
            $table = 'itinerario';
            break;
        case 'materia':
            $table = 'materias';
            break;
        case 'aula':
            $table = 'aulas';
            break;
        case 'profesor':
            $table = 'profesores';
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tipo no válido']);
            exit;
    }
    
    // Verificar si el elemento está siendo usado en la tabla tarjetas_disposicion
    $check_sql = "";
    switch($type) {
        case 'materia':
            $check_sql = "SELECT COUNT(*) as count FROM tarjetas_disposicion WHERE materia_id = $id";
            break;
        case 'aula':
            $check_sql = "SELECT COUNT(*) as count FROM tarjetas_disposicion WHERE aula_id = $id";
            break;
        case 'profesor':
            // Verificar si el profesor está asignado a alguna materia
            $check_sql = "SELECT COUNT(*) as count FROM materias WHERE profesor_id = $id";
            break;
        case 'itinerario':
            $check_sql = "SELECT COUNT(*) as count FROM tarjetas_disposicion WHERE itinerario_id = $id";
            break;
        case 'jornada':
            // Las jornadas no se usan en tarjetas_disposicion, pero verificamos si hay materias relacionadas
            $check_sql = "SELECT COUNT(*) as count FROM materias WHERE carrera_id IN (SELECT id_carrera FROM carreras WHERE universidad_id IN (SELECT id_universidad FROM universidades WHERE curso_pre_admision_id = $id))";
            break;
        default:
            $check_sql = "SELECT 0 as count";
    }
    
    $check_result = $conn->query($check_sql);
    if ($check_result) {
        $check_row = $check_result->fetch_assoc();
        
        if ($check_row['count'] > 0) {
            $mensaje = 'No se puede eliminar porque está siendo usado en el sistema';
            if ($type === 'materia') {
                $mensaje = 'No se puede eliminar la materia porque está asignada a disposiciones áulicas';
            } elseif ($type === 'profesor') {
                $mensaje = 'No se puede eliminar el profesor porque está asignado a materias';
            }
            echo json_encode(['success' => false, 'message' => $mensaje]);
            exit;
        }
    }
    
    $sql = "DELETE FROM $table WHERE $id_field = $id";
    error_log("delete_item.php - SQL ejecutado: $sql");
    
    if ($conn->query($sql) === TRUE) {
        error_log("delete_item.php - Eliminación exitosa");
        echo json_encode(['success' => true]);
    } else {
        error_log("delete_item.php - Error en la base de datos: " . $conn->error);
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?> 