<?php
include 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$fecha = $conn->real_escape_string($_POST['fecha']);
$turno_id = intval($_POST['turno_id']);

if (!$fecha || !$turno_id) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

try {
    // Obtener la capacidad total de todas las aulas
    $sql_capacidad_total = "
        SELECT SUM(capacidad) as capacidad_total 
        FROM aulas 
        WHERE estado = 'activa'
    ";
    $result_capacidad = $conn->query($sql_capacidad_total);
    $capacidad_total = $result_capacidad->fetch_assoc()['capacidad_total'] ?? 0;
    
    // Obtener el número de estudiantes ya asignados en ese turno y fecha
    $sql_estudiantes_asignados = "
        SELECT SUM(t.cantidad_estudiantes) as estudiantes_asignados
        FROM tarjetas t
        INNER JOIN itinerarios i ON t.id_itinerario = i.id_itinerario
        WHERE t.fecha = ? AND i.id_turno = ?
    ";
    
    $stmt = $conn->prepare($sql_estudiantes_asignados);
    $stmt->bind_param('si', $fecha, $turno_id);
    $stmt->execute();
    $result_estudiantes = $stmt->get_result();
    $estudiantes_asignados = $result_estudiantes->fetch_assoc()['estudiantes_asignados'] ?? 0;
    
    // Calcular capacidad disponible
    $capacidad_disponible = max(0, $capacidad_total - $estudiantes_asignados);
    
    echo json_encode([
        'success' => true,
        'capacidad_total' => $capacidad_total,
        'estudiantes_asignados' => $estudiantes_asignados,
        'capacidad_disponible' => $capacidad_disponible
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al calcular capacidad: ' . $e->getMessage()]);
}

$conn->close();
?> 