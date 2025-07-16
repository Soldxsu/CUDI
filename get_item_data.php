<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];
    
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
            $fields = 'nombre, carrera_id, curso_pre_admision_id, profesor_id, carrera_id as diplomatura_id'; // diplomatura_id es alias para compatibilidad
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
            $profesores = array();
            while ($row = $result->fetch_assoc()) {
                $profesores[] = $row;
            }
            echo json_encode($profesores);
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
    $sql = "SELECT $fields FROM $table WHERE $id_field = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Elemento no encontrado']);
    }
}

$conn->close();
?> 