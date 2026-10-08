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
<!-- LIBRERIAS Y TOKENS DEL MODULO                               -->
<!-- ============================================================ -->
<!-- DataTables 2.x por CDN: el tema Velzon no trae esta version en local. -->
<!-- SweetAlert2 y Toastify NO se incluyen aqui: ya son globales -->
<!-- (cabeza.php y pie.php los cargan en local). -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<style type="text/css">
/* Tokens del modulo Afiliaciones: un solo lugar para los colores */
/* Los morados y semaforos son los mismos de Velzon (ver app.min.css: --vz-*) */
.afili-encabezado {
    background-color: rgb(194,69,139,0.04);
    color: #ffffff;
}
.afili-encabezado-oscuro {
    background-color: #1e1328; /* vino institucional Voley+ */
    color: #ffffff;
}
.afili-encabezado .card-title,
.afili-encabezado-oscuro .card-title,
.afili-encabezado .modal-title,
.afili-encabezado-oscuro .modal-title {
    color: rgb(194, 69, 139);
}
/* Botones de solo icono con area tactil minima de 44px (WCAG 2.5.8) */
.btn-afili-accion {
    min-width: 44px;
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
/* Campos marcados como invalidos en la validacion de envio */
.afili-invalido {
    border-color: var(--vz-danger);
}
.afili-error-texto {
    color: var(--vz-danger);
    font-size: 0.875em;
    margin-top: 0.25rem;
}
</style>

<!-- ============================================================ -->
<!-- SCRIPTS GLOBALES E INCLUSION DE UTILERIAS (HOISTING)         -->
<!-- ============================================================ -->
<?php include_once 'tabs/utilerias_js.php'; ?>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL Y NAVEGACION POR PESTAÑAS (TABS)        -->
<!-- ============================================================ -->
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <!-- Nav Tabs estilo Velzon (botones: no mueven el scroll como los enlaces) -->
                <ul class="nav nav-tabs mb-3" id="afiliacionTabs" role="tablist">
                    <?php if ($es_acudiente || $es_admin): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo !$es_admin ? 'active' : ''; ?>"
                                id="tab-registro-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-registro-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-registro-content"
                                aria-selected="<?php echo !$es_admin ? 'true' : 'false'; ?>">
                                <i class="ri-user-add-line me-1"></i> 1. Registro de Deportista
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-mis-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-mis-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-mis-content"
                                aria-selected="false">
                                <i class="ri-history-line me-1"></i> 2. Mis Solicitudes
                            </button>
                        </li>
                        <?php endif; ?>

                        <?php if ($es_admin): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="tab-gestion-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-gestion-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-gestion-content"
                                aria-selected="true">
                                <i class="ri-shield-user-line me-1"></i> 3. Administracion de Afiliaciones
                            </button>
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