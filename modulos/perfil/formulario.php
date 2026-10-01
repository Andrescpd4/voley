<?php
/**
 * MODULO: Mi Perfil
 * Vista principal y orquestador del modulo de perfil de usuario.
 * 4 Pestanas:
 *   1. Datos Personales
 *   2. Cambiar Contrasena
 *   3. Actualizar Foto
 *   4. Mis Deportistas (con Ficha Deportiva / Curriculum)
 */

$persona_id = isset($_SESSION['persona_id']) ? intval($_SESSION['persona_id']) : 0;
$sql_persona = "SELECT * FROM persona WHERE id = $persona_id";
$persona = $db->select_row($sql_persona);

if (empty($persona)) {
    echo '<div class="alert alert-danger m-3">No se encontro la informacion del usuario.</div>';
    return;
}

$nombre_completo = trim($persona['nombre1'] . ' ' . $persona['nombre2'] . ' ' . $persona['apellido1'] . ' ' . $persona['apellido2']);
if ($nombre_completo === '') {
    $nombre_completo = 'Usuario';
}

$rol_nombre = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Usuario';
$foto_actual = !empty($persona['foto']) ? $persona['foto'] : 'img/user.png';
if (strpos($foto_actual, 'http') !== 0 && strpos($foto_actual, '/') !== 0) {
    $foto_url = WEB_ROOT . $foto_actual;
} else {
    $foto_url = $foto_actual;
}
?>

<!-- ============================================================ -->
<!-- ESTILOS VISUALES DEL MODULO DE PERFIL                        -->
<!-- ============================================================ -->
<style type="text/css">
/* Portada superior de perfil */
.perfil-portada {
    height: 180px;
    background: linear-gradient(135deg, #1e1328 0%, #3e1b4f 50%, #c2458b 100%);
    border-radius: 0.5rem 0.5rem 0 0;
    position: relative;
}

.perfil-avatar-contenedor {
    position: relative;
    margin-top: -65px;
    display: inline-block;
}

.perfil-avatar-img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border: 4px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background-color: #f8f9fa;
}

/* Tarjetas de deportistas vinculados */
.perfil-card-deportista {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ebec;
    border-radius: 0.5rem;
}
.perfil-card-deportista:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(30, 19, 40, 0.08);
}

.perfil-dep-avatar {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #c2458b;
}

/* Ficha deportiva tipo curriculum */
.perfil-ficha-cv {
    background-color: #ffffff;
    border: 1px solid #e9ebec;
    border-radius: 0.5rem;
    padding: 2.5rem;
    max-width: 900px;
    margin: 0 auto;
}

