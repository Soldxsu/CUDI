<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $materia_id = (int)$_POST['materia_id'];
    $profesor_id = (int)$_POST['profesor_id'];
    
    if ($materia_id && $profesor_id) {
        $sql = "UPDATE materias SET profesor_id = $profesor_id WHERE id_materia = $materia_id";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }
}

$conn->close();
?> 