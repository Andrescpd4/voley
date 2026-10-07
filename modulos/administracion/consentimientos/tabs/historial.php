<?php
// consentimientos/tabs/historial.php - Tab 2: Historial de firmas
// Solo admin (roles 1 y 4), verificado en formulario.php
?>

<!-- ===== TAB 2: HISTORIAL DE FIRMAS ===== -->
<div class="card border">
    <div class="card-header bg-light">
        <h6 class="card-title mb-0">
            <i class="ri-history-line me-1"></i> Historial de firmas
        </h6>
    </div>
    <div class="card-body border-bottom">
        <!-- Filtros de busqueda -->
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="consHistTipo">Documento</label>
                <select class="form-select" id="consHistTipo">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="consHistDoc">Documento de identidad</label>
                <input type="text" class="form-control" id="consHistDoc" placeholder="Ej: 10000001">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="consHistEstado">Decisión</label>
                <select class="form-select" id="consHistEstado">
                    <option value="">Todas</option>
                    <option value="1">Aceptadas</option>
                    <option value="0">Rechazadas</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100 accion-listar" onclick="consHistCargar()">
                    <i class="ri-search-line me-1"></i> Buscar
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="consHistLimpiar()">
                    <i class="ri-eraser-line me-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha de firma</th>
                        <th>Persona</th>
                        <th>Documento</th>
                        <th>Versión</th>
                        <th>Decisión</th>
                    </tr>
                </thead>
                <tbody id="consHistCuerpo" aria-live="polite">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="ms-2">Cargando historial...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT TAB HISTORIAL                                     -->
<!-- ============================================================ -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Cargar el combo de documentos y el historial al abrir
    consAjax('listar_tipos', {}, function(respuesta) {
        if (respuesta.error) {
            return;
        }
        var combo = document.getElementById('consHistTipo');
        var lista = respuesta.data;
        for (var i = 0; i < lista.length; i++) {
            var opcion = document.createElement('option');
            opcion.value = lista[i].id;
            opcion.textContent = lista[i].nombre;
            combo.appendChild(opcion);
        }
    });
    consHistCargar();
});

// Cargar el historial con los filtros actuales
function consHistCargar() {
    var cuerpo = document.getElementById('consHistCuerpo');
    if (!cuerpo) {
        return;
    }
    cuerpo.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Buscando firmas...</span></td></tr>';

    var datos = {};
    var tipoSel = document.getElementById('consHistTipo').value;
    var docTxt = document.getElementById('consHistDoc').value.trim();
    var estadoSel = document.getElementById('consHistEstado').value;
    if (tipoSel !== '') {
        datos.tipo_id = tipoSel;
    }
    if (docTxt !== '') {
        datos.persona_doc = docTxt;
    }
    if (estadoSel !== '') {
        datos.aceptada = estadoSel;
    }

    consAjax('listar_firmas', datos, function(respuesta) {
        if (respuesta.error) {
            return;
        }
        var lista = respuesta.data;
        if (!lista || lista.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron firmas con esos filtros.</td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < lista.length; i++) {
            html += consHistCrearFila(lista[i]);
        }
        cuerpo.innerHTML = html;
    });
}

// Armar una fila del historial
function consHistCrearFila(item) {
    var fecha = consEsc(item.fecha_firma);
    var persona = consEsc(item.persona_nombre);
    var documento = consEsc(item.persona_documento);
    var tipoNombre = consEsc(item.tipo_nombre);
    var version = consEsc(item.version_firmada);
    var badgeClase = consBadgeClase(item.tipo_clase);
    var decision = 'Aceptada';
    var badgeDecision = '<span class="badge bg-success-subtle text-success">Aceptada</span>';
    if (parseInt(item.aceptada) !== 1) {
        decision = 'Rechazada';
        badgeDecision = '<span class="badge bg-danger-subtle text-danger">Rechazada</span>';
    }

    return `
    <tr>
        <td>${fecha}</td>
        <td>
            <div class="fw-medium">${persona}</div>
            <small class="text-muted">Doc: ${documento}</small>
        </td>
        <td>
            <div>${tipoNombre}</div>
            <div class="mt-1">${badgeClase}</div>
        </td>
        <td>${version}</td>
        <td>${badgeDecision}<span class="d-none">${decision}</span></td>
    </tr>`;
}

// Limpiar filtros y recargar
function consHistLimpiar() {
    document.getElementById('consHistTipo').value = '';
    document.getElementById('consHistDoc').value = '';
    document.getElementById('consHistEstado').value = '';
    consHistCargar();
}
</script>