.perfil-ficha-header {
    border-bottom: 2px solid #1e1328;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}

.perfil-ficha-seccion {
    margin-bottom: 2rem;
}

.perfil-ficha-titulo-seccion {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e1328;
    border-bottom: 1px solid #e9ebec;
    padding-bottom: 0.4rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.perfil-ficha-item-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    text-transform: uppercase;
    margin-bottom: 0.2rem;
}

.perfil-ficha-item-valor {
    font-size: 0.95rem;
    color: #212529;
    font-weight: 500;
}

/* Estilos de impresion limpios para la ficha */
@media print {
    body * {
        visibility: hidden;
    }
    #perfilVistaFicha, #perfilVistaFicha * {
        visibility: visible;
    }
    #perfilVistaFicha {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .perfil-no-imprimir {
        display: none !important;
    }
    .perfil-ficha-cv {
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
}
</style>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL DEL PERFIL                              -->
<!-- ============================================================ -->
<div class="container-fluid">

    <!-- Tarjeta de Portada y Encabezado -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="perfil-portada"></div>
        <div class="card-body pt-0 px-4 pb-3">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3">
                <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-3 text-center text-sm-start">
                    <div class="perfil-avatar-contenedor">
                        <img src="<?php echo htmlspecialchars($foto_url); ?>" id="perfilAvatarSuperior" class="rounded-circle perfil-avatar-img" alt="Foto de perfil">
                    </div>
                    <div class="mb-2">
                        <h4 class="mb-1 fw-bold text-dark" id="perfilNombreCabecera"><?php echo htmlspecialchars($nombre_completo); ?></h4>
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                                <i class="ri-shield-user-line me-1"></i> <?php echo htmlspecialchars($rol_nombre); ?>
                            </span>
                            <span class="text-muted fs-13">
                                <i class="ri-id-card-line me-1"></i> <?php echo htmlspecialchars($persona['tipo_documento'] . ': ' . $persona['identificacion']); ?>
                            </span>
                            <?php if (!empty($persona['correo'])): ?>
                            <span class="text-muted fs-13">
                                <i class="ri-mail-line me-1"></i> <?php echo htmlspecialchars($persona['correo']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegacion por Pestanas y Contenido -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom-0 pb-0 pt-3">
            <ul class="nav nav-tabs nav-tabs-custom rounded card-header-tabs border-bottom-0" id="perfilNavTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-datos-link" data-bs-toggle="tab" data-bs-target="#tab-datos-panel" type="button" role="tab" aria-controls="tab-datos-panel" aria-selected="true">
                        <i class="ri-user-3-line me-1"></i> Datos Personales
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-clave-link" data-bs-toggle="tab" data-bs-target="#tab-clave-panel" type="button" role="tab" aria-controls="tab-clave-panel" aria-selected="false">
                        <i class="ri-lock-password-line me-1"></i> Cambiar Contraseña
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-foto-link" data-bs-toggle="tab" data-bs-target="#tab-foto-panel" type="button" role="tab" aria-controls="tab-foto-panel" aria-selected="false">
                        <i class="ri-image-edit-line me-1"></i> Actualizar Foto
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-deportistas-link" data-bs-toggle="tab" data-bs-target="#tab-deportistas-panel" type="button" role="tab" aria-controls="tab-deportistas-panel" aria-selected="false" onclick="perfilCargarDeportistas()">
                        <i class="ri-team-line me-1"></i> Mis Deportistas
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">

                <!-- ============================================================ -->
                <!-- PESTANA 1: DATOS PERSONALES                                  -->
                <!-- ============================================================ -->
                <div class="tab-pane fade show active" id="tab-datos-panel" role="tabpanel" aria-labelledby="tab-datos-link">
                    <form id="formPerfilDatos" onsubmit="perfilGuardarDatos(event)">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-0"><i class="ri-information-line me-1"></i> Informacion de Identificacion</h6>
                                <hr class="mt-2 mb-3">
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_tipo_doc" class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                <select class="form-select" id="perfil_tipo_doc" name="tipo_documento" required>
                                    <option value="CC" <?php echo ($persona['tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cedula de Ciudadania (CC)</option>
                                    <option value="TI" <?php echo ($persona['tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad (TI)</option>
                                    <option value="RC" <?php echo ($persona['tipo_documento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil (RC)</option>
                                    <option value="CE" <?php echo ($persona['tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cedula de Extranjeria (CE)</option>
                                    <option value="PASAPORTE" <?php echo ($persona['tipo_documento'] == 'PASAPORTE') ? 'selected' : ''; ?>>Pasaporte</option>
                                    <option value="OTRO" <?php echo ($persona['tipo_documento'] == 'OTRO') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_identificacion" class="form-label fw-semibold">Numero de Documento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="perfil_identificacion" name="identificacion" value="<?php echo htmlspecialchars($persona['identificacion']); ?>" required maxlength="20">
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_genero" class="form-label fw-semibold">Genero</label>
                                <select class="form-select" id="perfil_genero" name="genero">
                                    <option value="M" <?php echo ($persona['genero'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo ($persona['genero'] == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                    <option value="OTRO" <?php echo ($persona['genero'] == 'OTRO') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="text-primary fw-bold mb-0"><i class="ri-user-line me-1"></i> Nombres y Apellidos</h6>
                                <hr class="mt-2 mb-3">
                            </div>

                            <div class="col-md-3">
                                <label for="perfil_nombre1" class="form-label fw-semibold">Primer Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="perfil_nombre1" name="nombre1" value="<?php echo htmlspecialchars($persona['nombre1']); ?>" required maxlength="50">
                            </div>

                            <div class="col-md-3">
                                <label for="perfil_nombre2" class="form-label fw-semibold">Segundo Nombre</label>
                                <input type="text" class="form-control" id="perfil_nombre2" name="nombre2" value="<?php echo htmlspecialchars($persona['nombre2']); ?>" maxlength="50">
                            </div>

                            <div class="col-md-3">
                                <label for="perfil_apellido1" class="form-label fw-semibold">Primer Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="perfil_apellido1" name="apellido1" value="<?php echo htmlspecialchars($persona['apellido1']); ?>" required maxlength="50">
                            </div>

                            <div class="col-md-3">
                                <label for="perfil_apellido2" class="form-label fw-semibold">Segundo Apellido</label>
                                <input type="text" class="form-control" id="perfil_apellido2" name="apellido2" value="<?php echo htmlspecialchars($persona['apellido2']); ?>" maxlength="50">
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="text-primary fw-bold mb-0"><i class="ri-contacts-line me-1"></i> Contacto y Residencia</h6>
                                <hr class="mt-2 mb-3">
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_fecha_nac" class="form-label fw-semibold">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="perfil_fecha_nac" name="fecha_nacimiento" value="<?php echo htmlspecialchars($persona['fecha_nacimiento'] ?? ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_celular" class="form-label fw-semibold">Numero Celular</label>
                                <input type="text" class="form-control" id="perfil_celular" name="celular" value="<?php echo htmlspecialchars($persona['celular']); ?>" maxlength="20" placeholder="Ej: 3001234567">
                            </div>

                            <div class="col-md-4">
                                <label for="perfil_correo" class="form-label fw-semibold">Correo Electronico</label>
                                <input type="email" class="form-control" id="perfil_correo" name="correo" value="<?php echo htmlspecialchars($persona['correo']); ?>" maxlength="100" placeholder="correo@ejemplo.com">
                            </div>

                            <div class="col-12">
                                <label for="perfil_direccion" class="form-label fw-semibold">Direccion de Residencia</label>
                                <input type="text" class="form-control" id="perfil_direccion" name="direccion" value="<?php echo htmlspecialchars($persona['direccion'] ?? ''); ?>" maxlength="200" placeholder="Calle / Carrera / Barrio">
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" id="btnPerfilGuardarDatos" class="btn btn-primary px-4 py-2">
                                    <i class="ri-save-3-line me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ============================================================ -->
                <!-- PESTANA 2: CAMBIAR CONTRASENA                                -->
                <!-- ============================================================ -->
                <div class="tab-pane fade" id="tab-clave-panel" role="tabpanel" aria-labelledby="tab-clave-link">
                    <form id="formPerfilClave" onsubmit="perfilGuardarClave(event)" style="max-width: 600px;">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info border-0 mb-3">
                                    <i class="ri-shield-keyhole-line me-1"></i> Por seguridad, ingrese su contrasena actual para autorizar el cambio. La nueva clave debe tener minimo 5 caracteres.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="perfil_clave_actual" class="form-label fw-semibold">Contraseña Actual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="perfil_clave_actual" name="clave_actual" required maxlength="50" placeholder="Ingrese su clave actual">
                                    <button class="btn btn-outline-secondary" type="button" onclick="perfilToggleClave('perfil_clave_actual')">
                                        <i class="ri-eye-line" id="ico_perfil_clave_actual"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="perfil_clave_nueva" class="form-label fw-semibold">Nueva Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="perfil_clave_nueva" name="nueva_clave" required minlength="5" maxlength="50" placeholder="Minimo 5 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" onclick="perfilToggleClave('perfil_clave_nueva')">
                                        <i class="ri-eye-line" id="ico_perfil_clave_nueva"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="perfil_clave_confirmar" class="form-label fw-semibold">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="perfil_clave_confirmar" name="confirmar_clave" required minlength="5" maxlength="50" placeholder="Repita la nueva clave">
                                    <button class="btn btn-outline-secondary" type="button" onclick="perfilToggleClave('perfil_clave_confirmar')">
                                        <i class="ri-eye-line" id="ico_perfil_clave_confirmar"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" id="btnPerfilGuardarClave" class="btn btn-primary px-4 py-2">
                                    <i class="ri-lock-password-line me-1"></i> Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ============================================================ -->
                <!-- PESTANA 3: ACTUALIZAR FOTO                                   -->
                <!-- ============================================================ -->
                <div class="tab-pane fade" id="tab-foto-panel" role="tabpanel" aria-labelledby="tab-foto-link">
                    <form id="formPerfilFoto" onsubmit="perfilGuardarFoto(event)" style="max-width: 600px;">
                        <div class="text-center p-4 border rounded bg-light mb-3">
                            <div class="mb-3">
                                <img src="<?php echo htmlspecialchars($foto_url); ?>" id="perfilVistaPreviaFoto" class="rounded-circle avatar-xl img-thumbnail" style="width: 140px; height: 140px; object-fit: cover;" alt="Vista previa de foto">
                            </div>
                            <h6 class="text-dark fw-bold mb-1">Seleccionar Nueva Imagen</h6>
                            <p class="text-muted fs-13 mb-3">Formatos soportados: JPG, PNG o WEBP. Tamano maximo: 5 MB.</p>
                            
                            <input type="file" class="form-control mx-auto" id="perfil_archivo_foto" name="archivo" accept="image/jpeg,image/png,image/webp" style="max-width: 350px;" required onchange="perfilPrevisualizarFoto(event)">
                        </div>

                        <div class="text-end">
                            <button type="submit" id="btnPerfilGuardarFoto" class="btn btn-primary px-4 py-2">
                                <i class="ri-upload-2-line me-1"></i> Subir y Actualizar Foto
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================================ -->
                <!-- PESTANA 4: MIS DEPORTISTAS (FICHA Y TARJETAS)                -->
                <!-- ============================================================ -->
                <div class="tab-pane fade" id="tab-deportistas-panel" role="tabpanel" aria-labelledby="tab-deportistas-link">
                    
                    <!-- Vista 1: Grid de Tarjetas de Deportistas -->
                    <div id="perfilZonaListaDeportistas">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">Deportistas Vinculados</h5>
                                <p class="text-muted fs-13 mb-0">Listado de deportistas asociados a su cuenta como acudiente.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="perfilCargarDeportistas()">
                                <i class="ri-refresh-line me-1"></i> Refrescar
                            </button>
                        </div>

                        <div id="perfilContenedorTarjetas" class="row g-3">
                            <!-- Inyeccion dinamica via JS -->
                        </div>
                    </div>

                    <!-- Vista 2: Ficha Deportiva Completa (Curriculum) -->
                    <div id="perfilVistaFicha" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3 perfil-no-imprimir">
                            <button type="button" class="btn btn-outline-secondary" onclick="perfilCerrarFicha()">
                                <i class="ri-arrow-left-line me-1"></i> Volver a Mis Deportistas
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="perfilDescargarFicha()">
                                    <i class="ri-download-2-line me-1"></i> Descargar Ficha (HTML)
                                </button>
                                <button type="button" class="btn btn-primary" onclick="window.print()">
                                    <i class="ri-printer-line me-1"></i> Imprimir Ficha
                                </button>
                            </div>
                        </div>

                        <!-- Tarjeta del Curriculum Deportivo -->
                        <div class="perfil-ficha-cv" id="perfilContenidoFichaCV">
                            <!-- Inyeccion dinamica via JS -->
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- SCRIPTS Y LOGICA DEL MODULO (AGENTS.md §10–§11)             -->
<!-- ============================================================ -->
<script type="text/javascript">

// ------------------------------------------------------------
// 1. UTILIDAD AJAX CON TOKEN GLOBAL
// ------------------------------------------------------------
function perfilAjax(accion, datosExtra, alTerminar) {
    var formData = new FormData();
    if (datosExtra) {
        for (var clave in datosExtra) {
            formData.append(clave, datosExtra[clave]);
        }
    }

    $.ajax({
        url: page_root + accion,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
        },
        success: function(respuesta) {
            alTerminar(respuesta);
        },
        error: function() {
            Swal.fire({
                title: 'Error de Conexion',
                text: 'No fue posible comunicarse con el servidor',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

// ------------------------------------------------------------
// 2. ESCAPAR STRINGS CONTRA ATAQUES XSS
// ------------------------------------------------------------
function perfilEsc(texto) {
    if (texto === null || texto === undefined) {
        return '';
    }
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ------------------------------------------------------------
// 3. MOSTRAR / OCULTAR CONTRASENAS
// ------------------------------------------------------------
function perfilToggleClave(campoId) {
    var campo = document.getElementById(campoId);
    var icono = document.getElementById('ico_' + campoId);
    if (!campo) {
        return;
    }

    if (campo.type === 'password') {
        campo.type = 'text';
        if (icono) {
            icono.className = 'ri-eye-off-line';
        }
    } else {
        campo.type = 'password';
        if (icono) {
            icono.className = 'ri-eye-line';
        }
    }
}

// ------------------------------------------------------------
// 4. PREVISUALIZAR FOTO ANTES DE SUBIR
// ------------------------------------------------------------
function perfilPrevisualizarFoto(evento) {
    var input = evento.target;
    if (input.files && input.files[0]) {
        var archivo = input.files[0];
        if (archivo.size > 5242880) {
            Swal.fire({
                title: 'Archivo demasiado grande',
                text: 'La imagen seleccionada supera el limite de 5 MB',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            input.value = '';
            return;
        }

        var lector = new FileReader();
        lector.onload = function(e) {
            var img = document.getElementById('perfilVistaPreviaFoto');
            if (img) {
                img.src = e.target.result;
            }
        };
        lector.readAsDataURL(archivo);
    }
}

// ------------------------------------------------------------
// 5. GUARDAR DATOS PERSONALES
// ------------------------------------------------------------
function perfilGuardarDatos(evento) {
    evento.preventDefault();

    var boton = document.getElementById('btnPerfilGuardarDatos');
    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';
    }

    var form = document.getElementById('formPerfilDatos');
    var datos = {
        tipo_documento: form.tipo_documento.value,
        identificacion: form.identificacion.value,
        genero: form.genero.value,
        nombre1: form.nombre1.value,
        nombre2: form.nombre2.value,
        apellido1: form.apellido1.value,
        apellido2: form.apellido2.value,
        fecha_nacimiento: form.fecha_nacimiento.value,
        celular: form.celular.value,
        correo: form.correo.value,
        direccion: form.direccion.value
    };

    perfilAjax('aceptar', datos, function(respuesta) {
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="ri-save-3-line me-1"></i> Guardar Cambios';
        }

        if (respuesta.error) {
            Swal.fire({
                title: 'Atencion',
                text: respuesta.msg,
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        } else {
            Swal.fire({
                title: 'Exito',
                text: respuesta.msg,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });

            // Actualizar nombre visible en cabecera
            var nuevoNombre = (datos.nombre1 + ' ' + datos.apellido1).trim();
            var elNombre = document.getElementById('perfilNombreCabecera');
            if (elNombre) {
                elNombre.innerText = nuevoNombre;
            }
        }
    });
}

// ------------------------------------------------------------
// 6. GUARDAR NUEVA CONTRASENA
// ------------------------------------------------------------
function perfilGuardarClave(evento) {
    evento.preventDefault();

    var form = document.getElementById('formPerfilClave');
    var claveActual = form.clave_actual.value;
    var claveNueva = form.nueva_clave.value;
    var claveConfirmar = form.confirmar_clave.value;

    if (claveNueva.length < 5) {
        Swal.fire({
            title: 'Clave Invalida',
            text: 'La nueva contrasena debe tener al menos 5 caracteres',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    if (claveNueva !== claveConfirmar) {
        Swal.fire({
            title: 'No Coinciden',
            text: 'La confirmacion de la contrasena no coincide con la nueva clave',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    var boton = document.getElementById('btnPerfilGuardarClave');
    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Actualizando...';
    }

    var datos = {
        clave_actual: claveActual,
        nueva_clave: claveNueva,
        confirmar_clave: claveConfirmar
    };

    perfilAjax('cambiar_clave', datos, function(respuesta) {
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="ri-lock-password-line me-1"></i> Actualizar Contraseña';
        }

        if (respuesta.error) {
            Swal.fire({
                title: 'Error',
                text: respuesta.msg,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        } else {
            Swal.fire({
                title: 'Contraseña Actualizada',
                text: respuesta.msg,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
            form.reset();
        }
    });
}

// ------------------------------------------------------------
// 7. GUARDAR NUEVA FOTO DE PERFIL
// ------------------------------------------------------------
function perfilGuardarFoto(evento) {
    evento.preventDefault();

    var inputArchivo = document.getElementById('perfil_archivo_foto');
    if (!inputArchivo.files || !inputArchivo.files[0]) {
        Swal.fire({
            title: 'Archivo requerido',
            text: 'Seleccione una imagen antes de continuar',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    var boton = document.getElementById('btnPerfilGuardarFoto');
    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Subiendo...';
    }

    var formData = new FormData();
    formData.append('archivo', inputArchivo.files[0]);

    $.ajax({
        url: page_root + 'cambiar_foto',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
        },
        success: function(respuesta) {
            if (boton) {
                boton.disabled = false;
                boton.innerHTML = '<i class="ri-upload-2-line me-1"></i> Subir y Actualizar Foto';
            }

            if (respuesta.error) {
                Swal.fire({
                    title: 'Error',
                    text: respuesta.msg,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    title: 'Foto Actualizada',
                    text: respuesta.msg,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });

                // Actualizar foto en la vista superior
                if (respuesta.foto) {
                    var rutaFinal = respuesta.foto;
                    if (rutaFinal.indexOf('http') !== 0 && rutaFinal.indexOf('/') !== 0) {
                        rutaFinal = web_root + rutaFinal;
                    }
                    var imgSuperior = document.getElementById('perfilAvatarSuperior');
                    if (imgSuperior) {
                        imgSuperior.src = rutaFinal;
                    }
                }
            }
        },
        error: function() {
            if (boton) {
                boton.disabled = false;
                boton.innerHTML = '<i class="ri-upload-2-line me-1"></i> Subir y Actualizar Foto';
            }
            Swal.fire({
                title: 'Error',
                text: 'Error de comunicacion al subir la foto',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

// ------------------------------------------------------------
// 8. LISTAR MIS DEPORTISTAS VINCULADOS
// ------------------------------------------------------------
function perfilCargarDeportistas() {
    var contenedor = document.getElementById('perfilContenedorTarjetas');
    if (!contenedor) {
        return;
    }

    contenedor.innerHTML = '<div class="col-12 text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2">Cargando deportistas...</p></div>';

    perfilAjax('listar_mis_deportistas', {}, function(respuesta) {
        if (respuesta.error) {
            contenedor.innerHTML = `<div class="col-12"><div class="alert alert-danger">${perfilEsc(respuesta.msg)}</div></div>`;
            return;
        }

        var lista = respuesta.data;
        if (!lista || lista.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="avatar-lg mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-team-line fs-32 text-muted"></i>
                    </div>
                    <h5 class="text-dark fw-bold">No tienes deportistas vinculados</h5>
                    <p class="text-muted fs-13">Cuando registres o afilies a un deportista aparecera aqui con su ficha deportiva completa.</p>
                </div>
            `;
            return;
        }

        var htmlFinal = '';
        for (var i = 0; i < lista.length; i++) {
            var item = lista[i];
            htmlFinal = htmlFinal + perfilCrearTarjetaDeportista(item);
        }
        contenedor.innerHTML = htmlFinal;
    });
}

// ------------------------------------------------------------
// 9. CREAR TARJETA DE UN DEPORTISTA (HTML LIMPIO CON BACKTICKS)
// ------------------------------------------------------------
function perfilCrearTarjetaDeportista(dep) {
    var deportistaId = parseInt(dep.deportista_id, 10);
    var nombre = perfilEsc((dep.nombre1 + ' ' + dep.nombre2 + ' ' + dep.apellido1 + ' ' + dep.apellido2).trim());
    var documento = perfilEsc(dep.tipo_documento + ': ' + dep.identificacion);
    var categoria = perfilEsc(dep.categoria_nombre || 'Sin categoria');
    var parentesco = perfilEsc(dep.parentesco || 'Acudiente');
    var estado = dep.estado || 'borrador';

    var badgeEstado = '';
    if (estado === 'aprobado' || estado === 'activo') {
        badgeEstado = '<span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>';
    } else if (estado === 'pendiente_revision') {
        badgeEstado = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">En Revision</span>';
    } else {
        badgeEstado = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">' + perfilEsc(estado) + '</span>';
    }

    var fotoSrc = dep.foto ? dep.foto : 'img/user.png';
    if (fotoSrc.indexOf('http') !== 0 && fotoSrc.indexOf('/') !== 0) {
        fotoSrc = web_root + fotoSrc;
    }

    return `
        <div class="col-md-6 col-xl-4">
            <div class="card perfil-card-deportista h-100 shadow-none">
                <div class="card-body p-4 text-center">
                    <img src="${perfilEsc(fotoSrc)}" class="perfil-dep-avatar mb-3" alt="Foto de ${nombre}">
                    <h5 class="fw-bold text-dark mb-1 fs-15">${nombre}</h5>
                    <p class="text-muted fs-12 mb-2">${documento}</p>
                    
                    <div class="d-flex justify-content-center gap-1 mb-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">${categoria}</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle fs-11">${parentesco}</span>
                        ${badgeEstado}
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="perfilVerFicha(${deportistaId})">
                        <i class="ri-file-user-line me-1"></i> Ver Ficha Deportiva
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ------------------------------------------------------------
// 10. VER FICHA DEPORTIVA TIPO CURRICULUM
// ------------------------------------------------------------
function perfilVerFicha(deportistaId) {
    var zonaLista = document.getElementById('perfilZonaListaDeportistas');
    var zonaFicha = document.getElementById('perfilVistaFicha');
    var contenedorCV = document.getElementById('perfilContenidoFichaCV');

    if (!zonaFicha || !contenedorCV) {
        return;
    }

    zonaLista.style.display = 'none';
    zonaFicha.style.display = 'block';
    contenedorCV.innerHTML = '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2">Cargando ficha deportiva...</p></div>';

    perfilAjax('obtener_ficha', { deportista_id: deportistaId }, function(respuesta) {
        if (respuesta.error) {
            contenedorCV.innerHTML = `<div class="alert alert-danger">${perfilEsc(respuesta.msg)}</div>`;
            return;
        }

        var datos = respuesta.data;
        contenedorCV.innerHTML = perfilArmarHTMLFichaCV(datos);
    });
}

// ------------------------------------------------------------
// 11. CERRAR FICHA Y VOLVER A LISTA
// ------------------------------------------------------------
function perfilCerrarFicha() {
    var zonaLista = document.getElementById('perfilZonaListaDeportistas');
    var zonaFicha = document.getElementById('perfilVistaFicha');

    if (zonaLista && zonaFicha) {
        zonaFicha.style.display = 'none';
        zonaLista.style.display = 'block';
    }
}

// ------------------------------------------------------------
// 12. ARMAR EL HTML DEL CURRICULUM DEPORTIVO
// ------------------------------------------------------------
function perfilArmarHTMLFichaCV(datos) {
    var dep = datos.deportista;
    var acu = datos.acudiente;
    var vin = datos.vinculo;
    var docs = datos.documentos;

    var nombreCompleto = perfilEsc((dep.nombre1 + ' ' + (dep.nombre2 || '') + ' ' + dep.apellido1 + ' ' + (dep.apellido2 || '')).replace(/\s+/g, ' ').trim());
    var tipoYDoc = perfilEsc(dep.tipo_documento + ': ' + dep.identificacion);
    var categoria = perfilEsc(dep.categoria_nombre || 'Sin Categoria Asignada');
    var categoriaDesc = perfilEsc(dep.categoria_descripcion || '');
    var estado = perfilEsc(dep.estado || 'Activo');

    var fotoUrl = dep.foto ? dep.foto : 'img/user.png';
    if (fotoUrl.indexOf('http') !== 0 && fotoUrl.indexOf('/') !== 0) {
        fotoUrl = web_root + fotoUrl;
    }

    // Calcular edad si hay fecha de nacimiento
    var edadTexto = 'No registrada';
    if (dep.fecha_nacimiento) {
        var nac = new Date(dep.fecha_nacimiento);
        var hoy = new Date();
        var anios = hoy.getFullYear() - nac.getFullYear();
        var m = hoy.getMonth() - nac.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) {
            anios--;
        }
        edadTexto = anios + ' años (' + perfilEsc(dep.fecha_nacimiento) + ')';
    }

    var generoTexto = 'No especificado';
    if (dep.genero === 'M') {
        generoTexto = 'Masculino';
    } else if (dep.genero === 'F') {
        generoTexto = 'Femenino';
    } else if (dep.genero === 'OTRO') {
        generoTexto = 'Otro';
    }

    // Armar tabla de documentos cargados
    var filasDocsHTML = '';
    if (docs && docs.length > 0) {
        for (var i = 0; i < docs.length; i++) {
            var doc = docs[i];
            var docNombre = perfilEsc(doc.tipo_documento_nombre || 'Documento');
            var docEstado = perfilEsc(doc.estado || 'Cargado');
            var docFecha = perfilEsc(doc.fecha_subida ? doc.fecha_subida.substring(0, 10) : '-');
            
            filasDocsHTML += `
                <tr>
                    <td class="fw-semibold">${docNombre}</td>
                    <td><span class="badge bg-light text-dark border">${docEstado}</span></td>
                    <td class="text-muted fs-12">${docFecha}</td>
                </tr>
            `;
        }
    } else {
        filasDocsHTML = '<tr><td colspan="3" class="text-muted text-center py-2">No registra documentos adicionales</td></tr>';
    }

    var nombreAcudiente = perfilEsc((acu.nombre1 + ' ' + (acu.nombre2 || '') + ' ' + acu.apellido1 + ' ' + (acu.apellido2 || '')).replace(/\s+/g, ' ').trim());
    var parentescoTexto = perfilEsc(vin.parentesco || 'Acudiente');

    return `
        <!-- Encabezado de la Ficha / Curriculum -->
        <div class="perfil-ficha-header">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 text-center text-sm-start">
                    <img src="${perfilEsc(fotoUrl)}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #1e1328;" alt="Foto de ${nombreCompleto}">
                    <div>
                        <h3 class="fw-bold text-dark mb-1 fs-20">${nombreCompleto}</h3>
                        <p class="text-muted fs-14 mb-1"><i class="ri-id-card-line me-1"></i> ${tipoYDoc}</p>
                        <span class="badge bg-primary text-white fs-12 px-2 py-1"><i class="ri-trophy-line me-1"></i> ${categoria}</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-12 text-muted text-uppercase fw-bold">Club Deportivo Voley+</div>
                    <div class="fs-13 fw-semibold text-dark">Ficha Integral de Deportista</div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle mt-1">${estado}</span>
                </div>
            </div>
        </div>

        <!-- Seccion 1: Informacion Personal y Contacto -->
        <div class="perfil-ficha-seccion">
            <div class="perfil-ficha-titulo-seccion">
                <i class="ri-user-star-line text-primary"></i> 1. Informacion Personal
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">Edad y Nacimiento</div>
                    <div class="perfil-ficha-item-valor">${edadTexto}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">Genero</div>
                    <div class="perfil-ficha-item-valor">${generoTexto}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">Celular</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.celular || 'No registrado')}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">Correo</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.correo || 'No registrado')}</div>
                </div>
                <div class="col-12">
                    <div class="perfil-ficha-item-label">Direccion de Residencia</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.direccion || 'No registrada')}</div>
                </div>
            </div>
        </div>

        <!-- Seccion 2: Informacion Medica y Emergencias -->
        <div class="perfil-ficha-seccion">
            <div class="perfil-ficha-titulo-seccion">
                <i class="ri-heart-pulse-line text-danger"></i> 2. Ficha Medica y Emergencias
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">EPS / SISBEN</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.eps || 'No registrada')}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="perfil-ficha-item-label">Grupo Sanguineo (RH)</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.rh || 'No registrado')}</div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="perfil-ficha-item-label">Alergias o Condiciones Especiales</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.alergias || 'Ninguna reportada')}</div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="perfil-ficha-item-label">Contacto de Emergencia</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.contacto_emergencia_nombre || 'No registrado')}</div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="perfil-ficha-item-label">Telefono de Emergencia</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(dep.contacto_emergencia_telefono || 'No registrado')}</div>
                </div>
            </div>
        </div>

        <!-- Seccion 3: Datos del Acudiente Responsable -->
        <div class="perfil-ficha-seccion">
            <div class="perfil-ficha-titulo-seccion">
                <i class="ri-parent-line text-info"></i> 3. Acudiente Responsable
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-md-4">
                    <div class="perfil-ficha-item-label">Nombre del Acudiente</div>
                    <div class="perfil-ficha-item-valor">${nombreAcudiente}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="perfil-ficha-item-label">Parentesco</div>
                    <div class="perfil-ficha-item-valor">${parentescoTexto}</div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="perfil-ficha-item-label">Documento</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(acu.tipo_documento + ': ' + acu.identificacion)}</div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="perfil-ficha-item-label">Celular de Contacto</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(acu.celular || 'No registrado')}</div>
                </div>
                <div class="col-sm-6 col-md-6">
                    <div class="perfil-ficha-item-label">Correo Electronico</div>
                    <div class="perfil-ficha-item-valor">${perfilEsc(acu.correo || 'No registrado')}</div>
                </div>
            </div>
        </div>

        <!-- Seccion 4: Documentos y Soportes -->
        <div class="perfil-ficha-seccion mb-0">
            <div class="perfil-ficha-titulo-seccion">
                <i class="ri-file-list-3-line text-warning"></i> 4. Documentacion y Soportes
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Documento</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasDocsHTML}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

// ------------------------------------------------------------
// 13. DESCARGAR FICHA DEPORTIVA COMO HTML
// ------------------------------------------------------------
function perfilDescargarFicha() {
    var contenedor = document.getElementById('perfilContenidoFichaCV');
    if (!contenedor) {
        return;
    }

    var contenido = `
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Deportiva - Voley+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; padding: 2rem; }
        .perfil-ficha-cv { background: #fff; padding: 2.5rem; border: 1px solid #dee2e6; border-radius: .5rem; max-width: 850px; margin: 0 auto; }
        .perfil-ficha-header { border-bottom: 2px solid #1e1328; padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
        .perfil-ficha-seccion { margin-bottom: 2rem; }
        .perfil-ficha-titulo-seccion { font-size: 1.05rem; font-weight: 700; color: #1e1328; border-bottom: 1px solid #e9ebec; padding-bottom: .4rem; margin-bottom: 1rem; }
        .perfil-ficha-item-label { font-weight: 600; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; margin-bottom: .2rem; }
        .perfil-ficha-item-valor { font-size: .95rem; color: #212529; font-weight: 500; }
    </style>
</head>
<body>
    <div class="perfil-ficha-cv">
        ${contenedor.innerHTML}
    </div>
</body>
</html>
    `;

    var blob = new Blob([contenido], { type: 'text/html;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Ficha_Deportiva_VoleyPlus.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

</script>
