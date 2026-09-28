<?php
/**
 * MODULO: Afiliaciones Voley+
 * Orquestador de 3 Pestañas:
 * 1. Registro de Deportista (Acudiente / Admin)
 * 2. Mis Solicitudes (Acudiente / Admin)
 * 3. Administracion de Afiliaciones (Solo Admin Roles 1, 4)
 */

// 1. Determinar el rol del usuario en sesion (desde usuario_rol)
$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);
$es_acudiente = ($usuario_rol === 3);

// Si no tiene ningun rol valido, denegar
if (!$es_admin && !$es_acudiente) {
    echo '<div class="alert alert-danger m-3">No tiene permisos para acceder al modulo de Afiliaciones.</div>';
    return;
}
?>

<!-- ============================================================ -->
<!-- LIBRERIAS Y CSS GLOBAL DE VELZON                            -->
<!-- ============================================================ -->
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- ============================================================ -->
<!-- SCRIPTS GLOBALES E INCLUSION DE UTILERIAS (HOISTING)         -->
<!-- ============================================================ -->
<?php include_once 'tabs/utilerias_js.php'; ?>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL Y NAVEGACION POR PESTAÑAS (TABS)        -->
<!-- ============================================================ -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav Tabs estilo lavado_cubetas / Velzon -->
                    <ul class="nav nav-tabs border-tab border-0 mb-3 nav-secondary" id="afiliacionTabs" role="tablist">
                        <?php if ($es_acudiente || $es_admin): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo !$es_admin ? 'active' : ''; ?> nav-border pt-0 txt-secondary nav-secondary"
                               id="tab-registro-link"
                               data-bs-toggle="tab"
                               href="#tab-registro-content"
                               role="tab"
                               aria-controls="tab-registro-content"
                               aria-selected="<?php echo !$es_admin ? 'true' : 'false'; ?>">
                                <i class="ri-user-add-line me-1"></i> 1. Registro de Deportista
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link nav-border pt-0 txt-secondary nav-secondary"
                               id="tab-mis-link"
                               data-bs-toggle="tab"
                               href="#tab-mis-content"
                               role="tab"
                               aria-controls="tab-mis-content"
                               aria-selected="false">
                                <i class="ri-history-line me-1"></i> 2. Mis Solicitudes
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if ($es_admin): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active nav-border pt-0 txt-secondary nav-secondary"
                               id="tab-gestion-link"
                               data-bs-toggle="tab"
                               href="#tab-gestion-content"
                               role="tab"
                               aria-controls="tab-gestion-content"
                               aria-selected="true">
                                <i class="ri-shield-user-line me-1"></i> 3. Administracion de Afiliaciones
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content" id="afiliacionTabsContent">
                        <?php if ($es_acudiente || $es_admin): ?>
                        <!-- TAB 1: REGISTRO DE DEPORTISTA -->
                        <div class="tab-pane fade <?php echo !$es_admin ? 'show active' : ''; ?>"
                             id="tab-registro-content"
                             role="tabpanel"
                             aria-labelledby="tab-registro-link">
                            <?php include_once 'tabs/registro.php'; ?>
                        </div>

                        <!-- TAB 2: MIS SOLICITUDES -->
                        <div class="tab-pane fade"
                             id="tab-mis-content"
                             role="tabpanel"
                             aria-labelledby="tab-mis-link">
                            <?php include_once 'tabs/mis_solicitudes.php'; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($es_admin): ?>
                        <!-- TAB 3: ADMINISTRACION -->
                        <div class="tab-pane fade show active"
                             id="tab-gestion-content"
                             role="tabpanel"
                             aria-labelledby="tab-gestion-link">
                            <?php include_once 'tabs/gestion.php'; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- CONFIGURACION GENERAL AL CARGAR EL DOCUMENTO                 -->
<!-- ============================================================ -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Evitar que DataTables lance alertas modales intrusivas en caso de error
    if ($.fn.dataTable && $.fn.dataTable.ext) {
        $.fn.dataTable.ext.errMode = 'throw';
    }
});
</script>