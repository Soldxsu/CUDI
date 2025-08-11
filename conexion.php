<?php
// Incluir configuración de zona horaria
include_once 'config_timezone.php';

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "disposicion_aulica";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para debug de fecha (puedes comentar esta línea cuando esté funcionando)
function debugFecha() {
    $debug = debugTimezone();
    echo "<!-- Debug Fecha: " . $debug['fecha_actual'] . " - Día: " . $debug['dia_semana'] . " - Zona: " . $debug['zona_horaria'] . " -->";
}
?>