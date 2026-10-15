-- ============================================================
-- ROLLBACK Fase A — Asistencia (solo si algo sale mal)
-- Devuelve la BD al estado del respaldo
-- database/respaldos/2026_10_15_faseA_antes.sql
-- ============================================================

-- 1. Quitar permisos de acciones del modulo
DELETE FROM `admin_permiso_accion`
WHERE `accion` IN (SELECT `id` FROM `admin_accion` WHERE `menu` = 'asistencia');

-- 2. Quitar permisos del menu (dejar solo los que habia: 1 y 4)
DELETE FROM `admin_permiso_menu` WHERE `menu` = 'asistencia' AND `rol` IN (2, 3);

-- 3. Quitar las acciones del modulo
DELETE FROM `admin_accion` WHERE `menu` = 'asistencia';

-- 4. Quitar las columnas nuevas
ALTER TABLE `asistencia`
  DROP COLUMN IF EXISTS `origen`,
  DROP COLUMN IF EXISTS `minutos`,
  DROP COLUMN IF EXISTS `hora_salida`,
  DROP COLUMN IF EXISTS `hora_entrada`;

ALTER TABLE `clase`
  DROP COLUMN IF EXISTS `activo`,
  DROP COLUMN IF EXISTS `fecha_cierre`,
  DROP COLUMN IF EXISTS `estado`;
