<?php
/**
 * Configuración de Zona Horaria para Argentina
 * Este archivo debe ser incluido al inicio de todas las páginas que manejen fechas
 */

// Configurar zona horaria para Argentina
if (!date_default_timezone_get() || date_default_timezone_get() !== 'America/Argentina/Buenos_Aires') {
    date_default_timezone_set('America/Argentina/Buenos_Aires');
}

// Configurar locale para español
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252', 'es_ES', 'esp');

// Función para obtener el día de la semana en español
function getDiaSemana($fecha = null) {
    if ($fecha === null) {
        $fecha = date('Y-m-d');
    }
    
    $dias_semana = [
        0 => 'Domingo',
        1 => 'Lunes', 
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];
    
    $numero_dia = date('w', strtotime($fecha));
    return $dias_semana[$numero_dia];
}

// Función para obtener fecha formateada en español
function getFechaEspanol($fecha = null) {
    if ($fecha === null) {
        $fecha = date('Y-m-d');
    }
    
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    $timestamp = strtotime($fecha);
    $dia = date('j', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);
    $dia_semana = getDiaSemana($fecha);
    
    return "$dia_semana, $dia de $mes de $anio";
}

// Función para debug de zona horaria
function debugTimezone() {
    $zona_actual = date_default_timezone_get();
    $fecha_actual = date('Y-m-d H:i:s');
    $dia_semana = getDiaSemana();
    
    return [
        'zona_horaria' => $zona_actual,
        'fecha_actual' => $fecha_actual,
        'dia_semana' => $dia_semana,
        'timestamp' => time(),
        'utc_offset' => date('P')
    ];
}

// Verificar que la configuración esté funcionando
if (date_default_timezone_get() !== 'America/Argentina/Buenos_Aires') {
    error_log('ERROR: La zona horaria no se configuró correctamente. Actual: ' . date_default_timezone_get());
}
?> 