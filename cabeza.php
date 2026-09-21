<?php
// ============================================================
// CABEZA.PHP — Head global del sistema
//
// Se incluye en TODAS las paginas logueadas.
// Contiene:
//   - Meta tags y CSS del tema Velzon
//   - Topbar con foto de perfil, notificaciones, menu usuario
//   - Sidebar con menu dinamico (menu.php)
//   - Constantes JS (WEB_ROOT, PAGE_ROOT, TOKEN_GLOBAL)
//   - Scripts globales de Bootstrap, jQuery, etc.
// ============================================================
?>
<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title><?php echo RUTA_MENU ?? 'Voley+' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Voley+" name="description" />
    <meta content="Voley+" name="author" />

    <link rel="shortcut icon" href="<?php echo WEB_ROOT ?>img/favicon.png">
    <link rel="manifest" href="<?php echo WEB_ROOT ?>manifest.json">

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
    <!-- SweetAlert2 -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/css/sweetalert2.css">
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/sweet-alert/sweetalert.min.js"></script>
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/libs/toastify-js/src/toastify.css">
    <!-- Choices.js -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/libs/choices.js/public/assets/styles/choices.min.css">
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- flatpickr -->
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT ?>plantilla/assets/libs/flatpickr/flatpickr.min.css">
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/flatpickr/flatpickr.min.js"></script>

    <script type="text/javascript">
        const BASE_URL = '<?php echo WEB_ROOT ?>inicio/';
        const menu = "<?php echo MENU ?>";
        const web_root = "<?php echo WEB_ROOT ?>";
        const page_root = "<?php echo PAGE_ROOT ?>";
        let TOKEN_GLOBAL = localStorage.getItem('stp_k_l_t') || '';
    </script>
    <?php
        include_once 'script_lia.php';
        include_once 'style_lia.php';
    ?>
</head>

<body>
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="<?php echo WEB_ROOT ?>" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="<?php echo WEB_ROOT ?>img/logo-icon.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?php echo WEB_ROOT ?>img/logo-icon.png" alt="" height="17">
                                </span>
                            </a>
                        </div>
                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="ms-1 header-item d-flex">
                            <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle position-relative" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class='bx bx-bell fs-22'></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fs-16 fw-semibold">Notificaciones</h6>
                                </div>
                                <div class="list-group list-group-flush" style="max-height: 320px; overflow-y: auto;"></div>
                                <div class="p-2 text-center border-top">
                                    <small class="text-muted">Sin notificaciones</small>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <?php
                                    $foto_perfil = '';
                                    if (isset($_SESSION['foto'])) {
                                        if (strpos($_SESSION['foto'], 'http') === 0) {
                                            $foto_perfil = $_SESSION['foto'];
                                        } else {
                                            $foto_perfil = WEB_ROOT . $_SESSION['foto'];
                                        }
                                    }
                                    if ($foto_perfil == '') {
                                        $foto_perfil = WEB_ROOT . 'img/user.png';
                                    }
                                    ?>
                                    <img class="rounded-circle header-profile-user" src="<?php echo $foto_perfil ?>" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo $_SESSION['nombre_usuario'] ?? 'Usuario' ?></span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"><?php echo $_SESSION['rol'] ?? '' ?></span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Hola! <?php echo $_SESSION['nombre_usuario'] ?? 'Usuario' ?></h6>
                                <a class="dropdown-item" href="<?php echo WEB_ROOT ?>perfil"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Perfil</span></a>
                                <a class="dropdown-item" href="<?php echo WEB_ROOT ?>sesion/cerrar_sesion"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Salir</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== App Menu (Sidebar) ========== -->
        <div class="app-menu navbar-menu">
            <div class="navbar-brand-box">
                <a href="<?php echo WEB_ROOT ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?php echo WEB_ROOT ?>img/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo WEB_ROOT ?>img/logo-sm.png" alt="" height="57">
                    </span>
                </a>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <?php
                        // Incluir generador de menu
                        include_once 'menu.php';
                        generarMenu("");
                        ?>
                    </ul>
                </div>
            </div>
            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0"><?php echo RUTA_MENU ?></h4>
                                <div class="page-title-right" id="ruta_global"></div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
