<?php
// ============================================================
// POLITICA DE PRIVACIDAD — Vista publica y dentro del sistema
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - Cero emojis
//   - Rutas absolutas con WEB_ROOT
//   - Contenido dinamico desde tipo_autorizacion
//   - JS con backticks y funciones modulares
// ============================================================

global $db;

// 1. Consultar la politica vigente desde la base de datos
$sql_politica = "SELECT id, nombre, contenido, version FROM tipo_autorizacion WHERE slug = 'politica_privacidad' AND activo = 1 LIMIT 1";
$datos_politica = $db->select_row($sql_politica);

$titulo_politica = 'Política de Privacidad y Tratamiento de Datos Personales';
$contenido_politica = '<p>En mi calidad de padre, madre o representante legal del menor, autorizo al Club de Voleibol VOLEY+, en los
términos de la normativa colombiana aplicable en materia de protección de datos personales, para realizar la recolección, almacenamiento, uso,
actualización y demás tratamientos autorizados de los datos personales suministrados mediante este formulario, exclusivamente para las
finalidades relacionadas con:</P>
<ul><li>Inscripción y registro del deportista.</li>
<li>Administración de entrenamientos y actividades deportivas.</li>
<li>Gestión de competencias y eventos.</li>
<li>Comunicación con padres, madres y representantes legales.</li>
<li>Atención de situaciones de emergencia.</li>
<li>Cumplimiento de obligaciones legales o administrativas aplicables al club.</li>
• Gestión de seguros, afiliaciones o trámites deportivos, cuando corresponda.
Declaro haber sido informado(a) sobre los derechos que me asisten como titular de los datos y sobre los canales establecidos por el club para
ejercerlos.
Autorizo el tratamiento de datos personales:</p>';
$version_politica = '1.0';
$tipo_id_politica = 0;

if (is_array($datos_politica)) {
    if (isset($datos_politica['id'])) {
        $tipo_id_politica = intval($datos_politica['id']);
    }
    if (isset($datos_politica['nombre']) && $datos_politica['nombre'] !== '') {
        $titulo_politica = $datos_politica['nombre'];
    }
    if (isset($datos_politica['contenido']) && $datos_politica['contenido'] !== '') {
        $contenido_politica = $datos_politica['contenido'];
    }
    if (isset($datos_politica['version']) && $datos_politica['version'] !== '') {
        $version_politica = $datos_politica['version'];
    }
}

// 2. Verificar estado de sesion y si el usuario actual tiene la firma al dia
$usuario_autenticado = false;
$firma_vigente_registrada = false;
$fecha_firma_usuario = '';

if (function_exists('is_login')) {
    if (is_login()) {
        $usuario_autenticado = true;
        if (isset($_SESSION['persona_id'])) {
            $persona_id_logueado = intval($_SESSION['persona_id']);
            if ($persona_id_logueado > 0 && $tipo_id_politica > 0) {
                $sql_firma_check = "SELECT id, fecha_firma FROM autorizacion_firmada WHERE acudiente_id = " . $persona_id_logueado . " AND tipo_autorizacion_id = " . $tipo_id_politica . " AND version_firmada = '" . $db->escape_string($version_politica) . "' AND aceptada = 1 LIMIT 1";
                $firma_check = $db->select_row($sql_firma_check);
                if (is_array($firma_check)) {
                    if (isset($firma_check['id'])) {
                        $firma_vigente_registrada = true;
                        if (isset($firma_check['fecha_firma'])) {
                            $fecha_firma_usuario = $firma_check['fecha_firma'];
                        }
                    }
                }
            }
        }
    }
}
?>

