<?php
include 'conexion.php';

header('Content-Type: application/json');

if (isset($_GET['turno_id'])) {
    $turno_id = intval($_GET['turno_id']);
    
    // Obtener itinerarios del turno seleccionado
    $sql = "SELECT * FROM itinerario WHERE turno_id = ? ORDER BY hora_inicio";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $turno_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $itinerarios = [];
    while ($row = $result->fetch_assoc()) {
        $itinerarios[] = [
            'id_itinerario' => $row['id_itinerario'],
            'nombre' => $row['hora_inicio'] . ' - ' . $row['hora_fin'],
            'hora_inicio' => $row['hora_inicio'],
            'hora_fin' => $row['hora_fin']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'itinerarios' => $itinerarios
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'turno_id no proporcionado'
    ]);
}

$conn->close();
?> 