<?php
// ============================================================
// SESION — Vista del formulario de login
//
// Se muestra cuando el usuario NO tiene sesion activa.
// Usa efectos2.js para cifrar el formulario con AES antes
// de enviarlo al backend.
// ============================================================
?>
<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Iniciar sesion - Voley+</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Voley+" name="description" />
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

    <!-- jQuery -->
    <script src="<?php echo WEB_ROOT ?>js/jquery_3.4.1_jquery.min.js"></script>
    <!-- CryptoJS AES -->
    <script src="<?php echo WEB_ROOT ?>js/heaven/rollups/aes.js"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/css/sweetalert2.css">
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/sweet-alert/sweetalert.min.js"></script>

    <script type="text/javascript">
        const menu = "<?php echo MENU ?>";
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
                                <a href="/voley/" class="d-inline-block auth-logo">
                                    <img src="<?php echo WEB_ROOT ?>img/logo-icon.png" alt="" height="100">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">Voley+</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4 card-bg-fill">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Iniciar sesion</h5>
                                    <p class="text-muted">Ingresar usuario y contraseña para iniciar</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <form class="needs-validation" novalidate id="formulario" method="POST" action="<?php echo WEB_ROOT ?>sesion/iniciar" autocomplete="off">
                                        <div class="mb-3">
                                            <label for="usuario" class="form-label">Usuario de ingreso<span class="text-danger">*</span></label>
                                            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese Usuario" required>
                                            <div class="invalid-feedback">Ingresar usuario</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="clave">Contraseña<span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5 password-input" name="clave" id="clave" placeholder="Ingrese Contraseña" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                <div class="invalid-feedback">Ingresar contraseña</div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Ingresar</button>
                                        </div>
                                    </form>
                                    <div id="cargar" style="display: none; text-align: center;">
                                        <img src="<?php echo WEB_ROOT ?>img/loader.gif">
                                    </div>
                                </div>
                            </div>
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
                            <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> Voley+</p>
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
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/plugins.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/particles.js/particles.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/pages/particles.app.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/pages/form-validation.init.js"></script>

    <!-- Login JS -->
    <script src="<?php echo WEB_ROOT ?>js/heaven/efectos2.js"></script>
</body>
</html>
