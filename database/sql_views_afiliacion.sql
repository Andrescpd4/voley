-- ============================================================
-- VISTAS PARA MODULO AFILIACION
-- Ejecutar en la base de datos voley_plus
-- ============================================================

-- Asegurar columna observaciones en deportista
ALTER TABLE `deportista` ADD COLUMN IF NOT EXISTS `observaciones` TEXT DEFAULT NULL AFTER `fecha_afiliacion`;

-- Observaciones del club al revisar (separadas de las observaciones que escribe el acudiente)
ALTER TABLE `deportista` ADD COLUMN IF NOT EXISTS `observaciones_revision` TEXT DEFAULT NULL AFTER `observaciones`;

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

-- Vista v_afiliacion: combina deportista + acudiente + documentos
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
    d.`observaciones_revision` AS `observaciones_revision`,
    -- Datos deportista
    pd.`tipo_documento` AS `deportista_tipo_documento`,
    pd.`identificacion` AS `deportista_identificacion`,
    pd.`nombre1` AS `deportista_nombre1`,
    pd.`nombre2` AS `deportista_nombre2`,
    pd.`apellido1` AS `deportista_apellido1`,
    pd.`apellido2` AS `deportista_apellido2`,
    pd.`fecha_nacimiento` AS `deportista_fecha_nacimiento`,
    pd.`genero` AS `deportista_genero`,
    pd.`celular` AS `deportista_celular`,
    pd.`correo` AS `deportista_correo`,
    pd.`direccion` AS `deportista_direccion`,
    pd.`foto` AS `deportista_foto`,
    d.`eps` AS `deportista_eps`,
    d.`rh` AS `deportista_rh`,
    d.`alergias` AS `deportista_alergias`,
    d.`contacto_emergencia_nombre` AS `deportista_contacto_emergencia`,
    d.`contacto_emergencia_telefono` AS `deportista_telefono_emergencia`,
    d.`categoria_id` AS `deportista_categoria_id`,
    -- Datos acudiente
    pa.`tipo_documento` AS `acudiente_tipo_documento`,
    pa.`identificacion` AS `acudiente_identificacion`,
    pa.`nombre1` AS `acudiente_nombre1`,
    pa.`nombre2` AS `acudiente_nombre2`,
    pa.`apellido1` AS `acudiente_apellido1`,
    pa.`apellido2` AS `acudiente_apellido2`,
    pa.`celular` AS `acudiente_celular`,
    pa.`correo` AS `acudiente_correo`,
    pa.`direccion` AS `acudiente_direccion`,
    da.`parentesco` AS `acudiente_parentesco`,
    -- Usuario acudiente
    ua.`id` AS `acudiente_usuario_id`,
    ua.`rol_id` AS `acudiente_usuario_rol`,
    -- PDF principal (documento_identidad)
    (SELECT doc.`archivo` FROM `documento` doc 
     INNER JOIN `tipo_documento` td ON doc.`tipo_documento_id` = td.`id`
     WHERE doc.`deportista_id` = d.`id` 
     AND td.`slug` = 'documento_identidad'
     AND doc.`estado` = 'aprobado'
     ORDER BY doc.`fecha_subida` DESC LIMIT 1) AS `pdf_ruta`,
    d.`created_at`
FROM `deportista` d
INNER JOIN `deportista_acudiente` da ON d.`id` = da.`deportista_id`
INNER JOIN `persona` pd ON d.`persona_id` = pd.`id`
INNER JOIN `persona` pa ON da.`acudiente_id` = pa.`id`
LEFT JOIN `usuario` ua ON pa.`id` = ua.`persona_id`
WHERE da.`es_principal` = 1
   OR da.`id` = (SELECT MIN(id) FROM deportista_acudiente WHERE deportista_id = d.id);

-- Vista v_documentos_deportista: documentos por deportista y tipo
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