<?php
// afiliacion_acudiente.php - Logica del acudiente (Tab 1: Registro + Tab 2: Mis Solicitudes)

trait afiliacion_acudiente
{
    // ============================================================
    // TAB 1: REGISTRO DE DEPORTISTA (Acudiente - Rol 3)
    // ============================================================

    // 1. Crear registro completo: persona + deportista + vinculo acudiente + usuario
    function crear_registro_acudiente()
    {
        $this->validar_token_simple();

        // Verificar que sea acudiente o admin (super admin prueba como desarrollador)
        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos para registrar deportistas');
            return;
        }

        $persona_id_acudiente = $this->_obtener_persona_id();
        if ($persona_id_acudiente <= 0) {
            $this->_error('No se pudo identificar al acudiente en sesion');
            return;
        }

        // Si es admin y envia acudiente_id_override, registrar a nombre de ese acudiente
        if ($this->_es_admin() && isset($_POST['acudiente_id_override'])) {
            $override_id = intval($_POST['acudiente_id_override']);
            if ($override_id > 0) {
                $existe_acu = $this->_obtener_valor(
                    "SELECT COUNT(*) FROM persona WHERE id = ?",
                    array($override_id)
                );
                if (intval($existe_acu) > 0) {
                    $persona_id_acudiente = $override_id;
                }
            }
        }

        // 2. Determinar si es borrador o envio a revision
        if (isset($_POST['modo_guardado'])) {
            $modo = $_POST['modo_guardado'];
        } else {
            $modo = 'borrador';
        }

        // 3. Validar campos segun modo
        $errores = $this->_validar_registro($modo);
        if (!empty($errores)) {
            $this->_error(implode(', ', $errores));
            return;
        }

        // 4. Leer datos del deportista desde POST
        if (isset($_POST['dep_nombre1'])) { $dep_nombre1 = trim($_POST['dep_nombre1']); } else { $dep_nombre1 = ''; }
        if (isset($_POST['dep_nombre2'])) { $dep_nombre2 = trim($_POST['dep_nombre2']); } else { $dep_nombre2 = ''; }
        if (isset($_POST['dep_apellido1'])) { $dep_apellido1 = trim($_POST['dep_apellido1']); } else { $dep_apellido1 = ''; }
        if (isset($_POST['dep_apellido2'])) { $dep_apellido2 = trim($_POST['dep_apellido2']); } else { $dep_apellido2 = ''; }
        if (isset($_POST['dep_tipo_documento'])) { $dep_tipo_doc = trim($_POST['dep_tipo_documento']); } else { $dep_tipo_doc = 'TI'; }
        if (isset($_POST['dep_identificacion'])) { $dep_identificacion = trim($_POST['dep_identificacion']); } else { $dep_identificacion = ''; }
        if (isset($_POST['dep_fecha_nacimiento'])) { $dep_fecha_nac = trim($_POST['dep_fecha_nacimiento']); } else { $dep_fecha_nac = ''; }
        if (isset($_POST['dep_genero'])) { $dep_genero = trim($_POST['dep_genero']); } else { $dep_genero = 'M'; }
        if (isset($_POST['dep_celular'])) { $dep_celular = trim($_POST['dep_celular']); } else { $dep_celular = ''; }
        if (isset($_POST['dep_correo'])) { $dep_correo = trim($_POST['dep_correo']); } else { $dep_correo = ''; }
        if (isset($_POST['dep_direccion'])) { $dep_direccion = trim($_POST['dep_direccion']); } else { $dep_direccion = ''; }
        if (isset($_POST['dep_categoria_id'])) { $dep_categoria = intval($_POST['dep_categoria_id']); } else { $dep_categoria = 0; }
        if (isset($_POST['dep_eps'])) { $dep_eps = trim($_POST['dep_eps']); } else { $dep_eps = ''; }
        if (isset($_POST['dep_rh'])) { $dep_rh = trim($_POST['dep_rh']); } else { $dep_rh = ''; }
        if (isset($_POST['dep_alergias'])) { $dep_alergias = trim($_POST['dep_alergias']); } else { $dep_alergias = ''; }
        if (isset($_POST['dep_contacto_emergencia_nombre'])) { $dep_contacto_nombre = trim($_POST['dep_contacto_emergencia_nombre']); } else { $dep_contacto_nombre = ''; }
        if (isset($_POST['dep_contacto_emergencia_telefono'])) { $dep_contacto_tel = trim($_POST['dep_contacto_emergencia_telefono']); } else { $dep_contacto_tel = ''; }
        if (isset($_POST['dep_observaciones'])) { $dep_observaciones = trim($_POST['dep_observaciones']); } else { $dep_observaciones = ''; }