<?php if (!$usuario_autenticado) : ?>
<!-- ============================================================ -->
<!-- VISTA PUBLICA (INVITADO SIN SESION)                          -->
<!-- ============================================================ -->
<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="voley" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title><?php echo htmlspecialchars($titulo_politica); ?> - Voley+</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Politica de Privacidad y Tratamiento de Datos del Club Voley+" name="description" />
    <meta content="Voley+" name="author" />

    <link rel="shortcut icon" href="<?php echo WEB_ROOT ?>img/favicon.png">

    <!-- Bootstrap Css -->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <style>
        .politica-caja-legal {
            background: #ffffff;
            border-radius: 8px;
            padding: 32px;
            line-height: 1.7;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }
        .politica-caja-legal h3, .politica-caja-legal h4, .politica-caja-legal h5 {
            color: #405189;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        .politica-caja-legal p, .politica-caja-legal li {
            color: #495057;
            font-size: 15px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Encabezado con logo -->
                <div class="text-center mb-4">
                    <a href="<?php echo WEB_ROOT ?>iniciar-sesion" class="d-inline-block">
                        <img src="<?php echo WEB_ROOT ?>img/logo-icon.png" alt="Logo Voley+" height="70">
                    </a>
                    <h1 class="fs-4 fw-bold mt-3 mb-1 text-primary">Voley+ Escuela Deportiva</h1>
                    <p class="text-muted">Compromiso con la privacidad y protección de datos</p>
                </div>

                <!-- Tarjeta principal de la politica -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-shield-check-line fs-4"></i>
                            <h2 class="fs-5 mb-0 text-white"><?php echo htmlspecialchars($titulo_politica); ?></h2>
                        </div>
                        <span class="badge bg-white text-primary fw-semibold">Versión <?php echo htmlspecialchars($version_politica); ?></span>
                    </div>

                        <div class="politica-caja-legal mb-4">
                            <?php echo $contenido_politica; ?>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="text-muted small">
                                <i class="ri-information-line me-1"></i> Esta política es de obligatorio cumplimiento para todos los miembros del club.
                            </div>
                            <a href="<?php echo WEB_ROOT ?>iniciar-sesion" class="btn btn-primary">
                                <i class="ri-arrow-left-line me-1"></i> Volver al inicio de sesión
                            </a>
                        </div>
                    <
                </div>

                <!-- Pie de pagina exterior -->
                <div class="text-center mt-4 text-muted small">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Voley+ — Sistema de Gestión Deportiva Integral.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php else : ?>
<!-- ============================================================ -->
<!-- VISTA DENTRO DEL SISTEMA (USUARIO AUTENTICADO)              -->
<!-- ============================================================ -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"><?php echo htmlspecialchars($titulo_politica); ?></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo WEB_ROOT ?>inicio">Inicio</a></li>
                    <li class="breadcrumb-item active">Política de Privacidad</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <?php if (!$firma_vigente_registrada) : ?>
        <!-- Aviso de consentimiento pendiente -->
        <div class="alert alert-warning border-0 d-flex align-items-center mb-3 shadow-sm p-3" role="alert">
            <i class="ri-alert-line fs-2 me-3 text-warning"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading fs-6 mb-1 fw-bold">Consentimiento requerido</h5>
                <p class="mb-0 small">Para continuar utilizando el sistema debes registrar tu aceptación a la versión vigente (<strong>Versión <?php echo htmlspecialchars($version_politica); ?></strong>).</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header border-0 bg-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2">
                        <i class="ri-shield-check-line fs-5 text-white"></i>
                    </span>
                    <div>
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($titulo_politica); ?></h5>
                        <small class="text-muted">Documento institucional vigente</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($firma_vigente_registrada) : ?>
                    <span class="badge bg-success text-white"><i class="ri-check-double-line me-1"></i> Aceptada (<?php echo htmlspecialchars($fecha_firma_usuario); ?>)</span>
                    <?php else : ?>
                    <span class="badge bg-warning text-dark"><i class="ri-time-line me-1"></i> Pendiente de firma</span>
                    <?php endif; ?>
                    <span class="badge bg-primary text-white fs-6">Versión <?php echo htmlspecialchars($version_politica); ?></span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="border rounded p-4 bg-light-subtle mb-4" style="max-height: 520px; overflow-y: auto;">
                    <?php echo $contenido_politica; ?>
                </div>

                <?php if (!$firma_vigente_registrada) : ?>
                <!-- Botones de accion para usuario con consentimiento pendiente -->
                <div class="p-3 border rounded bg-light mb-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="fw-semibold">¿Aceptas los términos y condiciones de tratamiento de datos?</div>
                        <small class="text-muted">Tu decisión quedará registrada con fecha, hora y firma electrónica para auditoría.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" id="btnAceptarPolVista" onclick="politicaAceptarDesdeVista()">
                            <i class="ri-check-line me-1"></i> Aceptar términos
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="btnRechazarPolVista" onclick="politicaRechazarDesdeVista()">
                            <i class="ri-close-line me-1"></i> Rechazar y salir
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                    <span class="text-muted small">
                        <i class="ri-lock-line me-1"></i> Tratamiento de datos bajo estándares de seguridad y confidencialidad.
                    </span>
                    <a href="<?php echo WEB_ROOT ?>inicio" class="btn btn-outline-primary btn-sm">
                        <i class="ri-home-4-line me-1"></i> Volver al panel principal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// Aceptar la politica desde la vista embebida
function politicaAceptarDesdeVista() {
    var boton_aceptar = document.getElementById('btnAceptarPolVista');
    if (boton_aceptar) {
        boton_aceptar.disabled = true;
    }

    fetch(web_root + 'iniciar-sesion/aceptar_politica', {
        method: 'POST'
    })
    .then(function(respuesta) {
        return respuesta.json();
    })
    .then(function(datos_resp) {
        if (datos_resp.error === false) {
            Swal.fire({
                icon: 'success',
                title: 'Consentimiento registrado',
                text: 'Gracias por aceptar los términos.',
                timer: 1400,
                showConfirmButton: false
            });
            setTimeout(function() {
                window.location.href = web_root + 'inicio';
            }, 1200);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos_resp.msg || 'No se pudo guardar la aceptación.'
            });
            if (boton_aceptar) {
                boton_aceptar.disabled = false;
            }
        }
    })
    .catch(function(error_red) {
        console.error(error_red);
        if (boton_aceptar) {
            boton_aceptar.disabled = false;
        }
    });
}

// Rechazar la politica y cerrar sesion
function politicaRechazarDesdeVista() {
    Swal.fire({
        title: '¿Rechazar política?',
        text: 'Si rechazas los términos se cerrará tu sesión y no podrás ingresar a la plataforma.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar y salir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#405189'
    }).then(function(resultado_swal) {
        if (resultado_swal.isConfirmed) {
            fetch(web_root + 'iniciar-sesion/rechazar_politica', {
                method: 'POST'
            })
            .then(function() {
                window.location.href = web_root + 'iniciar-sesion';
            })
            .catch(function() {
                window.location.href = web_root + 'iniciar-sesion';
            });
        }
    });
}
</script>
<?php endif; ?>
