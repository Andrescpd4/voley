<?php
// ============================================================
// SESION — Vista del formulario de login
//
// Formulario de inicio de sesión de Voley+.
// Envía POST a inicar-sesion/iniciar mediante fetch()
// Guarda el token devuelto en localStorage('stp_k_l_t')
// ============================================================
?>
<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="voley" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <title>Iniciar Sesión - Voley+</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Gestión de Escuela de Voleibol" name="description" />
    <meta content="Voley+" name="author" />

    <link rel="shortcut icon" href="<?php echo WEB_ROOT ?>img/favicon.png">

    <!-- Layout config Js -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?php echo WEB_ROOT ?>plantilla/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/css/sweetalert2.css">
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/sweet-alert/sweetalert.min.js"></script>

    <script type="text/javascript">
        const web_root = "<?php echo WEB_ROOT ?>";
        const page_root = "<?php echo PAGE_ROOT ?>";
    </script>
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="<?php echo WEB_ROOT ?>" class="d-inline-block auth-logo">
                                    <img src="<?php echo WEB_ROOT ?>img/logo-icon.png" alt="Logo" height="80">
                                </a>
                            </div>
                            <p class="mt-3 fs-16 fw-medium text-white">Voley+ Escuela Deportiva</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-2 card-bg-fill">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">¡Bienvenido!</h5>
                                    <p class="text-muted">Ingresa tus credenciales para acceder al sistema</p>
                                </div>
                                <div class="p-2 mt-3">
                                    <form id="formLogin" autocomplete="off">
                                        <div class="mb-3">
                                            <label for="usuario" class="form-label">Usuario o Documento <span class="text-danger">*</span></label>
                                            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ej: ADMIN o 10000001" required autofocus>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="clave">Contraseña <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5" name="clave" id="clave" placeholder="Ingresa tu contraseña" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="btnToggleClave">
                                                    <i class="ri-eye-fill align-middle" id="iconoClave"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-primary w-100" type="submit" id="btnIngresar">
                                                <span id="btnTexto"><i class="ri-login-box-line me-1"></i> Ingresar</span>
                                                <span id="btnCargando" style="display: none;">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                    Verificando...
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="mb-0 text-muted">¿Problemas para acceder? Contacta a la administración.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> Voley+ — Sistema de Gestión Deportiva</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/feather-icons/feather.min.js"></script>

    <script>
        // 1. Alternar visibilidad de contraseña
        var btnToggle = document.getElementById('btnToggleClave');
        var inputClave = document.getElementById('clave');
        var iconoClave = document.getElementById('iconoClave');

        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                if (inputClave.type === 'password') {
                    inputClave.type = 'text';
                    iconoClave.className = 'ri-eye-off-fill align-middle';
                } else {
                    inputClave.type = 'password';
                    iconoClave.className = 'ri-eye-fill align-middle';
                }
            });
        }

        // 2. Enviar formulario de login vía AJAX
        var formLogin = document.getElementById('formLogin');
        var btnIngresar = document.getElementById('btnIngresar');
        var btnTexto = document.getElementById('btnTexto');
        var btnCargando = document.getElementById('btnCargando');

        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();

            var usuario = document.getElementById('usuario').value.trim();
            var clave = document.getElementById('clave').value;

            if (usuario === '' || clave === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos obligatorios',
                    text: 'Por favor ingresa tu usuario y contraseña'
                });
                return;
            }

            // Mostrar estado de carga
            btnIngresar.disabled = true;
            btnTexto.style.display = 'none';
            btnCargando.style.display = 'inline-block';

            var formData = new FormData();
            formData.append('usuario', usuario);
            formData.append('clave', clave);

            fetch(web_root + 'iniciar-sesion/iniciar', {
                method: 'POST',
                body: formData
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (data.error === false) {
                    // Guardar JWT en localStorage
                    if (data.token) {
                        localStorage.setItem('stp_k_l_t', data.token);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Ingreso Exitoso!',
                        text: data.msg || 'Bienvenido al sistema',
                        timer: 1200,
                        showConfirmButton: false
                    });

                    setTimeout(function() {
                        window.location.href = data.redirect || (web_root + 'inicio');
                    }, 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso Denegado',
                        text: data.msg || 'Credenciales incorrectas'
                    });
                    btnIngresar.disabled = false;
                    btnTexto.style.display = 'inline-block';
                    btnCargando.style.display = 'none';
                }
            })
            .catch(function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo contactar con el servidor. Verifica tu conexión.'
                });
                btnIngresar.disabled = false;
                btnTexto.style.display = 'inline-block';
                btnCargando.style.display = 'none';
            });
        });
    </script>
</body>
</html>
