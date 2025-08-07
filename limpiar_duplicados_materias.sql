-- Script para eliminar materias duplicadas de la base de datos
USE `disposicion_aulica`;

-- Crear tabla temporal para identificar duplicados
CREATE TEMPORARY TABLE materias_duplicadas AS
SELECT nombre, carrera_id, curso_pre_admision_id, MIN(id_materia) as id_materia_a_mantener
FROM materias 
GROUP BY nombre, carrera_id, curso_pre_admision_id
HAVING COUNT(*) > 1;

-- Mostrar las materias duplicadas que se van a eliminar
SELECT 'Materias duplicadas que se eliminarán:' as mensaje;
SELECT m.id_materia, m.nombre, m.carrera_id, m.curso_pre_admision_id, m.profesor_id
FROM materias m
INNER JOIN materias_duplicadas md ON m.nombre = md.nombre 
    AND (m.carrera_id = md.carrera_id OR (m.carrera_id IS NULL AND md.carrera_id IS NULL))
    AND (m.curso_pre_admision_id = md.curso_pre_admision_id OR (m.curso_pre_admision_id IS NULL AND md.curso_pre_admision_id IS NULL))
WHERE m.id_materia != md.id_materia_a_mantener
ORDER BY m.nombre, m.id_materia;

-- Eliminar las materias duplicadas (mantener solo la primera de cada grupo)
DELETE m FROM materias m
INNER JOIN materias_duplicadas md ON m.nombre = md.nombre 
    AND (m.carrera_id = md.carrera_id OR (m.carrera_id IS NULL AND md.carrera_id IS NULL))
    AND (m.curso_pre_admision_id = md.curso_pre_admision_id OR (m.curso_pre_admision_id IS NULL AND md.curso_pre_admision_id IS NULL))
WHERE m.id_materia != md.id_materia_a_mantener;

-- Verificar que se eliminaron los duplicados
SELECT 'Verificación después de eliminar duplicados:' as mensaje;
SELECT nombre, COUNT(*) as cantidad
FROM materias 
GROUP BY nombre 
HAVING COUNT(*) > 1
ORDER BY cantidad DESC;

-- Mostrar el total de materias restantes
SELECT 'Total de materias después de limpiar:' as mensaje;
SELECT COUNT(*) as total_materias FROM materias;

-- Eliminar tabla temporal
DROP TEMPORARY TABLE IF EXISTS materias_duplicadas; 