        // 5. Leer datos del acudiente (editables)
        if (isset($_POST['acu_celular'])) { $acu_celular = trim($_POST['acu_celular']); } else { $acu_celular = ''; }
        if (isset($_POST['acu_correo'])) { $acu_correo = trim($_POST['acu_correo']); } else { $acu_correo = ''; }
        if (isset($_POST['acu_direccion'])) { $acu_direccion = trim($_POST['acu_direccion']); } else { $acu_direccion = ''; }
        if (isset($_POST['parentesco'])) { $parentesco = trim($_POST['parentesco']); } else { $parentesco = 'padre'; }

        // 6. Verificar que el documento del deportista no exista ya
        if ($dep_identificacion !== '') {
            $existe_doc = $this->_obtener_valor(
                "SELECT COUNT(*) FROM persona WHERE identificacion = ?",
                array($dep_identificacion)
            );
            if (intval($existe_doc) > 0) {
                $this->_error('Ya existe una persona registrada con el documento ' . $dep_identificacion);
                return;
            }
        }

        // 7. Definir estado segun modo
        if ($modo === 'enviar') {
            $estado_deportista = 'pendiente_revision';
        } else {
            $estado_deportista = 'borrador';
        }

        // 8. Generar usuario automatico para el deportista
        $usuario_generado = '';
        $password_hash = '';
        if ($dep_nombre1 !== '' && $dep_apellido1 !== '' && $dep_identificacion !== '') {
            $usuario_generado = $this->_generar_usuario_deportista($dep_nombre1, $dep_apellido1, $dep_identificacion);
            $password_hash = $this->_generar_password($dep_identificacion);
        }

        // 9. Crear persona del deportista
        $datos_persona = array(
            'tipo_documento' => $dep_tipo_doc,
            'identificacion' => $dep_identificacion,
            'user' => $usuario_generado,
            'nombre1' => $dep_nombre1,
            'nombre2' => $dep_nombre2,
            'apellido1' => $dep_apellido1,
            'apellido2' => $dep_apellido2,
            'fecha_nacimiento' => ($dep_fecha_nac !== '') ? $dep_fecha_nac : null,
            'genero' => $dep_genero,
            'celular' => $dep_celular,
            'correo' => $dep_correo,
            'direccion' => $dep_direccion
        );

        $nueva_persona_id = $this->_insertar('persona', $datos_persona);
        if ($nueva_persona_id <= 0) {
            $this->_error('Error al crear el registro de persona del deportista');
            return;
        }

        // 10. Crear deportista vinculado a la persona
        $datos_deportista = array(
            'persona_id' => $nueva_persona_id,
            'categoria_id' => ($dep_categoria > 0) ? $dep_categoria : null,
            'eps' => $dep_eps,
            'rh' => $dep_rh,
            'alergias' => $dep_alergias,
            'contacto_emergencia_nombre' => $dep_contacto_nombre,
            'contacto_emergencia_telefono' => $dep_contacto_tel,
            'estado' => $estado_deportista,
            'fecha_afiliacion' => date('Y-m-d'),
            'observaciones' => $dep_observaciones
        );

        $nuevo_deportista_id = $this->_insertar('deportista', $datos_deportista);
        if ($nuevo_deportista_id <= 0) {
            $this->_error('Error al crear el registro de deportista');
            return;
        }

        // 11. Crear vinculo acudiente-deportista
        $datos_vinculo = array(
            'deportista_id' => $nuevo_deportista_id,
            'acudiente_id' => $persona_id_acudiente,
            'parentesco' => $parentesco,
            'es_principal' => 1
        );

        $vinculo_id = $this->_insertar('deportista_acudiente', $datos_vinculo);
        if ($vinculo_id <= 0) {
            $this->_error('Error al vincular acudiente con deportista');
            return;
        }

        // 12. Crear usuario para el deportista (si hay datos suficientes)
        if ($usuario_generado !== '' && $password_hash !== '') {
            $datos_usuario = array(
                'persona_id' => $nueva_persona_id,
                'rol_id' => 5,
                'login' => $usuario_generado,
                'password_hash' => $password_hash,
                'activo' => 0
            );
            $this->_insertar('usuario', $datos_usuario);
        }

        // 13. Actualizar datos del acudiente si los edito
        if ($acu_celular !== '' || $acu_correo !== '' || $acu_direccion !== '') {
            $datos_acudiente = array();
            if ($acu_celular !== '') { $datos_acudiente['celular'] = $acu_celular; }
            if ($acu_correo !== '') { $datos_acudiente['correo'] = $acu_correo; }
            if ($acu_direccion !== '') { $datos_acudiente['direccion'] = $acu_direccion; }
            if (!empty($datos_acudiente)) {
                $this->_actualizar('persona', $datos_acudiente, array('id' => $persona_id_acudiente));
            }
        }

