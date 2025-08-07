<?php
include 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'gestionar_profesor':
            $modo = $_POST['modo'] ?? '';
            $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
            $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
            $correo = $conn->real_escape_string($_POST['correo'] ?? '');
            $telefono = $conn->real_escape_string($_POST['telefono'] ?? '');
            
            if (empty($nombre) || empty($apellido)) {
                echo json_encode(['success' => false, 'message' => 'Nombre y apellido son obligatorios']);
                exit;
            }
            
            if ($modo === 'agregar') {
                $sql = "INSERT INTO profesores (nombre, apellido, correo, telefono) VALUES ('$nombre', '$apellido', '$correo', '$telefono')";
                $mensaje = 'Profesor agregado exitosamente';
            } else {
                $profesor_id = intval($_POST['profesor_id'] ?? 0);
                if ($profesor_id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'ID de profesor inválido']);
                    exit;
                }
                $sql = "UPDATE profesores SET nombre='$nombre', apellido='$apellido', correo='$correo', telefono='$telefono' WHERE id_profesor=$profesor_id";
                $mensaje = 'Profesor actualizado exitosamente';
            }
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => $mensaje]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $conn->error]);
            }
            break;
            
        case 'eliminar_profesor':
            $profesor_id = intval($_POST['profesor_id'] ?? 0);
            
            if ($profesor_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de profesor inválido']);
                exit;
            }
            
            // Verificar si el profesor está asignado a alguna materia
            $check_sql = "SELECT COUNT(*) as count FROM materias WHERE profesor_id = $profesor_id";
            $result = $conn->query($check_sql);
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el profesor porque está asignado a materias']);
                exit;
            }
            
            $sql = "DELETE FROM profesores WHERE id_profesor = $profesor_id";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Profesor eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
            }
            break;
            
        case 'actualizar_relacion_materia_profesor':
            $materia_id = intval($_POST['materia_id'] ?? 0);
            $profesor_id = intval($_POST['profesor_id'] ?? 0);
            
            if ($materia_id <= 0 || $profesor_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'IDs de materia y profesor inválidos']);
                exit;
            }
            
            // Verificar que la materia y el profesor existen
            $check_materia = "SELECT COUNT(*) as count FROM materias WHERE id_materia = $materia_id";
            $check_profesor = "SELECT COUNT(*) as count FROM profesores WHERE id_profesor = $profesor_id";
            
            $result_materia = $conn->query($check_materia);
            $result_profesor = $conn->query($check_profesor);
            
            $row_materia = $result_materia->fetch_assoc();
            $row_profesor = $result_profesor->fetch_assoc();
            
            if ($row_materia['count'] == 0) {
                echo json_encode(['success' => false, 'message' => 'La materia no existe']);
                exit;
            }
            
            if ($row_profesor['count'] == 0) {
                echo json_encode(['success' => false, 'message' => 'El profesor no existe']);
                exit;
            }
            
            // Actualizar la relación materia-profesor
            $sql = "UPDATE materias SET profesor_id = $profesor_id WHERE id_materia = $materia_id";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Profesor asignado a la materia correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al asignar profesor: ' . $conn->error]);
            }
            break;
            
        case 'eliminar_relacion_materia_profesor':
            $materia_id = intval($_POST['materia_id'] ?? 0);
            
            if ($materia_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de materia inválido']);
                exit;
            }
            
            // Verificar que la materia existe
            $check_materia = "SELECT COUNT(*) as count FROM materias WHERE id_materia = $materia_id";
            $result_materia = $conn->query($check_materia);
            $row_materia = $result_materia->fetch_assoc();
            
            if ($row_materia['count'] == 0) {
                echo json_encode(['success' => false, 'message' => 'La materia no existe']);
                exit;
            }
            
            // Eliminar la relación materia-profesor (establecer profesor_id como NULL)
            $sql = "UPDATE materias SET profesor_id = NULL WHERE id_materia = $materia_id";
            
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Relación profesor-materia eliminada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar relación: ' . $conn->error]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conn->close();
?> 