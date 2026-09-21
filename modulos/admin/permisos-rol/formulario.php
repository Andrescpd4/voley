<style>
    .gcp-rol-card {
        border: 1px solid var(--vz-border-color);
        border-radius: 8px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 8px;
    }
    .gcp-rol-card:hover { background: var(--vz-light); }
    .gcp-rol-card.active {
        border-color: var(--vz-primary);
        background: rgba(64,81,137,0.08);
    }
    .gcp-module-item {
        border: 1px solid var(--vz-border-color);
        border-radius: 6px;
        margin-bottom: 8px;
        overflow: hidden;
    }
    .gcp-module-header {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        background: var(--vz-light);
        border-bottom: 1px solid var(--vz-border-color);
        cursor: pointer;
    }
    .gcp-module-header:hover { background: var(--vz-lighter); }
    .gcp-module-checkbox {
        width: 16px;
        height: 16px;
        margin-right: 8px;
    }
    .gcp-module-icon {
        font-size: 1.2rem;
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }
    .gcp-module-name {
        flex: 1;
        font-weight: 600;
    }
    .gcp-module-indicator {
        position: absolute;
        right: 12px;
        font-size: 0.75rem;
        color: #6c757d;
    }
    .gcp-actions-container {
        padding: 0 12px 12px 12px;
    }
    .gcp-action-item {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        border-top: 1px solid var(--vz-border-color);
    }
    .gcp-action-item:first-child { border-top: none; }
    .gcp-action-checkbox {
        width: 14px;
        height: 14px;
        margin-right: 8px;
    }
    .gcp-action-label {
        flex: 1;
        font-size: 0.9rem;
        cursor: pointer;
    }
    .gcp-action-label:hover { color: var(--vz-primary); }
    .gcp-action-disabled {
        opacity: 0.4;
        pointer-events: none;
    }
    .gcp-action-disabled .gcp-action-label { color: #6c757d; }
    .gcp-hint {
        font-size: 0.85rem;
        color: #878a99;
        padding: 8px 12px;
        border-bottom: 1px solid var(--vz-border-color);
        background: var(--vz-lighter);
    }
    .gcp-empty-state {
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }
    .gcp-empty-state i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #adb5bd;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <!-- COLUMNA IZQUIERDA: Lista de Roles -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="ri-shield-user-line me-1"></i>Roles del Sistema</h6>
                    <div id="gcpListaRoles" class="mt-3">
                        <div class="text-center"><span class="spinner-border spinner-border-sm text-primary"></span> Cargando...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Árbol integrado de permisos -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title" id="gcpTituloPermisos"><i class="ri-menu-2-line me-1"></i>Seleccione un rol para ver sus permisos</h6>
                    <div id="gcpPermisosContainer" class="mt-3">
                        <div class="gcp-empty-state">
                            <i class="ri-user-settings-line"></i>
                            <p>Seleccione un rol para configurar sus permisos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// ============================================================
// VARIABLES GLOBALES
// ============================================================
var gcpDatos = { roles:[], menus:[], acciones:[], permisos_menu:[], permisos_accion:[], usuario_rol:0 };
var gcpRolActual = 0;

// ============================================================
// FUNCION PARA MOSTRAR MENSAJES (Toastify o fallback a alert)
// ============================================================
function gcpMsg(texto, titulo, tipo) {
    if (typeof Toastify !== 'undefined') {
        var color = '#405189';
        if (tipo == 'success') color = '#0ab39c';
        if (tipo == 'error') color = '#f06548';
        if (tipo == 'warning') color = '#f7b84b';
        Toastify({
            text: texto,
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: { background: color }
        }).showToast();
    } else {
        alert(texto);
    }
}

// ============================================================
// ESCAPE DE TEXTO PARA EVITAR XSS
// ============================================================
function gcpEsc(s) {
    if (!s) return '';
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

// ============================================================
// NOMBRE LEGIBLE PARA ACCIONES (mejorado para nombres arbitrarios)
// ============================================================
function gcpNombreAccion(accion) {
    if (!accion) return '';
    
    // Convertir a string si no lo es
    accion = String(accion);
    
    // Remover guiones bajos iniciales y finales
    accion = accion.replace(/^_+|_+$/g, '');
    
    // Reemplazar guiones bajos con espacios
    accion = accion.replace(/_/g, ' ');
    
    // Capitalizar primera letra de cada palabra
    accion = accion.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    
    // Mapeos especiales para mejorar legibilidad
    var reemplazos = {
        'Id': 'ID',
        'Url': 'URL',
        'Xml': 'XML',
        'Json': 'JSON',
        'Api': 'API',
        'Html': 'HTML',
        'Css': 'CSS',
        'Pdf': 'PDF',
        'Zip': 'ZIP',
        'Csv': 'CSV'
    };
    
    for (var clave in reemplazos) {
        var regex = new RegExp('\\b' + clave + '\\b', 'g');
        accion = accion.replace(regex, reemplazos[clave]);
    }
    
    return accion;
}

// ============================================================
// 1. CARGAR DATOS INICIALES
// ============================================================
function gcpCargarDatos() {
    $.ajax({
        url: page_root + 'listar',
        type: 'GET',
        dataType: 'json',
        headers: { 'Authorization': TOKEN_GLOBAL },
        success: function(r) {
            if (r.error) {
                gcpMsg(r.msg, 'Error', 'error');
                return;
            }
            gcpDatos = r;
            gcpRenderizarRoles();
        },
        error: function() {
            gcpMsg('Error al cargar los permisos', 'Error', 'error');
        }
    });
}

// ============================================================
// 2. RENDERIZAR LISTA DE ROLES
// ============================================================
function gcpRenderizarRoles() {
    var c = document.getElementById('gcpListaRoles');
    var h = '';
    for (var i = 0; i < gcpDatos.roles.length; i++) {
        var rol = gcpDatos.roles[i];
        var badge = '';
        if (rol.nivel >= 90) badge = ' <span class="badge bg-danger-subtle text-danger">Super Admin</span>';
        h += '<div class="gcp-rol-card" onclick="gcpSeleccionarRol(' + rol.id + ')" id="gcpRol' + rol.id + '">';
        h += '<div class="fw-semibold">' + gcpEsc(rol.nombre) + badge + '</div>';
        h += '<small class="text-muted">Nivel: ' + gcpEsc(String(rol.nivel || 0)) + '</small>';
        h += '</div>';
    }
    c.innerHTML = h;
}

// ============================================================
// 3. SELECCIONAR UN ROL Y MOSTRAR SUS PERMISOS
// ============================================================
function gcpSeleccionarRol(id) {
    console.log('[GCP] Seleccionando rol:', id);
    gcpRolActual = id;

    // Marcar visualmente la tarjeta activa
    var cards = document.querySelectorAll('.gcp-rol-card');
    for (var i = 0; i < cards.length; i++) {
        cards[i].classList.remove('active');
    }
    document.getElementById('gcpRol' + id).classList.add('active');

    // Buscar el nombre del rol seleccionado
    var rol = null;
    for (var i = 0; i < gcpDatos.roles.length; i++) {
        if (gcpDatos.roles[i].id == id) {
            rol = gcpDatos.roles[i];
            break;
        }
    }

    // Actualizar titulo
    document.getElementById('gcpTituloPermisos').innerHTML = '<i class="ri-menu-2-line me-1"></i>Permisos: ' + (rol ? gcpEsc(rol.nombre) : '');

    // Renderizar permisos del rol
    console.log('[GCP] Llamando a gcpRenderizarPermisosIntegrado');
    gcpRenderizarPermisosIntegrado(id);
    console.log('[GCP] Renderizado completado');
}

// ============================================================
// 4. RENDERIZAR ÁRBOL INTEGRADO DE PERMISOS
// ============================================================
function gcpRenderizarPermisosIntegrado(rolId) {
    console.log('[GCP] gcpRenderizarPermisosIntegrado iniciado para rol:', rolId);
    var c = document.getElementById('gcpPermisosContainer');
    console.log('[GCP] Contenedor encontrado:', c);

    if (gcpDatos.menus.length === 0) {
        console.log('[GCP] No hay menus en gcpDatos');
        c.innerHTML = '<div class="gcp-empty-state"><i class="ri-folder-line"></i><p>No hay módulos configurados</p></div>';
        return;
    }
    console.log('[GCP] Menus disponibles:', gcpDatos.menus.length);
    console.log('[GCP] Acciones disponibles:', gcpDatos.acciones.length);
    console.log('[GCP] Permisos menu:', gcpDatos.permisos_menu.length);
    console.log('[GCP] Permisos accion:', gcpDatos.permisos_accion.length);

    // Construir mapa de permisos para búsqueda rápida
    var permisosMenuMap = {};
    for (var i = 0; i < gcpDatos.permisos_menu.length; i++) {
        var pm = gcpDatos.permisos_menu[i];
        if (!permisosMenuMap[pm.rol]) permisosMenuMap[pm.rol] = {};
        permisosMenuMap[pm.rol][pm.menu] = true;
    }
    
    var permisosAccionMap = {};
    for (var i = 0; i < gcpDatos.permisos_accion.length; i++) {
        var pa = gcpDatos.permisos_accion[i];
        if (!permisosAccionMap[pa.rol]) permisosAccionMap[pa.rol] = {};
        permisosAccionMap[pa.rol][pa.accion] = true;
    }

    // Construir árbol de módulos
    var modulosPorNivel = {};
    var modulosRaiz = [];
    
    // Agrupar módulos por nivel (padre)
    for (var i = 0; i < gcpDatos.menus.length; i++) {
        var m = gcpDatos.menus[i];
        if (!m.visible || m.visible !== 'S') continue;
        
        var nivel = m.padre === null || m.padre === '' ? 0 : 1; // Simplificado: asume máximo 2 niveles
        if (!modulosPorNivel[nivel]) modulosPorNivel[nivel] = [];
        modulosPorNivel[nivel].push(m);
        
        if (nivel === 0) modulosRaiz.push(m);
    }
    console.log('[GCP] Modulos raíz:', modulosRaiz.length);

    // Construir HTML del árbol
    var h = '<div class="gcp-module-tree">';
    
    // Procesar módulos raíz
    for (var i = 0; i < modulosRaiz.length; i++) {
        h += gcpRenderizarModuloRecursivo(modulosRaiz[i], 0, rolId, permisosMenuMap, permisosAccionMap);
    }
    
    h += '</div>';
    
    console.log('[GCP] HTML generado, longitud:', h.length);
    c.innerHTML = h;
    console.log('[GCP] innerHTML asignado');
    
    // Inicializar estados de checkboxes indeterminados
    gcpActualizarEstadosIndeterminados(rolId, permisosMenuMap, permisosAccionMap);
    console.log('[GCP] Estados indeterminados actualizados');
}

// Función recursiva para renderizar un módulo y sus hijos
function gcpRenderizarModuloRecursivo(modulo, nivel, rolId, permisosMenuMap, permisosAccionMap) {
    var menuSlug = modulo.menu;
    var tienePermisoMenu = permisosMenuMap[rolId] && permisosMenuMap[rolId][menuSlug];
    var tieneHijos = false;
    
    // Verificar si tiene hijos directos (simplificado: asumimos que los hijos tienen este módulo como padre)
    for (var i = 0; i < gcpDatos.menus.length; i++) {
        var hijo = gcpDatos.menus[i];
        if (hijo.padre === menuSlug) {
            tieneHijos = true;
            break;
        }
    }
    
    var h = '';
    h += '<div class="gcp-module-item" data-menu="' + menuSlug + '" data-nivel="' + nivel + '">';
    
    // Header del módulo
    h += '<div class="gcp-module-header" onclick="gcpToggleModulo(this)">';
    h += '<input type="checkbox" class="gcp-module-checkbox" value="' + menuSlug + '" ' + (tienePermisoMenu ? 'checked' : '') + '>';
    h += '<span class="gcp-module-icon">' + (modulo.icono || '') + '</span>';
    h += '<span class="gcp-module-name">' + gcpEsc(modulo.nombre) + '</span>';
    
    // Indicador de acciones disponibles
    var accionesDelModulo = [];
    for (var j = 0; j < gcpDatos.acciones.length; j++) {
        if (gcpDatos.acciones[j].menu === menuSlug) {
            accionesDelModulo.push(gcpDatos.acciones[j]);
        }
    }
    if (accionesDelModulo.length > 0) {
        h += '<span class="gcp-module-indicator">(' + accionesDelModulo.length + ' acciones)</span>';
    }
    h += '</div>';
    
    // Contenedor de acciones (si tiene acciones)
    if (accionesDelModulo.length > 0) {
        h += '<div class="gcp-actions-container">';
        for (var j = 0; j < accionesDelModulo.length; j++) {
            var accion = accionesDelModulo[j];
            var tienePermisoAccion = permisosAccionMap[rolId] && permisosAccionMap[rolId][accion.id];
            var claseDeshabilitada = !tienePermisoMenu ? ' gcp-action-disabled' : '';
            
            h += '<div class="gcp-action-item' + claseDeshabilitada + '">';
            h += '<input type="checkbox" class="gcp-action-checkbox" value="' + accion.id + '" data-menu="' + menuSlug + '" ' + (tienePermisoAccion ? 'checked' : '') + '>';
            h += '<label class="gcp-action-label">' + gcpNombreAccion(accion.accion) + '</label>';
            h += '</div>';
        }
        h += '</div>';
    }
    
    // Contenedor de hijos (si tiene hijos)
    if (tieneHijos) {
        h += '<div class="gcp-module-children" style="padding-left: ' + (20 * (nivel + 1)) + 'px;">';
        for (var i = 0; i < gcpDatos.menus.length; i++) {
            var hijo = gcpDatos.menus[i];
            if (hijo.padre === menuSlug) {
                h += gcpRenderizarModuloRecursivo(hijo, nivel + 1, rolId, permisosMenuMap, permisosAccionMap);
            }
        }
        h += '</div>';
    }
    
    h += '</div>';
    
    return h;
}

// ============================================================
// 5. TOGGLE DE MÓDULO (marcar/desmarcar módulo y sus acciones)
// ============================================================
function gcpToggleModulo(element) {
    var checkbox = element.querySelector('.gcp-module-checkbox');
    var marcado = checkbox.checked;
    var menuSlug = checkbox.value;
    
    // Encontrar el contenedor del módulo
    var moduloItem = element.closest('.gcp-module-item');
    
    // Habilitar/deshabilitar acciones del módulo
    var acciones = moduloItem.querySelectorAll('.gcp-action-checkbox');
    for (var i = 0; i < acciones.length; i++) {
        var input = acciones[i];
        input.disabled = !marcado;
        if (!marcado) input.checked = false; // Desmarcar acciones al deshabilitar módulo
    }
    
    // Actualizar estado visual del módulo padre (si existe)
    gcpActualizarEstadoModuloPadre(moduloItem);
}

// ============================================================
// 6. ACTUALIZAR ESTADO DEL MÓDULO PADRE BASADO EN SUS HIJOS
// ============================================================
function gcpActualizarEstadoModuloPadre(moduloItem) {
    // Subir al contenedor de hijos y de ahí al módulo padre
    var childrenContainer = moduloItem.closest('.gcp-module-children');
    if (!childrenContainer) return; // No hay padre (es raíz)
    
    var padreItem = childrenContainer.parentElement;
    if (!padreItem || !padreItem.classList.contains('gcp-module-item')) return;
    
    var padreCheckbox = padreItem.querySelector('.gcp-module-checkbox');
    if (!padreCheckbox) return;
    
    // Obtener todos los checkboxes de módulos hermanos (hijos del mismo padre)
    var hermanosChecks = childrenContainer.querySelectorAll('.gcp-module-checkbox');
    var todosMarcados = true;
    var algunoMarcado = false;
    
    for (var i = 0; i < hermanosChecks.length; i++) {
        var cb = hermanosChecks[i];
        if (cb.checked) algunoMarcado = true;
        else todosMarcados = false;
    }
    
    if (algunoMarcado && !todosMarcados) {
        // Estado indeterminado
        padreCheckbox.checked = false;
        padreCheckbox.indeterminate = true;
    } else {
        padreCheckbox.checked = todosMarcados;
        padreCheckbox.indeterminate = false;
    }
    
    // Propagar hacia arriba recursivamente
    gcpActualizarEstadoModuloPadre(padreItem);
}

// ============================================================
// 7. ACTUALIZAR ESTADOS INDETERMINADOS INICIALES
// ============================================================
function gcpActualizarEstadosIndeterminados(rolId, permisosMenuMap, permisosAccionMap) {
    // Actualizar estados de módulos basado en sus acciones
    var modulosItems = document.querySelectorAll('.gcp-module-item');
    
    for (var i = 0; i < modulosItems.length; i++) {
        var moduloItem = modulosItems[i];
        var menuSlug = moduloItem.dataset.menu;
        var accionesChecks = moduloItem.querySelectorAll('.gcp-action-checkbox');
        
        if (accionesChecks.length === 0) return; // Módulo sin acciones
        
        var totalAcciones = accionesChecks.length;
        var accionesMarcadas = 0;
        
        for (var j = 0; j < accionesChecks.length; j++) {
            var cb = accionesChecks[j];
            if (cb.checked) accionesMarcadas++;
        }
        
        var moduloCheckbox = moduloItem.querySelector('.gcp-module-checkbox');
        
        if (accionesMarcadas === 0) {
            moduloCheckbox.checked = false;
            moduloCheckbox.indeterminate = false;
        } else if (accionesMarcadas === totalAcciones) {
            moduloCheckbox.checked = true;
            moduloCheckbox.indeterminate = false;
        } else {
            // Estado indeterminado: algunas acciones marcadas
            moduloCheckbox.checked = false; // Visualmente no marcado
            moduloCheckbox.indeterminate = true;
        }
    }
}

// ============================================================
// 8. CONFIRMAR GUARDAR (SweetAlert2)
// ============================================================
function gcpConfirmarGuardar() {
    if (gcpRolActual === 0) {
        gcpMsg('Seleccione un rol primero', 'Aviso', 'warning');
        return;
    }

    // Buscar nombre del rol
    var nombreRol = '';
    for (var i = 0; i < gcpDatos.roles.length; i++) {
        if (gcpDatos.roles[i].id == gcpRolActual) {
            nombreRol = gcpDatos.roles[i].nombre;
            break;
        }
    }

    // Recopilar módulos y acciones seleccionadas
    var modulosSeleccionados = [];
    var checksModulo = document.querySelectorAll('.gcp-module-checkbox:checked');
    for (var i = 0; i < checksModulo.length; i++) {
        modulosSeleccionados.push(checksModulo[i].value);
    }

    var accionesSeleccionadas = [];
    var checksAccion = document.querySelectorAll('.gcp-action-checkbox:checked');
    for (var i = 0; i < checksAccion.length; i++) {
        accionesSeleccionadas.push(checksAccion[i].value);
    }

    // Generar resumen detallado
    var htmlResumen = '<div style="text-align:left; font-size:0.9em; max-height: 300px; overflow-y: auto;">';
    htmlResumen += '<p><b>Rol:</b> ' + gcpEsc(nombreRol) + '</p>';
    htmlResumen += '<p><b>Módulos seleccionados:</b> ' + (modulosSeleccionados.length > 0 ? gcpEsc(modulosSeleccionados.join(', ')) : '<i>Ninguno</i>') + '</p>';
    htmlResumen += '<p><b>Acciones seleccionadas:</b> ' + (accionesSeleccionadas.length > 0 ? accionesSeleccionadas.length + ' acciones' : '<i>Ninguna</i>') + '</p>';
    
    // Mostrar detalle de módulos si hay pocos
    if (modulosSeleccionados.length <= 5) {
        var detalleModulos = [];
        for (var i = 0; i < modulosSeleccionados.length; i++) {
            var slug = modulosSeleccionados[i];
            var nombreModulo = '';
            for (var j = 0; j < gcpDatos.menus.length; j++) {
                if (gcpDatos.menus[j].menu === slug) {
                    nombreModulo = gcpDatos.menus[j].nombre;
                    break;
                }
            }
            detalleModulos.push(nombreModulo || slug);
        }
        htmlResumen += '<p><b>Detalle módulos:</b> ' + gcpEsc(detalleModulos.join(', ')) + '</p>';
    }
    
    htmlResumen += '</div>';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirmar guardado de permisos',
            html: htmlResumen,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#405189',
            cancelButtonColor: '#f06548',
            confirmButtonText: 'Si, guardar',
            cancelButtonText: 'Cancelar',
            width: '500px'
        }).then(function(result) {
            if (result.isConfirmed) {
                gcpEjecutarGuardar();
            }
        });
    } else {
        gcpEjecutarGuardar();
    }
}

