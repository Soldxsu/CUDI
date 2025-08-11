<?php
include 'conexion.php';

echo "<h1>Prueba de Zona Horaria</h1>";
echo "<p><strong>Zona horaria configurada:</strong> " . date_default_timezone_get() . "</p>";
echo "<p><strong>Fecha y hora actual:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Día de la semana:</strong> " . date('l') . " (en inglés)</p>";
echo "<p><strong>Día de la semana (número):</strong> " . date('w') . " (0=Domingo, 1=Lunes, etc.)</p>";

// Array de días en español
$dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$dia_actual = $dias_semana[date('w')];

echo "<p><strong>Día de la semana en español:</strong> " . $dia_actual . "</p>";

// Verificar si es domingo
if (date('w') == 0) {
    echo "<p style='color: green;'><strong>✓ CORRECTO: Hoy es DOMINGO</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ INCORRECTO: Hoy NO es domingo</strong></p>";
}

// Mostrar información del servidor
echo "<h2>Información del Servidor</h2>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Timezone PHP:</strong> " . ini_get('date.timezone') . "</p>";

// Verificar si hay diferencia horaria
$utc_time = gmdate('Y-m-d H:i:s');
$local_time = date('Y-m-d H:i:s');
echo "<p><strong>Hora UTC:</strong> " . $utc_time . "</p>";
echo "<p><strong>Hora Local (Argentina):</strong> " . $local_time . "</p>";

// Calcular diferencia
$utc_timestamp = strtotime($utc_time);
$local_timestamp = strtotime($local_time);
$diferencia = $local_timestamp - $utc_timestamp;
$diferencia_horas = $diferencia / 3600;

echo "<p><strong>Diferencia con UTC:</strong> " . $diferencia_horas . " horas</p>";

// Verificar que la diferencia sea correcta para Argentina (UTC-3)
if (abs($diferencia_horas - (-3)) < 1) {
    echo "<p style='color: green;'><strong>✓ Diferencia horaria correcta para Argentina (UTC-3)</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Diferencia horaria incorrecta</strong></p>";
}
?> 