        // 14. Si se envia a revision: guardar firma y firmar la cola obligatoria
        if ($modo === 'enviar') {
            if (isset($_POST['firma_imagen_data'])) {
                $firma_data_post = trim($_POST['firma_imagen_data']);
            } else {
                $firma_data_post = '';
            }
            $res_firma = $this->guardar_firma_acudiente($persona_id_acudiente, $firma_data_post);
            if (!$res_firma['ok']) {
                $this->_error($res_firma['msg']);
                return;
            }
            $this->firmar_cola_acudiente($persona_id_acudiente, $res_firma['ruta']);
        }

        // 15. Registrar en bitacora
        $this->_historiar(1, 'Registro afiliacion deportista', $nuevo_deportista_id, 'Deportista: ' . $dep_nombre1 . ' ' . $dep_apellido1 . ' - Estado: ' . $estado_deportista);

        // 16. Responder con datos para el frontend
        $respuesta = array(
            'deportista_id' => $nuevo_deportista_id,
            'persona_id' => $nueva_persona_id,
            'usuario_generado' => $usuario_generado,
            'estado' => $estado_deportista
        );

        if ($modo === 'enviar') {
            $this->_success('Solicitud de afiliacion enviada a revision. Usuario generado: ' . $usuario_generado . ' / Clave: ' . $dep_identificacion, $respuesta);
        } else {
            $this->_success('Borrador guardado correctamente', $respuesta);
        }
    }

    // 2. Actualizar borrador existente
    function actualizar_registro_acudiente()
    {
        $this->validar_token_simple();

        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $persona_id_acudiente = $this->_obtener_persona_id();
        if ($persona_id_acudiente <= 0) {
            $this->_error('No se pudo identificar al acudiente');
            return;
        }

        // 1. Obtener deportista_id del POST
        if (isset($_POST['deportista_id'])) {
            $deportista_id = intval($_POST['deportista_id']);
        } else {
            $deportista_id = 0;
        }
        if ($deportista_id <= 0) {
            $this->_error('ID de deportista requerido');
            return;
        }

        // 2. Verificar vinculo (el admin omite esta verificacion: acceso total)
        if ($this->_es_admin()) {
            $vinculo = $this->_obtener_fila(
                "SELECT da.id, d.estado FROM deportista_acudiente da
                 INNER JOIN deportista d ON d.id = da.deportista_id
                 WHERE da.deportista_id = ?",
                array($deportista_id)
            );
        } else {
            $vinculo = $this->_obtener_fila(
                "SELECT da.id, d.estado FROM deportista_acudiente da
                 INNER JOIN deportista d ON d.id = da.deportista_id
                 WHERE da.deportista_id = ? AND da.acudiente_id = ?",
                array($deportista_id, $persona_id_acudiente)
            );
        }
        if (empty($vinculo)) {
            $this->_error('No tiene acceso a este deportista');
            return;
        }

        // 3. Solo se puede editar en estado borrador o requiere_info
        $estado_actual = $vinculo['estado'];
        if ($estado_actual !== 'borrador' && $estado_actual !== 'requiere_info') {
            $this->_error('Solo se pueden editar solicitudes en estado borrador o que requieren informacion adicional');
            return;
        }

        // 4. Determinar modo de guardado
        if (isset($_POST['modo_guardado'])) {
            $modo = $_POST['modo_guardado'];
        } else {
            $modo = 'borrador';
        }

        // 5. Validar segun modo
        $errores = $this->_validar_registro($modo);
        if (!empty($errores)) {
            $this->_error(implode(', ', $errores));
            return;
        }

        // 6. Leer datos del POST
        if (isset($_POST['dep_nombre1'])) { $dep_nombre1 = trim($_POST['dep_nombre1']); } else { $dep_nombre1 = ''; }
        if (isset($_POST['dep_nombre2'])) { $dep_nombre2 = trim($_POST['dep_nombre2']); } else { $dep_nombre2 = ''; }
        if (isset($_POST['dep_apellido1'])) { $dep_apellido1 = trim($_POST['dep_apellido1']); } else { $dep_apellido1 = ''; }
        if (isset($_POST['dep_apellido2'])) { $dep_apellido2 = trim($_POST['dep_apellido2']); } else { $dep_apellido2 = ''; }
        if (isset($_POST['dep_tipo_documento'])) { $dep_tipo_doc = trim($_POST['dep_tipo_documento']); } else { $dep_tipo_doc = 'TI'; }
        if (isset($_POST['dep_identificacion'])) { $dep_identificacion = trim($_POST['dep_identificacion']); } else { $dep_identificacion = ''; }
        if (isset($_POST['dep_fecha_nacimiento'])) { $dep_fecha_nac = trim($_POST['dep_fecha_nacimiento']); } else { $dep_fecha_nac = ''; }
        if (isset($_POST['dep_genero'])) { $dep_genero = trim($_POST['dep_genero']); } else { $dep_genero = 'M'; }
        if (isset($_POST['dep_celular'])) { $dep_celular = trim($_POST['dep_celular']); } else { $dep_celular = ''; }
        if (isset($_POST['dep_correo'])) { $dep_correo = trim($_POST['dep_correo']); } else { $dep_correo = ''; }
        if (isset($_POST['dep_direccion'])) { $dep_direccion = trim($_POST['dep_direccion']); } else { $dep_direccion = ''; }
        if (isset($_POST['dep_categoria_id'])) { $dep_categoria = intval($_POST['dep_categoria_id']); } else { $dep_categoria = 0; }
        if (isset($_POST['dep_eps'])) { $dep_eps = trim($_POST['dep_eps']); } else { $dep_eps = ''; }
        if (isset($_POST['dep_rh'])) { $dep_rh = trim($_POST['dep_rh']); } else { $dep_rh = ''; }
        if (isset($_POST['dep_alergias'])) { $dep_alergias = trim($_POST['dep_alergias']); } else { $dep_alergias = ''; }
        if (isset($_POST['dep_contacto_emergencia_nombre'])) { $dep_contacto_nombre = trim($_POST['dep_contacto_emergencia_nombre']); } else { $dep_contacto_nombre = ''; }
        if (isset($_POST['dep_contacto_emergencia_telefono'])) { $dep_contacto_tel = trim($_POST['dep_contacto_emergencia_telefono']); } else { $dep_contacto_tel = ''; }
        if (isset($_POST['dep_observaciones'])) { $dep_observaciones = trim($_POST['dep_observaciones']); } else { $dep_observaciones = ''; }
        if (isset($_POST['parentesco'])) { $parentesco = trim($_POST['parentesco']); } else { $parentesco = ''; }

        // 7. Obtener persona_id del deportista
        $dep_persona_id = $this->_obtener_valor(
            "SELECT persona_id FROM deportista WHERE id = ?",
            array($deportista_id)
        );
        $dep_persona_id = intval($dep_persona_id);
        if ($dep_persona_id <= 0) {
            $this->_error('Deportista no encontrado');
            return;
        }

        // 8. Verificar documento unico (excluyendo el actual)
        if ($dep_identificacion !== '') {
            $existe_doc = $this->_obtener_valor(
                "SELECT COUNT(*) FROM persona WHERE identificacion = ? AND id != ?",
                array($dep_identificacion, $dep_persona_id)
            );
            if (intval($existe_doc) > 0) {
                $this->_error('Ya existe otra persona con el documento ' . $dep_identificacion);
                return;
            }
        }

        // 9. Definir nuevo estado
        if ($modo === 'enviar') {
            $nuevo_estado = 'pendiente_revision';
        } else {
            $nuevo_estado = 'borrador';
        }

        // 10. Actualizar persona del deportista
        $datos_persona = array(
            'tipo_documento' => $dep_tipo_doc,
            'identificacion' => $dep_identificacion,
            'nombre1' => $dep_nombre1,
            'nombre2' => $dep_nombre2,
            'apellido1' => $dep_apellido1,
            'apellido2' => $dep_apellido2,
            'genero' => $dep_genero,
            'celular' => $dep_celular,
            'correo' => $dep_correo,
            'direccion' => $dep_direccion
        );
        if ($dep_fecha_nac !== '') {
            $datos_persona['fecha_nacimiento'] = $dep_fecha_nac;
        }
        $this->_actualizar('persona', $datos_persona, array('id' => $dep_persona_id));

        // 11. Actualizar deportista
        $datos_deportista = array(
            'eps' => $dep_eps,
            'rh' => $dep_rh,
            'alergias' => $dep_alergias,
            'contacto_emergencia_nombre' => $dep_contacto_nombre,
            'contacto_emergencia_telefono' => $dep_contacto_tel,
            'estado' => $nuevo_estado,
            'observaciones' => $dep_observaciones
        );
        if ($dep_categoria > 0) {
            $datos_deportista['categoria_id'] = $dep_categoria;
        }
        $this->_actualizar('deportista', $datos_deportista, array('id' => $deportista_id));

        // 12. Actualizar parentesco si cambio (admin actualiza por deportista, acudiente por vinculo propio)
        if ($parentesco !== '') {
            if ($this->_es_admin()) {
                $this->_ejecutar(
                    "UPDATE deportista_acudiente SET parentesco = ? WHERE deportista_id = ?",
                    array($parentesco, $deportista_id)
                );
            } else {
                $this->_ejecutar(
                    "UPDATE deportista_acudiente SET parentesco = ? WHERE deportista_id = ? AND acudiente_id = ?",
                    array($parentesco, $deportista_id, $persona_id_acudiente)
                );
            }
        }

        // 13. Actualizar usuario si cambio documento
        if ($dep_identificacion !== '') {
            $usuario_generado = $this->_generar_usuario_deportista($dep_nombre1, $dep_apellido1, $dep_identificacion);
            $password_hash = $this->_generar_password($dep_identificacion);
            $existe_usuario = $this->_obtener_valor(
                "SELECT COUNT(*) FROM usuario WHERE persona_id = ?",
                array($dep_persona_id)
            );
            if (intval($existe_usuario) > 0) {
                $this->_ejecutar(
                    "UPDATE usuario SET login = ?, password_hash = ? WHERE persona_id = ?",
                    array($usuario_generado, $password_hash, $dep_persona_id)
                );
            }
        }

        // 14. Si se envia a revision: guardar firma y firmar la cola obligatoria
        if ($modo === 'enviar') {
            if (isset($_POST['firma_imagen_data'])) {
                $firma_data_post = trim($_POST['firma_imagen_data']);
            } else {
                $firma_data_post = '';
            }
            $res_firma = $this->guardar_firma_acudiente($persona_id_acudiente, $firma_data_post);
            if (!$res_firma['ok']) {
                $this->_error($res_firma['msg']);
                return;
            }
            $this->firmar_cola_acudiente($persona_id_acudiente, $res_firma['ruta']);
        }

        // 15. Bitacora
        $this->_historiar(3, 'Actualizar afiliacion deportista', $deportista_id, 'Estado: ' . $nuevo_estado);

        if ($modo === 'enviar') {
            $this->_success('Solicitud enviada a revision correctamente');
        } else {
            $this->_success('Borrador actualizado correctamente');
        }
    }

    // 3. Subir documento individual por tipo
    function subir_documento_acudiente()
    {
        $this->validar_token_simple();

        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $persona_id_acudiente = $this->_obtener_persona_id();

        // 1. Leer parametros
        if (isset($_POST['deportista_id'])) {
            $deportista_id = intval($_POST['deportista_id']);
        } else {
            $deportista_id = 0;
        }
        if (isset($_POST['tipo_documento_id'])) {
            $tipo_doc_id = intval($_POST['tipo_documento_id']);
        } else {
            $tipo_doc_id = 0;
        }

        if ($deportista_id <= 0 || $tipo_doc_id <= 0) {
            $this->_error('Deportista y tipo de documento son requeridos');
            return;
        }

        // 2. Verificar vinculo acudiente-deportista (el admin omite esta verificacion)
        if ($this->_es_admin()) {
            $vinculo = $this->_obtener_fila(
                "SELECT id FROM deportista_acudiente WHERE deportista_id = ?",
                array($deportista_id)
            );
        } else {
            $vinculo = $this->_obtener_fila(
                "SELECT id FROM deportista_acudiente WHERE deportista_id = ? AND acudiente_id = ?",
                array($deportista_id, $persona_id_acudiente)
            );
        }
        if (empty($vinculo)) {
            $this->_error('No tiene acceso a este deportista');
            return;
        }

        // 3. Verificar que el tipo_documento exista
        $tipo_doc = $this->_obtener_fila(
            "SELECT id, nombre, slug FROM tipo_documento WHERE id = ? AND activo = 1",
            array($tipo_doc_id)
        );
        if (empty($tipo_doc)) {
            $this->_error('Tipo de documento no valido');
            return;
        }

        // 4. Validar archivo
        if (!isset($_FILES['archivo'])) {
            $this->_error('No se recibio ningun archivo');
            return;
        }

        $validacion = $this->_validar_documento($_FILES['archivo']);
        if (!$validacion['ok']) {
            $this->_error($validacion['msg']);
            return;
        }

        // 5. Crear directorio de destino
        $carpeta = 'storage/afiliaciones/' . $deportista_id;

        // 6. Generar nombre seguro
        $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $nombre_archivo = $tipo_doc['slug'] . '_' . uniqid() . '.' . $ext;
        $destino = $carpeta . '/' . $nombre_archivo;

        // 7. Mover archivo
        if (!$this->_mover_archivo($_FILES['archivo']['tmp_name'], $destino)) {
            $this->_error('Error al guardar el archivo');
            return;
        }

        // 8. Eliminar documento anterior del mismo tipo (si existe)
        $doc_anterior = $this->_obtener_fila(
            "SELECT id, archivo FROM documento WHERE deportista_id = ? AND tipo_documento_id = ?",
            array($deportista_id, $tipo_doc_id)
        );
        if (!empty($doc_anterior)) {
            $this->_ejecutar(
                "DELETE FROM documento WHERE id = ?",
                array($doc_anterior['id'])
            );
        }

        // 9. Insertar nuevo registro en tabla documento
        $usuario_id = $this->_obtener_usuario_id();
        $datos_doc = array(
            'deportista_id' => $deportista_id,
            'tipo_documento_id' => $tipo_doc_id,
            'archivo' => $destino,
            'archivo_original' => $_FILES['archivo']['name'],
            'estado' => 'pendiente',
            'subido_por' => ($usuario_id > 0) ? $usuario_id : $persona_id_acudiente,
            'fecha_subida' => date('Y-m-d H:i:s')
        );
        $doc_id = $this->_insertar('documento', $datos_doc);

        // 10. Bitacora
        $this->_historiar(1, 'Subir documento afiliacion', $deportista_id, 'Tipo: ' . $tipo_doc['nombre'] . ' - Archivo: ' . $nombre_archivo);

        $this->_success('Documento subido correctamente', array(
            'documento_id' => $doc_id,
            'archivo' => $destino,
            'tipo_nombre' => $tipo_doc['nombre']
        ));
    }

    // 4. Eliminar documento propio
    function eliminar_documento_acudiente()
    {
        $this->validar_token();

        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $persona_id_acudiente = $this->_obtener_persona_id();

        if (isset($_POST['documento_id'])) {
            $documento_id = intval($_POST['documento_id']);
        } else {
            $documento_id = 0;
        }

        if ($documento_id <= 0) {
            $this->_error('ID de documento requerido');
            return;
        }

        // 1. Verificar documento (el admin omite el filtro por acudiente)
        if ($this->_es_admin()) {
            $doc = $this->_obtener_fila(
                "SELECT doc.id, doc.archivo, doc.deportista_id
                 FROM documento doc
                 WHERE doc.id = ?",
                array($documento_id)
            );
        } else {
            $doc = $this->_obtener_fila(
                "SELECT doc.id, doc.archivo, doc.deportista_id
                 FROM documento doc
                 INNER JOIN deportista_acudiente da ON da.deportista_id = doc.deportista_id
                 WHERE doc.id = ? AND da.acudiente_id = ?",
                array($documento_id, $persona_id_acudiente)
            );
        }
        if (empty($doc)) {
            $this->_error('Documento no encontrado o no tiene acceso');
            return;
        }

        // 2. Verificar que el deportista este en estado editable
        $estado = $this->_obtener_valor(
            "SELECT estado FROM deportista WHERE id = ?",
            array($doc['deportista_id'])
        );
        if ($estado !== 'borrador' && $estado !== 'requiere_info') {
            $this->_error('No se pueden eliminar documentos de solicitudes ya enviadas');
            return;
        }

        // 3. Eliminar registro (archivo queda en disco)
        $this->_ejecutar(
            "DELETE FROM documento WHERE id = ?",
            array($documento_id)
        );

        $this->_historiar(2, 'Eliminar documento afiliacion', $documento_id, 'Archivo: ' . $doc['archivo']);
        $this->_success('Documento eliminado correctamente');
    }

    // ============================================================
    // TAB 2: MIS SOLICITUDES (Acudiente - Rol 3)
    // ============================================================

    // 5. Listar solicitudes del acudiente logueado
    function listar_mis_solicitudes()
    {
        $this->validar_token();

        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        // 1. Consultar solicitudes (el admin ve las ultimas 100 globales, el acudiente solo las suyas)
        if ($this->_es_admin()) {
            $sql = "SELECT d.id AS deportista_id,
                           CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS deportista_nombre,
                           d.estado,
                           CASE
                               WHEN d.estado = 'aprobado' THEN 100
                               WHEN d.estado = 'activo' THEN 100
                               WHEN d.estado = 'pendiente_revision' THEN 50
                               WHEN d.estado = 'requiere_info' THEN 25
                               ELSE 0
                           END AS porcentaje_completado,
                           d.fecha_afiliacion AS fecha_solicitud,
                           d.observaciones,
                           d.created_at
                    FROM deportista d
                    INNER JOIN persona p ON d.persona_id = p.id
                    INNER JOIN deportista_acudiente da ON da.deportista_id = d.id
                    ORDER BY d.created_at DESC
                    LIMIT 100";
            $solicitudes = $this->_consultar($sql, array());
        } else {
            $persona_id_acudiente = $this->_obtener_persona_id();
            if ($persona_id_acudiente <= 0) {
                $this->_error('No se pudo identificar al acudiente');
                return;
            }

            $sql = "SELECT d.id AS deportista_id,
                           CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS deportista_nombre,
                           d.estado,
                           CASE
                               WHEN d.estado = 'aprobado' THEN 100
                               WHEN d.estado = 'activo' THEN 100
                               WHEN d.estado = 'pendiente_revision' THEN 50
                               WHEN d.estado = 'requiere_info' THEN 25
                               ELSE 0
                           END AS porcentaje_completado,
                           d.fecha_afiliacion AS fecha_solicitud,
                           d.observaciones,
                           d.created_at
                    FROM deportista d
                    INNER JOIN persona p ON d.persona_id = p.id
                    INNER JOIN deportista_acudiente da ON da.deportista_id = d.id
                    WHERE da.acudiente_id = ?
                    ORDER BY d.created_at DESC";

            $solicitudes = $this->_consultar($sql, array($persona_id_acudiente));
        }

        // 2. Formatear respuesta
        $filas = array();
        for ($i = 0; $i < count($solicitudes); $i++) {
            $solicitudes[$i]['_NUM_'] = $i + 1;
            $solicitudes[$i]['deportista_id_encoded'] = urlsafe_b64encode($solicitudes[$i]['deportista_id']);
            $filas[] = $solicitudes[$i];
        }

        echo json_encode(array(
            'error' => false,
            'total' => count($filas),
            'rows' => $filas
        ));
    }

    // 6. Obtener detalle completo de una solicitud propia
    function obtener_mis_solicitud()
    {
        $this->validar_token();

        if (!$this->_es_acudiente() && !$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $persona_id_acudiente = $this->_obtener_persona_id();

        if (isset($_POST['deportista_id'])) {
            $deportista_id = intval($_POST['deportista_id']);
        } else {
            $deportista_id = 0;
        }

        if ($deportista_id <= 0) {
            $this->_error('ID de deportista requerido');
            return;
        }

        // 1. Verificar vinculo (el admin omite esta verificacion)
        if ($this->_es_admin()) {
            $vinculo = $this->_obtener_fila(
                "SELECT da.id, da.parentesco, da.es_principal, da.acudiente_id
                 FROM deportista_acudiente da
                 WHERE da.deportista_id = ?
                 ORDER BY da.id ASC",
                array($deportista_id)
            );
            if (!empty($vinculo)) {
                $persona_id_acudiente = intval($vinculo['acudiente_id']);
            }
        } else {
            $vinculo = $this->_obtener_fila(
                "SELECT da.id, da.parentesco, da.es_principal
                 FROM deportista_acudiente da
                 WHERE da.deportista_id = ? AND da.acudiente_id = ?",
                array($deportista_id, $persona_id_acudiente)
            );
        }
        if (empty($vinculo)) {
            $this->_error('No tiene acceso a este deportista');
            return;
        }

        // 2. Obtener datos completos del deportista
        $deportista = $this->_obtener_fila(
            "SELECT d.*, p.tipo_documento, p.identificacion, p.nombre1, p.nombre2,
                    p.apellido1, p.apellido2, p.fecha_nacimiento, p.genero,
                    p.celular, p.correo, p.direccion,
                    c.nombre AS categoria_nombre
             FROM deportista d
             INNER JOIN persona p ON d.persona_id = p.id
             LEFT JOIN categoria c ON c.id = d.categoria_id
             WHERE d.id = ?",
            array($deportista_id)
        );

        if (empty($deportista)) {
            $this->_error('Deportista no encontrado');
            return;
        }

        // 3. Obtener documentos del deportista
        $documentos = $this->_consultar(
            "SELECT doc.id, doc.archivo, doc.archivo_original, doc.estado,
                    doc.fecha_subida, td.nombre AS tipo_nombre, td.slug AS tipo_slug
             FROM documento doc
             INNER JOIN tipo_documento td ON td.id = doc.tipo_documento_id
             WHERE doc.deportista_id = ?
             ORDER BY td.id",
            array($deportista_id)
        );

        // 4. Datos del acudiente
        $acudiente = $this->_obtener_fila(
            "SELECT p.nombre1, p.nombre2, p.apellido1, p.apellido2,
                    p.celular, p.correo, p.direccion, p.tipo_documento, p.identificacion
             FROM persona p
             WHERE p.id = ?",
            array($persona_id_acudiente)
        );

        $deportista['parentesco'] = $vinculo['parentesco'];
        $deportista['documentos'] = $documentos;
        $deportista['acudiente'] = $acudiente;

        $this->_success('', $deportista);
    }

    // ============================================================
    // COMPARTIDAS
    // ============================================================

    // 7. Listar categorias activas (para select)
    function listar_categorias()
    {
        $this->validar_token();

        $categorias = $this->_consultar(
            "SELECT id, nombre, descripcion, edad_minima, edad_maxima, genero
             FROM categoria WHERE activo = 1 ORDER BY edad_minima"
        );

        $this->_success('', $categorias);
    }

    // 8. Listar tipos de documento (para modal documentos)
    function listar_tipos_documento()
    {
        $this->validar_token();

        $tipos = $this->_consultar(
            "SELECT id, nombre, slug, obligatorio FROM tipo_documento WHERE activo = 1 ORDER BY id"
        );

        // Si se paso deportista_id, incluir estado de cada documento
        if (isset($_POST['deportista_id'])) {
            $deportista_id = intval($_POST['deportista_id']);
        } else {
            $deportista_id = 0;
        }

        if ($deportista_id > 0) {
            for ($i = 0; $i < count($tipos); $i++) {
                $doc = $this->_obtener_fila(
                    "SELECT id, archivo, archivo_original, estado, fecha_subida
                     FROM documento
                     WHERE deportista_id = ? AND tipo_documento_id = ?
                     ORDER BY fecha_subida DESC LIMIT 1",
                    array($deportista_id, $tipos[$i]['id'])
                );
                if (!empty($doc)) {
                    $tipos[$i]['documento'] = $doc;
                    $tipos[$i]['subido'] = true;
                } else {
                    $tipos[$i]['documento'] = null;
                    $tipos[$i]['subido'] = false;
                }
            }
        }

        $this->_success('', $tipos);
    }

    // ============================================================
    // VALIDACIONES
    // ============================================================

    // 9. Validar campos del registro segun modo (borrador o envio)
    function _validar_registro($modo)
    {
        $errores = array();

        // Campos minimos para borrador
        if (isset($_POST['dep_nombre1'])) {
            $nombre1 = trim($_POST['dep_nombre1']);
        } else {
            $nombre1 = '';
        }
        if (isset($_POST['dep_apellido1'])) {
            $apellido1 = trim($_POST['dep_apellido1']);
        } else {
            $apellido1 = '';
        }

        if ($nombre1 === '') {
            $errores[] = 'El primer nombre del deportista es requerido';
        }
        if ($apellido1 === '') {
            $errores[] = 'El primer apellido del deportista es requerido';
        }

        // Campos completos para envio a revision
        if ($modo === 'enviar') {
            if (isset($_POST['dep_identificacion'])) {
                $identificacion = trim($_POST['dep_identificacion']);
            } else {
                $identificacion = '';
            }
            if ($identificacion === '') {
                $errores[] = 'El numero de documento del deportista es requerido';
            }

            if (isset($_POST['dep_fecha_nacimiento'])) {
                $fecha_nac = trim($_POST['dep_fecha_nacimiento']);
            } else {
                $fecha_nac = '';
            }
            if ($fecha_nac === '') {
                $errores[] = 'La fecha de nacimiento es requerida';
            }

            if (isset($_POST['dep_eps'])) {
                $eps = trim($_POST['dep_eps']);
            } else {
                $eps = '';
            }
            if ($eps === '') {
                $errores[] = 'La EPS es requerida';
            }

            if (isset($_POST['dep_contacto_emergencia_nombre'])) {
                $contacto_nombre = trim($_POST['dep_contacto_emergencia_nombre']);
            } else {
                $contacto_nombre = '';
            }
            if ($contacto_nombre === '') {
                $errores[] = 'El contacto de emergencia es requerido';
            }

            if (isset($_POST['dep_contacto_emergencia_telefono'])) {
                $contacto_tel = trim($_POST['dep_contacto_emergencia_telefono']);
            } else {
                $contacto_tel = '';
            }
            if ($contacto_tel === '') {
                $errores[] = 'El telefono de emergencia es requerido';
            }

            // La firma dibujada del acudiente es obligatoria al enviar
            if (isset($_POST['firma_imagen_data'])) {
                $firma_data = trim($_POST['firma_imagen_data']);
            } else {
                $firma_data = '';
            }
            if ($firma_data === '') {
                $errores[] = 'La firma del acudiente es requerida para enviar a revision';
            } else if (strpos($firma_data, 'data:image/png;base64,') !== 0) {
                $errores[] = 'La firma recibida no tiene un formato valido';
            }
        }

        return $errores;
    }
}