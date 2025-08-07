<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];
    
    error_log("save_item.php - Tipo: $type, ID: $id");
    
    $table = '';
    $id_field = 'id_' . $type;
    
    switch($type) {
        case 'jornada':
            $table = 'jornada';
            $dias = $conn->real_escape_string($_POST['dias']);
            if (!empty($id)) {
                $sql = "UPDATE $table SET dias = '$dias' WHERE $id_field = $id";
            } else {
                $sql = "INSERT INTO $table (dias) VALUES ('$dias')";
            }
            break;
            
        case 'itinerario':
            $table = 'itinerario';
            $horario = $conn->real_escape_string($_POST['horario']);
            if (!empty($id)) {
                $sql = "UPDATE $table SET horario = '$horario' WHERE $id_field = $id";
            } else {
                $sql = "INSERT INTO $table (horario) VALUES ('$horario')";
            }
            break;
            
        case 'materia':
            $table = 'materias';
            
            // Función para capitalizar cada palabra
            function capitalizar_palabras($str) {
                return mb_convert_case(trim($str), MB_CASE_TITLE, "UTF-8");
            }
            
            $nombre = capitalizar_palabras($conn->real_escape_string($_POST['nombre']));
            $carrera_id = isset($_POST['carrera_id']) && $_POST['carrera_id'] !== '' ? intval($_POST['carrera_id']) : 'NULL';
            $curso_pre_admision_id = isset($_POST['curso_pre_admision_id']) && $_POST['curso_pre_admision_id'] !== '' ? intval($_POST['curso_pre_admision_id']) : 'NULL';
            $profesor_id = isset($_POST['profesor_id']) && $_POST['profesor_id'] !== '' ? intval($_POST['profesor_id']) : 'NULL';
            
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la materia es obligatorio']);
                exit;
            }
            
            // Validar que no se seleccione tanto carrera como curso pre-admisión
            if ($carrera_id !== 'NULL' && $curso_pre_admision_id !== 'NULL') {
                echo json_encode(['success' => false, 'message' => 'Una materia no puede pertenecer tanto a una carrera como a un curso pre-admisión']);
                exit;
            }
            
            if (!empty($id)) {
                $sql = "UPDATE $table SET nombre='$nombre', carrera_id=$carrera_id, curso_pre_admision_id=$curso_pre_admision_id, profesor_id=$profesor_id WHERE $id_field=$id";
            } else {
                $sql = "INSERT INTO $table (nombre, carrera_id, curso_pre_admision_id, profesor_id) VALUES ('$nombre', $carrera_id, $curso_pre_admision_id, $profesor_id)";
            }
            break;
            
        case 'aula':
            $table = 'aulas';
            $numero = $conn->real_escape_string($_POST['numero']);
            $piso = (int)$_POST['piso'];
            $cantidad = (int)$_POST['cantidad'];
            if (!empty($id)) {
                $sql = "UPDATE $table SET numero = '$numero', piso = $piso, cantidad = $cantidad WHERE $id_field = $id";
            } else {
                $sql = "INSERT INTO $table (numero, piso, cantidad) VALUES ('$numero', $piso, $cantidad)";
            }
            break;
            
        case 'profesor':
            $table = 'profesores';
            $nombre = ucwords(strtolower($conn->real_escape_string($_POST['nombre'])));
            $apellido = ucwords(strtolower($conn->real_escape_string($_POST['apellido'])));
            $correo = $conn->real_escape_string($_POST['correo']);
            $telefono = $conn->real_escape_string($_POST['telefono']);
            // Manejar campos opcionales
            $correo_sql = empty($correo) ? 'NULL' : "'$correo'";
            $telefono_sql = empty($telefono) ? 'NULL' : "'$telefono'";
            
            if (!empty($id)) {
                $sql = "UPDATE $table SET nombre = '$nombre', apellido = '$apellido', correo = $correo_sql, telefono = $telefono_sql WHERE $id_field = $id";
            } else {
                $sql = "INSERT INTO $table (nombre, apellido, correo, telefono) VALUES ('$nombre', '$apellido', $correo_sql, $telefono_sql)";
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Tipo no válido']);
            exit;
    }
    
    error_log("save_item.php - SQL ejecutado: $sql");
    if ($conn->query($sql) === TRUE) {
        error_log("save_item.php - Operación exitosa");
        if ($type === 'profesor' && empty($id)) {
            echo json_encode(['success' => true, 'new_profesor_id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        error_log("save_item.php - Error en la base de datos: " . $conn->error);
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?> 