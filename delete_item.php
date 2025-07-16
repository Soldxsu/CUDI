<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];
    
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
    
    // Verificar si el elemento está siendo usado en la tabla dias
    $check_sql = "SELECT COUNT(*) as count FROM dias WHERE ${type}_id = $id";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar porque está siendo usado en una disposición áulica']);
        exit;
    }
    
    $sql = "DELETE FROM $table WHERE $id_field = $id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?> 