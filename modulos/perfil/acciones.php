<?php
/**
 * MODULO: Perfil de Usuario
 * Backend de acciones en formato JSON.
 * Maneja:
 * - Actualizacion de datos personales
 * - Cambio de contrasena con cifrado BCRYPT
 * - Actualizacion de foto de perfil
 * - Listado de deportistas vinculados (solo propios)
 * - Obtencion de ficha deportiva completa
 */

class Formulario extends Base
{
    // ============================================================
    // VALIDACION DE SEGURIDAD Y HELPERS
    // ============================================================

    // Valida que el token este presente en los encabezados HTTP
    private function validar_token_simple()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(array(
                'error' => true,
                'msg' => 'Sesion no valida o token ausente'
            ));
            exit();
        }
    }

    // Obtiene el ID de la persona en sesion como entero seguro
    private function obtener_persona_sesion_id()
    {
        if (isset($_SESSION['persona_id'])) {
            return intval($_SESSION['persona_id']);
        }
        return 0;
    }

    // ============================================================
    // ACCION PRINCIPAL (PAGINA)
    // ============================================================

    public function perfil()
    {
        // Metodo de soporte para la ruta de pagina
        echo json_encode(array('error' => false, 'msg' => 'OK'));
    }

    // ============================================================
    // 1. ACTUALIZAR DATOS PERSONALES
    // ============================================================

    public function aceptar()
    {
        $this->validar_token_simple();

        $persona_id = $this->obtener_persona_sesion_id();
        if ($persona_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Sesion de usuario no valida'));
            return;
        }

        // 1. Leer y limpiar campos del formulario
        if (isset($_POST['tipo_documento'])) {
            $tipo_documento = trim($_POST['tipo_documento']);
        } else {
            $tipo_documento = 'CC';
        }

        if (isset($_POST['identificacion'])) {
            $identificacion = trim($_POST['identificacion']);
        } else {
            $identificacion = '';
        }

        if (isset($_POST['nombre1'])) {
            $nombre1 = trim($_POST['nombre1']);
        } else {
            $nombre1 = '';
        }

        if (isset($_POST['nombre2'])) {
            $nombre2 = trim($_POST['nombre2']);
        } else {
            $nombre2 = '';
        }

        if (isset($_POST['apellido1'])) {
            $apellido1 = trim($_POST['apellido1']);
        } else {
            $apellido1 = '';
        }

        if (isset($_POST['apellido2'])) {
            $apellido2 = trim($_POST['apellido2']);
        } else {
            $apellido2 = '';
        }

        if (isset($_POST['fecha_nacimiento'])) {
            $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
        } else {
            $fecha_nacimiento = null;
        }

        if (isset($_POST['genero'])) {
            $genero = trim($_POST['genero']);
        } else {
            $genero = 'M';
        }

        if (isset($_POST['celular'])) {
            $celular = trim($_POST['celular']);
        } else {
            $celular = '';
        }

        if (isset($_POST['correo'])) {
            $correo = trim($_POST['correo']);
        } else {
            $correo = '';
        }

        if (isset($_POST['direccion'])) {
            $direccion = trim($_POST['direccion']);
        } else {
            $direccion = '';
        }

        // 2. Validaciones obligatorias
        if ($identificacion === '') {
            echo json_encode(array('error' => true, 'msg' => 'El numero de documento es obligatorio'));
            return;
        }

        if ($nombre1 === '') {
            echo json_encode(array('error' => true, 'msg' => 'El primer nombre es obligatorio'));
            return;
        }

        if ($apellido1 === '') {
            echo json_encode(array('error' => true, 'msg' => 'El primer apellido es obligatorio'));
            return;
        }

        // 3. Verificar que la identificacion no pertenezca a otra persona
        $identificacion_limpia = addslashes($identificacion);
        $sql_duplicado = "SELECT id FROM persona WHERE identificacion = '$identificacion_limpia' AND id <> $persona_id";
        $existe_otro = $this->db->select_one($sql_duplicado);

        if (is_string($existe_otro) && $existe_otro !== '') {
            echo json_encode(array('error' => true, 'msg' => 'El documento ingresado ya pertenece a otro usuario'));
            return;
        }

        // 4. Preparar arreglo de actualizacion
        $datos_actualizar = array(
            'tipo_documento'   => $tipo_documento,
            'identificacion'   => $identificacion,
            'nombre1'          => $nombre1,
            'nombre2'          => $nombre2,
            'apellido1'        => $apellido1,
            'apellido2'        => $apellido2,
            'fecha_nacimiento' => !empty($fecha_nacimiento) ? $fecha_nacimiento : null,
            'genero'           => $genero,
            'celular'          => $celular,
            'correo'           => $correo,
            'direccion'        => $direccion
        );

        $this->db->update('persona', $datos_actualizar, array('id' => $persona_id));

        // 5. Actualizar variables de sesion activas
        $_SESSION['nombre_usuario'] = trim($nombre1 . ' ' . $apellido1);

        echo json_encode(array(
            'error' => false,
            'msg'   => 'Datos personales actualizados correctamente'
        ));
    }

    // ============================================================
    // 2. CAMBIAR CONTRASENA
    // ============================================================

    public function cambiar_clave()
    {
        $this->validar_token_simple();

        $persona_id = $this->obtener_persona_sesion_id();
        if ($persona_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Sesion de usuario no valida'));
            return;
        }

        if (isset($_POST['clave_actual'])) {
            $clave_actual = trim($_POST['clave_actual']);
        } else {
            $clave_actual = '';
        }

        if (isset($_POST['nueva_clave'])) {
            $nueva_clave = trim($_POST['nueva_clave']);
        } else {
            $nueva_clave = '';
        }

        if (isset($_POST['confirmar_clave'])) {
            $confirmar_clave = trim($_POST['confirmar_clave']);
        } else {
            $confirmar_clave = '';
        }

        // 1. Validaciones basicas
        if ($clave_actual === '') {
            echo json_encode(array('error' => true, 'msg' => 'Debe ingresar la contrasena actual'));
            return;
        }

        if ($nueva_clave === '') {
            echo json_encode(array('error' => true, 'msg' => 'Debe ingresar la nueva contrasena'));
            return;
        }

        if (strlen($nueva_clave) < 5) {
            echo json_encode(array('error' => true, 'msg' => 'La nueva contrasena debe tener al menos 5 caracteres'));
            return;
        }

        if ($nueva_clave !== $confirmar_clave) {
            echo json_encode(array('error' => true, 'msg' => 'La confirmacion de la contrasena no coincide'));
            return;
        }

        if ($clave_actual === $nueva_clave) {
            echo json_encode(array('error' => true, 'msg' => 'La nueva contrasena no puede ser igual a la actual'));
            return;
        }

        // 2. Consultar contrasena actual en base de datos
        $sql_usuario = "SELECT id, password_hash FROM usuario WHERE persona_id = $persona_id";
        $fila_usuario = $this->db->select_row($sql_usuario);

        if (empty($fila_usuario) || !isset($fila_usuario['password_hash'])) {
            echo json_encode(array('error' => true, 'msg' => 'No se encontro la cuenta de usuario'));
            return;
        }

        // 3. Verificar si la clave actual coincide
        $hash_almacenado = $fila_usuario['password_hash'];
        $clave_valida = password_verify($clave_actual, $hash_almacenado);

        if (!$clave_valida) {
            echo json_encode(array('error' => true, 'msg' => 'La contrasena actual es incorrecta'));
            return;
        }

        // 4. Generar nuevo hash seguro y actualizar
        $nuevo_hash = password_hash($nueva_clave, PASSWORD_BCRYPT);
        $this->db->update('usuario', array('password_hash' => $nuevo_hash), array('persona_id' => $persona_id));

        echo json_encode(array(
            'error' => false,
            'msg'   => 'Contrasena actualizada con exito'
        ));
    }

    // ============================================================
    // 3. CAMBIAR FOTO DE PERFIL
    // ============================================================

    public function cambiar_foto()
    {
        $this->validar_token_simple();

        $persona_id = $this->obtener_persona_sesion_id();
        if ($persona_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Sesion de usuario no valida'));
            return;
        }

        if (!isset($_FILES['archivo']) || empty($_FILES['archivo']['name'])) {
            echo json_encode(array('error' => true, 'msg' => 'No se ha seleccionado ningun archivo de imagen'));
            return;
        }

        // Validar tamano (maximo 5MB)
        $tamano_bytes = $_FILES['archivo']['size'];
        if ($tamano_bytes > 5242880) {
            echo json_encode(array('error' => true, 'msg' => 'La imagen supera el tamano maximo permitido de 5 MB'));
            return;
        }

        // Validar extension
        $nombre_original = $_FILES['archivo']['name'];
        $partes = explode('.', $nombre_original);
        $extension = strtolower(end($partes));
        $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'webp');

        if (!in_array($extension, $extensiones_permitidas)) {
            echo json_encode(array('error' => true, 'msg' => 'Formato no permitido. Solo se admiten imagenes JPG, PNG o WEBP'));
            return;
        }

        // Subir archivo usando el metodo de db_conect
        $subida = $this->db->SubirArchivo('archivo', 5000, 'imagen', 'fotos_perfil/');

        if (isset($subida['error']) && $subida['error'] == true) {
            echo json_encode(array('error' => true, 'msg' => $subida['mensaje']));
            return;
        }

        if (isset($subida['mensaje'])) {
            $ruta_foto = $subida['mensaje'];
        } else {
            $ruta_foto = '';
        }

        if ($ruta_foto === '') {
            echo json_encode(array('error' => true, 'msg' => 'Error al guardar la imagen en el servidor'));
            return;
        }

        // Actualizar en base de datos y sesion
        $this->db->update('persona', array('foto' => $ruta_foto), array('id' => $persona_id));
        $_SESSION['foto'] = $ruta_foto;

        echo json_encode(array(
            'error' => false,
            'msg'   => 'Foto de perfil actualizada con exito',
            'foto'  => $ruta_foto
        ));
    }

    // ============================================================
    // 4. LISTAR MIS DEPORTISTAS VINCULADOS
    // ============================================================

    public function listar_mis_deportistas()
    {
        $this->validar_token_simple();

        $persona_id = $this->obtener_persona_sesion_id();
        if ($persona_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Sesion de usuario no valida'));
            return;
        }

        // Consulta estricta: solo deportistas vinculados al usuario en sesion
        $sql = "SELECT d.id AS deportista_id, d.estado, d.eps, d.rh, d.alergias,
                       d.contacto_emergencia_nombre, d.contacto_emergencia_telefono,
                       c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                       p.id AS persona_id, p.tipo_documento, p.identificacion,
                       p.nombre1, p.nombre2, p.apellido1, p.apellido2,
                       p.fecha_nacimiento, p.genero, p.celular, p.correo, p.foto,
                       da.parentesco, da.es_principal
                FROM deportista d
                INNER JOIN persona p ON p.id = d.persona_id
                INNER JOIN deportista_acudiente da ON da.deportista_id = d.id
                LEFT JOIN categoria c ON c.id = d.categoria_id
                WHERE da.acudiente_id = $persona_id
                ORDER BY p.nombre1 ASC, p.apellido1 ASC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }

        echo json_encode(array(
            'error' => false,
            'data'  => $filas
        ));
    }

    // ============================================================
    // 5. OBTENER FICHA DEPORTIVA COMPLETA (TIPO CURRICULUM)
    // ============================================================

    public function obtener_ficha()
    {
        $this->validar_token_simple();

        $persona_id = $this->obtener_persona_sesion_id();
        if ($persona_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Sesion de usuario no valida'));
            return;
        }

        if (isset($_POST['deportista_id'])) {
            $deportista_id = intval($_POST['deportista_id']);
        } else {
            $deportista_id = 0;
        }

        if ($deportista_id <= 0) {
            echo json_encode(array('error' => true, 'msg' => 'Identificador de deportista no valido'));
            return;
        }

        // 1. Validar permiso estricto: el deportista debe estar vinculado al usuario
        $sql_vinculo = "SELECT id, parentesco, es_principal FROM deportista_acudiente
                        WHERE deportista_id = $deportista_id AND acudiente_id = $persona_id";
        $vinculo = $this->db->select_row($sql_vinculo);

        if (empty($vinculo)) {
            echo json_encode(array('error' => true, 'msg' => 'No tiene permiso para ver la ficha de este deportista'));
            return;
        }

        // 2. Obtener datos completos del deportista y categoria
        $sql_deportista = "SELECT d.id AS deportista_id, d.estado, d.eps, d.rh, d.alergias,
                                  d.contacto_emergencia_nombre, d.contacto_emergencia_telefono,
                                  d.fecha_afiliacion, d.observaciones,
                                  c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                                  c.descripcion AS categoria_descripcion,
                                  p.id AS persona_id, p.tipo_documento, p.identificacion,
                                  p.nombre1, p.nombre2, p.apellido1, p.apellido2,
                                  p.fecha_nacimiento, p.genero, p.celular, p.correo, p.direccion, p.foto
                           FROM deportista d
                           INNER JOIN persona p ON p.id = d.persona_id
                           LEFT JOIN categoria c ON c.id = d.categoria_id
                           WHERE d.id = $deportista_id";
        $deportista = $this->db->select_row($sql_deportista);

        if (empty($deportista)) {
            echo json_encode(array('error' => true, 'msg' => 'El deportista no existe'));
            return;
        }

        // 3. Obtener datos del acudiente consultante
        $sql_acudiente = "SELECT p.id, p.tipo_documento, p.identificacion,
                                 p.nombre1, p.nombre2, p.apellido1, p.apellido2,
                                 p.celular, p.correo, p.direccion
                          FROM persona p
                          WHERE p.id = $persona_id";
        $acudiente = $this->db->select_row($sql_acudiente);

        // 4. Obtener documentos cargados del deportista
        $sql_docs = "SELECT doc.id, doc.tipo_documento_id, doc.archivo, doc.archivo_original,
                            doc.estado, doc.observaciones, doc.fecha_subida,
                            td.nombre AS tipo_documento_nombre
                     FROM documento doc
                     INNER JOIN tipo_documento td ON td.id = doc.tipo_documento_id
                     WHERE doc.deportista_id = $deportista_id
                     ORDER BY td.id ASC";
        $documentos = $this->db->select_all($sql_docs);
        if (!is_array($documentos)) {
            $documentos = array();
        }

        echo json_encode(array(
            'error' => false,
            'data'  => array(
                'deportista' => $deportista,
                'vinculo'    => $vinculo,
                'acudiente'  => $acudiente,
                'documentos' => $documentos
            )
        ));
    }
}

// Despacho de la accion
$accion = ACCION;
$f = new Formulario();
$f->$accion();
