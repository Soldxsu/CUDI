<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Fecha y Zona Horaria</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .correcto { color: green; font-weight: bold; }
        .incorrecto { color: red; font-weight: bold; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 5px; border-left: 4px solid red; }
        .success { background: #e6ffe6; padding: 10px; margin: 10px 0; border-radius: 5px; border-left: 4px solid green; }
    </style>
</head>
<body>
    <h1>🔍 Verificación de Fecha y Zona Horaria</h1>
    
    <?php
    // Incluir configuración
    include_once 'config_timezone.php';
    
    echo "<div class='info'>";
    echo "<h2>📅 Información de Fecha y Hora</h2>";
    
    $debug = debugTimezone();
    
    echo "<p><strong>Zona Horaria Configurada:</strong> <span class='correcto'>" . $debug['zona_horaria'] . "</span></p>";
    echo "<p><strong>Fecha y Hora Actual:</strong> " . $debug['fecha_actual'] . "</p>";
    echo "<p><strong>Día de la Semana:</strong> <span class='correcto'>" . $debug['dia_semana'] . "</span></p>";
    echo "<p><strong>Timestamp:</strong> " . $debug['timestamp'] . "</p>";
    echo "<p><strong>Offset UTC:</strong> " . $debug['utc_offset'] . "</p>";
    
    echo "</div>";
    
    // Verificar si es domingo
    echo "<div class='info'>";
    echo "<h2>🎯 Verificación del Día</h2>";
    
    $numero_dia = date('w');
    $es_domingo = ($numero_dia == 0);
    
    if ($es_domingo) {
        echo "<p class='success'>✅ <strong>CORRECTO:</strong> Hoy es DOMINGO (día 0)</p>";
    } else {
        echo "<p class='error'>❌ <strong>INCORRECTO:</strong> Hoy NO es domingo. Es día " . $numero_dia . "</p>";
    }
    
    echo "<p><strong>Número del día (0-6):</strong> " . $numero_dia . "</p>";
    echo "<p><strong>Día en inglés:</strong> " . date('l') . "</p>";
    echo "<p><strong>Día en español:</strong> " . $debug['dia_semana'] . "</p>";
    
    echo "</div>";
    
    // Verificar configuración del servidor
    echo "<div class='info'>";
    echo "<h2>🖥️ Información del Servidor</h2>";
    
    echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
    echo "<p><strong>Timezone PHP:</strong> " . ini_get('date.timezone') . "</p>";
    echo "<p><strong>Locale:</strong> " . setlocale(LC_TIME, 0) . "</p>";
    
    echo "</div>";
    
    // Verificar diferencia horaria con UTC
    echo "<div class='info'>";
    echo "<h2>⏰ Diferencia Horaria con UTC</h2>";
    
    $utc_time = gmdate('Y-m-d H:i:s');
    $local_time = date('Y-m-d H:i:s');
    
    echo "<p><strong>Hora UTC:</strong> " . $utc_time . "</p>";
    echo "<p><strong>Hora Local (Argentina):</strong> " . $local_time . "</p>";
    
    $utc_timestamp = strtotime($utc_time);
    $local_timestamp = strtotime($local_time);
    $diferencia = $local_timestamp - $utc_timestamp;
    $diferencia_horas = $diferencia / 3600;
    
    echo "<p><strong>Diferencia con UTC:</strong> " . $diferencia_horas . " horas</p>";
    
    // Verificar que la diferencia sea correcta para Argentina (UTC-3)
    if (abs($diferencia_horas - (-3)) < 1) {
        echo "<p class='success'>✅ <strong>CORRECTO:</strong> Diferencia horaria correcta para Argentina (UTC-3)</p>";
    } else {
        echo "<p class='error'>❌ <strong>INCORRECTO:</strong> Diferencia horaria incorrecta. Debería ser UTC-3</p>";
    }
    
    echo "</div>";
    
    // Verificar array de días
    echo "<div class='info'>";
    echo "<h2>📋 Array de Días de la Semana</h2>";
    
    $dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    
    echo "<p><strong>Array completo:</strong></p>";
    echo "<ul>";
    foreach ($dias_semana as $indice => $dia) {
        $marcado = ($indice == $numero_dia) ? " <strong>← HOY</strong>" : "";
        echo "<li>[$indice] => $dia$marcado</li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Día actual del array:</strong> " . $dias_semana[$numero_dia] . "</p>";
    
    echo "</div>";
    
    // Verificar si hay problemas en el calendario
    echo "<div class='info'>";
    echo "<h2>📅 Verificación del Calendario</h2>";
    
    $mes_actual = date('n');
    $anio_actual = date('Y');
    
    echo "<p><strong>Mes actual:</strong> $mes_actual</p>";
    echo "<p><strong>Año actual:</strong> $anio_actual</p>";
    
    // Calcular primer día del mes
    $primer_dia = new DateTime("$anio_actual-$mes_actual-01");
    $dia_semana_primer = $primer_dia->format('w');
    
    echo "<p><strong>Primer día del mes:</strong> " . $primer_dia->format('Y-m-d') . "</p>";
    echo "<p><strong>Día de la semana del primer día:</strong> $dia_semana_primer (" . $dias_semana[$dia_semana_primer] . ")</p>";
    
    // Calcular inicio del calendario
    $inicio_calendario = clone $primer_dia;
    $inicio_calendario->modify('-' . $dia_semana_primer . ' days');
    
    echo "<p><strong>Inicio del calendario:</strong> " . $inicio_calendario->format('Y-m-d') . " (" . $dias_semana[$inicio_calendario->format('w')] . ")</p>";
    
    echo "</div>";
    
    // Mostrar mensaje final
    if ($es_domingo && abs($diferencia_horas - (-3)) < 1) {
        echo "<div class='success'>";
        echo "<h2>🎉 ¡CONFIGURACIÓN CORRECTA!</h2>";
        echo "<p>La zona horaria está configurada correctamente y hoy es domingo.</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h2>⚠️ PROBLEMAS DETECTADOS</h2>";
        if (!$es_domingo) {
            echo "<p>• El día de la semana no es correcto</p>";
        }
        if (abs($diferencia_horas - (-3)) >= 1) {
            echo "<p>• La diferencia horaria no es correcta para Argentina</p>";
        }
        echo "<p>Revisa la configuración del servidor y PHP.</p>";
        echo "</div>";
    }
    ?>
    
    <div class='info'>
        <h2>🔧 Soluciones Recomendadas</h2>
        <ol>
            <li><strong>Verificar php.ini:</strong> Asegúrate de que <code>date.timezone = "America/Argentina/Buenos_Aires"</code> esté en tu archivo php.ini</li>
            <li><strong>Reiniciar servidor:</strong> Después de cambiar la configuración, reinicia Apache/XAMPP</li>
            <li><strong>Verificar zona horaria del sistema:</strong> Asegúrate de que Windows esté configurado en zona horaria de Argentina</li>
            <li><strong>Limpiar cache:</strong> Limpia el cache del navegador y del servidor</li>
        </ol>
    </div>
    
    <p><a href="disposicionaulica.php">← Volver al Calendario</a></p>
</body>
</html> 