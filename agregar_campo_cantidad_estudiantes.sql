-- Script para agregar el campo cantidad_estudiantes a la tabla tarjetas_disposicion
ALTER TABLE `tarjetas_disposicion` 
ADD COLUMN `cantidad_estudiantes` int(11) NOT NULL DEFAULT 0 AFTER `profesor_id`;

-- Actualizar registros existentes con un valor por defecto
UPDATE `tarjetas_disposicion` SET `cantidad_estudiantes` = 50 WHERE `cantidad_estudiantes` = 0; 