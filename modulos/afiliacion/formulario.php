<?php
/**
 * MÓDULO: Afiliaciones
 * Orquestador - Nav de pestañas + Includes
 * Incluye archivo de utilerias JS en la parte superior
 */

// Determinar rol del usuario - variables ya definidas en cabeza.php
$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);
$es_acudiente = ($usuario_rol === 3);
?>
<!-- ================ LIBRERIAS Y CSS GLOBAL ================ -->
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.1/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.1/js/buttons.dataTables.js"></script>

<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- ================ SCRIPTS HOISTED (ANTES DE INCLUDES) ================ -->
<script type="text/javascript">
// ===== VARIABLES GLOBALES DEL SISTEMA =====
// Variables ya definidas globalmente en cabeza.php: TOKEN_GLOBAL, page_root, MENU_SLUG
const ES_ADMIN = <?php echo $es_admin ? 'true' : 'false'; ?>;
const ES_ACUDIENTE = <?php echo $es_acudiente ? 'true' : 'false'; ?>;

// ===== UTILERÍAS GLOBALES DEL SISTEMA =====

// gcAjax - para peticiones AJAX con token
function gcAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: respuesta.msg });
                } else if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parsing response:', e);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar respuesta' });
            }
        } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error en el servidor' });
        }
    };

    xhr.onerror = function() {
        console.error('Network error');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion' });
    };

    var params = new URLSearchParams();
    for (var key in datos) {
        params.append(key, datos[key]);
    }
    xhr.send(params.toString());
}

// Utilidades de escape
function gcEsc(valor) {
    if (!valor) return '';
    return valor.toString()
        .replace(/&/g, '&')
        .replace(/</g, '<')
        .replace(/>/g, '>')
        .replace(/"/g, '"')
        .replace(/'/g, '&#039;');
}

// Debounce para busqueda en tiempo real
function gcDebounce(func, delay) {
    var timeout;
    return function() {
        var contexto = this;
        var args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(contexto, args);
        }, delay);
    };
}
</script>

<!-- ================ MODALES COMPARTIDOS ================ -->
<!-- Modal compartido genérico -->
<div class="modal fade" id="gcModalShared" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gcModalSharedTitle">Titulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="gcModalSharedBody">
                Contenido del modal...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ================ UTILERÍAS JS DEL MÓDULO (INCLUIDO POR HOISTING) ================ -->
<?php include_once 'tabs/utilerias_js.php'; ?>

<!-- ================ FIN DE SCRIPTS HOISTED ================ -->

<!-- ================ NAV DE PESTAÑAS ================ -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs border-tab border-0 mb-0 nav-secondary" id="afiliacionTabs" role="tablist">
                        <?php if ($es_admin): ?>
                        <li class="nav-item">
                            <a class="nav-link active nav-border txt-secondary"
                               id="afiliacion-gestion-tab"
                               data-bs-toggle="tab"
                               href="#afiliacion-gestion"
                               role="tab"
                               aria-controls="afiliacion-gestion"
                               aria-selected="true">
                                <i class="ri-user-add-line me-1"></i> Gestion
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if ($es_acudiente): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo !$es_admin ? 'active' : ''; ?> nav-border txt-secondary"
                               id="afiliacion-mis-tab"
                               data-bs-toggle="tab"
                               href="#afiliacion-mis"
                               role="tab"
                               aria-controls="afiliacion-mis"
                               aria-selected="<?php echo !$es_admin ? 'true' : 'false'; ?>">
                                <i class="ri-file-user-line me-1"></i> Mis Afiliaciones
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (!$es_admin && !$es_acudiente): ?>
                        <li class="nav-item">
                            <a class="nav-link active nav-border txt-secondary" href="#" tabindex="-1" aria-disabled="true">
                                <i class="ri-lock-line me-1"></i> Sin acceso
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content" id="afiliacionTabsContent">
                        <?php if ($es_admin): ?>
                        <div class="tab-pane fade show active" id="afiliacion-gestion" role="tabpanel" aria-labelledby="afiliacion-gestion-tab">
                            <div class="card-body px-0 pb-0">
                                <?php include_once 'tabs/gestion.php' ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($es_acudiente): ?>
                        <div class="tab-pane fade <?php echo !$es_admin ? 'show active' : ''; ?>" id="afiliacion-mis" role="tabpanel" aria-labelledby="afiliacion-mis-tab">
                            <div class="card-body px-0 pb-0">
                                <?php include_once 'tabs/mis_afiliaciones.php' ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!$es_admin && !$es_acudiente): ?>
                        <div class="tab-pane fade show active" id="afiliacion-no-acceso" role="tabpanel">
                            <div class="text-center py-5">
                                <i class="ri-lock-line display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">No tiene permisos para acceder a este modulo</h5>
                                <p class="text-muted">Contacte al administrador si cree que esto es un error.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================ CONFIGURACIÓN GLOBAL DE DATATABLES ================ -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    $.fn.dataTable.ext.errMode = 'throw';
});
</script>
