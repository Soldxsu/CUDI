<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0;url=disposicionaulica.php">
    <title>Document</title>
</head>
</html>
<?php
include("conexion.php");

$jornada = isset($_REQUEST["jornada_id"]) ? intval($_REQUEST["jornada_id"]) : 0;
$itinerario = isset($_REQUEST["itinerario_id"]) ? intval($_REQUEST["itinerario_id"]) : 0;
$materia = isset($_REQUEST["materia_id"]) ? intval($_REQUEST["materia_id"]) : 0;
$aula = isset($_REQUEST["aula_id"]) ? intval($_REQUEST["aula_id"]) : 0;

// Validar que todos los campos requeridos estén presentes
if ($jornada && $itinerario && $materia && $aula) {
    // Obtener el profesor_id de la materia seleccionada
    $profesor_id = '';
    $sql_prof = "SELECT profesor_id FROM materias WHERE id_materia = $materia";
    $result_prof = $conn->query($sql_prof);
    if ($result_prof && $result_prof->num_rows > 0) {
        $row_prof = $result_prof->fetch_assoc();
        $profesor_id = $row_prof['profesor_id'];
    }
    if (!empty($profesor_id)) {
        $sql= "INSERT INTO dias (jornada_id, itinerario_id, materia_id, aula_id, profesor_id) VALUES ($jornada, $itinerario, $materia, $aula, $profesor_id)";
        if ($conn->query($sql) === TRUE) {
            header("Location: disposicionaulica.php");
            exit;
        } else {
            echo "<p style='color:red;'>Error al guardar la disposición: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>No se puede guardar la disposición: la materia seleccionada no tiene profesor asignado.</p>";
    }
} else {
    echo "<p style='color:red;'>Faltan datos obligatorios para guardar la disposición.</p>";
}
?>