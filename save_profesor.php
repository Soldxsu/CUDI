<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = ucwords(strtolower($conn->real_escape_string($_POST['nombre'])));
    $apellido = ucwords(strtolower($conn->real_escape_string($_POST['apellido'])));
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    
    // Validar que los campos requeridos estén presentes
    if (empty($nombre) || empty($apellido)) {
        echo json_encode(['success' => false, 'message' => 'Nombre y apellido son obligatorios']);
        exit;
    }
    
    // Si el correo está vacío, establecer como NULL
    $correo_sql = empty($correo) ? 'NULL' : "'$correo'";
    $telefono_sql = empty($telefono) ? 'NULL' : "'$telefono'";
    
    $sql = "INSERT INTO profesores (nombre, apellido, correo, telefono) VALUES ('$nombre', '$apellido', $correo_sql, $telefono_sql)";
    
    if ($conn->query($sql) === TRUE) {
        $profesor_id = $conn->insert_id;
        echo json_encode([
            'success' => true, 
            'profesor_id' => $profesor_id,
            'nombre' => $nombre,
            'apellido' => $apellido
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?> 