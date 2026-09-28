-- ============================================================
-- VISTAS PARA MODULO AFILIACION - Versión Completa
-- Ejecutar en la base de datos voley_plus
-- ============================================================

-- Vista v_deportista: deportistas con datos completos de persona
CREATE OR REPLACE VIEW `v_deportista` AS
SELECT 
    d.`id`,
    d.`persona_id`,
    d.`categoria_id`,
    d.`eps`,
    d.`rh`,
    d.`alergias`,
    d.`contacto_emergencia_nombre` AS `contacto_emergencia`,
    d.`contacto_emergencia_telefono` AS `telefono_emergencia`,
    d.`estado`,
    d.`fecha_afiliacion`,
    d.`observaciones` AS `deportista_observaciones`,
    d.`created_at`,
    p.`tipo_documento`,
    p.`identificacion`,
    p.`nombre1`, p.`nombre2`, p.`apellido1`, p.`apellido2`,
    p.`fecha_nacimiento`,
    p.`genero`,
    p.`celular`,
    p.`correo`,
    p.`direccion`,
    p.`foto`
FROM `deportista` d
INNER JOIN `persona` p ON d.`persona_id` = p.`id`;

-- Vista v_acudiente: acudientes vinculados a deportistas con datos completos
CREATE OR REPLACE VIEW `v_acudiente` AS
SELECT 
    da.`id`,
    da.`deportista_id`,
    da.`acudiente_id` AS `persona_id`,
    da.`parentesco`,
    da.`es_principal`,
    p.`tipo_documento`,
    p.`identificacion`,
    p.`nombre1`, p.`nombre2`, p.`apellido1`, p.`apellido2`,
    p.`celular`,
    p.`correo`,
    p.`direccion`,
    u.`id` AS `usuario_id`,
    u.`rol_id` AS `usuario_rol`
FROM `deportista_acudiente` da
INNER JOIN `persona` p ON da.`acudiente_id` = p.`id`
LEFT JOIN `usuario` u ON p.`id` = u.`persona_id`;

-- Vista v_afiliacion: combina deportista + acudiente + documentos + usuario
-- Incluye TODOS los campos necesarios para formularios y listados
CREATE OR REPLACE VIEW `v_afiliacion` AS
SELECT 
    da.`id` AS `id`,
    da.`acudiente_id` AS `acudiente_id`,
    d.`id` AS `deportista_id`,
    d.`estado` AS `estado`,
    CASE 
        WHEN d.`estado` = 'aprobado' THEN 100
        WHEN d.`estado` = 'activo' THEN 100
        WHEN d.`estado` = 'pendiente_revision' THEN 50
        WHEN d.`estado` = 'requiere_info' THEN 25
        ELSE 0
    END AS `porcentaje_completado`,
    d.`fecha_afiliacion` AS `fecha_solicitud`,
    d.`fecha_afiliacion` AS `fecha_aprobacion`,
    d.`observaciones` AS `observaciones`,
    d.`created_at`,
    d.`updated_at`,
    -- Datos deportista
    dp.`tipo_documento` AS `dep_tipo_documento`,
    dp.`identificacion` AS `dep_identificacion`,
    dp.`nombre1` AS `dep_nombre1`,
    dp.`nombre2` AS `dep_nombre2`,
    dp.`apellido1` AS `dep_apellido1`,
    dp.`apellido2` AS `dep_apellido2`,
    dp.`fecha_nacimiento` AS `dep_fecha_nacimiento`,
    dp.`genero` AS `dep_genero`,
    dp.`celular` AS `dep_celular`,
    dp.`correo` AS `dep_correo`,
    dp.`direccion` AS `dep_direccion`,
    dp.`foto` AS `dep_foto`,
    dp.`categoria_id` AS `dep_categoria_id`,
    dp.`eps` AS `dep_eps`,
    dp.`rh` AS `dep_rh`,
    dp.`alergias` AS `dep_alergias`,
    dp.`contacto_emergencia` AS `dep_contacto_emergencia_nombre`,
    dp.`telefono_emergencia` AS `dep_contacto_emergencia_telefono`,
    -- Datos acudiente
    ap.`tipo_documento` AS `acu_tipo_documento`,
    ap.`identificacion` AS `acu_identificacion`,
    ap.`nombre1` AS `acu_nombre1`,
    ap.`nombre2` AS `acu_nombre2`,
    ap.`apellido1` AS `acu_apellido1`,
    ap.`apellido2` AS `acu_apellido2`,
    ap.`celular` AS `acu_celular`,
    ap.`correo` AS `acu_correo`,
    ap.`direccion` AS `acu_direccion`,
    da.`parentesco` AS `acu_parentesco`,
    da.`es_principal` AS `acu_es_principal`,
    ap.`usuario_id` AS `acu_usuario_id`,
    ap.`usuario_rol` AS `acu_usuario_rol`,
    -- Documentos: subquery para obtener último documento por tipo
    (SELECT GROUP_CONCAT(CONCAT(td.`slug`, ':', doc.`archivo`, ':', doc.`estado`) SEPARATOR '|')
     FROM `documento` doc
     INNER JOIN `tipo_documento` td ON doc.`tipo_documento_id` = td.`id`
     WHERE doc.`deportista_id` = d.`id` AND doc.`estado` IN ('aprobado', 'pendiente', 'en_revision')
    ) AS `documentos_info`
FROM `deportista` d
INNER JOIN `deportista_acudiente` da ON d.`id` = da.`deportista_id`
INNER JOIN `persona` dp ON d.`persona_id` = dp.`id`
INNER JOIN `persona` ap ON da.`acudiente_id` = ap.`id`
WHERE da.`es_principal` = 1
   OR da.`id` = (SELECT MIN(id) FROM deportista_acudiente WHERE deportista_id = d.id);

-- Vista v_documentos_deportista: documentos por deportista para listado en modal
CREATE OR REPLACE VIEW `v_documentos_deportista` AS
SELECT 
    doc.`id`,
    doc.`deportista_id`,
    doc.`tipo_documento_id`,
    td.`nombre` AS `tipo_nombre`,
    td.`slug` AS `tipo_slug`,
    td.`obligatorio` AS `tipo_obligatorio`,
    doc.`archivo`,
    doc.`archivo_original`,
    doc.`estado`,
    doc.`observaciones`,
    doc.`subido_por`,
    doc.`fecha_subida`,
    doc.`revisado_por`,
    doc.`fecha_revision`
FROM `documento` doc
INNER JOIN `tipo_documento` td ON doc.`tipo_documento_id` = td.`id`
WHERE td.`activo` = 1;

-- ============================================================
-- FIN VISTAS
-- ============================================================