// ============================================================
// 9. EJECUTAR GUARDADO
// ============================================================
function gcpEjecutarGuardar() {
    // Recolectar módulos seleccionados
    var modulos = [];
    var checksModulo = document.querySelectorAll('.gcp-module-checkbox:checked');
    for (var i = 0; i < checksModulo.length; i++) {
        modulos.push(checksModulo[i].value);
    }

    // Recolectar acciones seleccionadas (solo las habilitadas)
    var acciones = [];
    var checksAccion = document.querySelectorAll('.gcp-action-checkbox:checked');
    for (var i = 0; i < checksAccion.length; i++) {
        var cb = checksAccion[i];
        // Solo incluir si su módulo está habilitado (no deshabilitado)
        var accionItem = cb.closest('.gcp-action-item');
        if (!accionItem || !accionItem.classList.contains('gcp-action-disabled')) {
            acciones.push(cb.value);
        }
    }

    // Construir FormData para envio
    var fd = new FormData();
    fd.append('rol_id', gcpRolActual);
    for (var i = 0; i < modulos.length; i++) {
        fd.append('menus[]', modulos[i]);
    }
    for (var i = 0; i < acciones.length; i++) {
        fd.append('acciones[]', acciones[i]);
    }

    // Enviar peticion AJAX al metodo guardar
    $.ajax({
        url: page_root + 'guardar',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: { 'Authorization': TOKEN_GLOBAL },
        success: function(r) {
            if (r.error) {
                gcpMsg(r.msg, 'Error', 'error');
                return;
            }
            gcpMsg(r.msg, 'Exito', 'success');
            // Recargar datos para reflejar cambios
            gcpCargarDatos();
            // Reseleccionar el rol para mantener la vista
            setTimeout(function() {
                gcpSeleccionarRol(gcpRolActual);
            }, 500);
        },
        error: function() {
            gcpMsg('Error al guardar los permisos', 'Error', 'error');
        }
    });
}

// ============================================================
// INICIALIZAR: Cargar datos al listo el documento
// ============================================================
$(document).ready(function() {
    gcpCargarDatos();
});
</script>