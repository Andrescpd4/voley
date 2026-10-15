<?php
// ============================================================
// ASISTENCIA — Tab Consolidado (staff: admin y entrenador)
//
// Porcentaje y horas por deportista en un periodo.
// % = (asistio + tarde) / total de marcas.
//
// Reglas AGENTS.md (§10):
//   - Plantillas con backticks, una funcion por pieza
//   - Bucles for tradicionales, var en espanol
// ============================================================
?>

<!-- Filtros del periodo -->
<div class="row g-3 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label text-muted small mb-1">Desde</label>
        <input type="date" id="asiFiltroDesde" class="form-control form-control-sm">
    </div>
    <div class="col-md-3">
        <label class="form-label text-muted small mb-1">Hasta</label>
        <input type="date" id="asiFiltroHasta" class="form-control form-control-sm">
    </div>
    <div class="col-md-3">
        <label class="form-label text-muted small mb-1">Categoria</label>
        <select id="asiFiltroCategoria" class="form-select form-select-sm">
            <option value="0">Todas las categorias</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary btn-sm w-100" onclick="asiCargarConsolidado()">
            <i class="ri-search-line me-1"></i> Buscar
        </button>
    </div>
</div>

<!-- Tabla del consolidado -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col">Deportista</th>
                <th scope="col">Categoria</th>
                <th scope="col" class="text-center">Asistio</th>
                <th scope="col" class="text-center">Tarde</th>
                <th scope="col" class="text-center">Excusa</th>
                <th scope="col" class="text-center">No asistio</th>
                <th scope="col" class="text-center">Total</th>
                <th scope="col" class="text-center">% Asistencia</th>
                <th scope="col" class="text-center">Horas</th>
            </tr>
        </thead>
        <tbody id="asiTablaConsolidado">
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">Pulse Buscar para ver el consolidado</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB CONSOLIDADO                                 -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Crear una fila del consolidado con backticks
function asiCrearFilaConsolidado(item) {
    var nombre = asiEsc(item.nombre_completo);
    var documento = asiEsc(item.identificacion);

    var categoria_txt = 'Sin categoria';
    if (item.categoria_nombre) {
        categoria_txt = asiEsc(item.categoria_nombre);
    }

    var porcentaje = parseInt(item.porcentaje);
    if (isNaN(porcentaje)) {
        porcentaje = 0;
    }

    // Color de la barra segun el porcentaje
    var color_barra = 'bg-danger';
    if (porcentaje >= 80) {
        color_barra = 'bg-success';
    } else if (porcentaje >= 50) {
        color_barra = 'bg-warning';
    }

    return `<tr>
        <td>
            <div class="fw-semibold text-dark">${nombre}</div>
            <small class="text-muted">${documento}</small>
        </td>
        <td><span class="badge bg-light text-dark">${categoria_txt}</span></td>
        <td class="text-center"><span class="badge bg-success-subtle text-success">${item.total_asistio}</span></td>
        <td class="text-center"><span class="badge bg-warning-subtle text-warning">${item.total_tarde}</span></td>
        <td class="text-center"><span class="badge bg-info-subtle text-info">${item.total_excusa}</span></td>
        <td class="text-center"><span class="badge bg-danger-subtle text-danger">${item.total_no_asistio}</span></td>
        <td class="text-center fw-bold">${item.total_marcas}</td>
        <td class="text-center" style="min-width: 130px;">
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-fill" style="height: 8px;">
                    <div class="progress-bar ${color_barra}" style="width: ${porcentaje}%"></div>
                </div>
                <small class="fw-bold">${porcentaje}%</small>
            </div>
        </td>
        <td class="text-center fw-bold">${item.horas} h</td>
    </tr>`;
}

// 2. Renderizar el consolidado en la tabla
function asiRenderizarConsolidado(lista) {
    var cuerpo = document.getElementById('asiTablaConsolidado');
    if (!cuerpo) {
        return;
    }
    if (!lista || lista.length === 0) {
        cuerpo.innerHTML = `<tr>
            <td colspan="9" class="text-center py-4 text-muted">No hay registros en el periodo</td>
        </tr>`;
        return;
    }
    var html_final = '';
    for (var i = 0; i < lista.length; i++) {
        html_final = html_final + asiCrearFilaConsolidado(lista[i]);
    }
    cuerpo.innerHTML = html_final;
}

// 3. Cargar el consolidado con los filtros elegidos
function asiCargarConsolidado() {
    var desde = document.getElementById('asiFiltroDesde').value;
    var hasta = document.getElementById('asiFiltroHasta').value;
    var categoria = document.getElementById('asiFiltroCategoria').value;

    var cuerpo = document.getElementById('asiTablaConsolidado');
    cuerpo.innerHTML = `<tr>
        <td colspan="9" class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            Calculando consolidado...
        </td>
    </tr>`;

    asiAjax('consolidado', { desde: desde, hasta: hasta, categoria_id: categoria }, function(respuesta) {
        if (!respuesta.error) {
            asiRenderizarConsolidado(respuesta.data);
        }
    });
}

// 4. Llenar fechas del mes actual y combo de categorias
function asiIniciarConsolidado() {
    var ahora_txt = asiAhoraTexto();
    var mes_actual = ahora_txt.slice(0, 7);
    document.getElementById('asiFiltroDesde').value = mes_actual + '-01';
    document.getElementById('asiFiltroHasta').value = ahora_txt.slice(0, 10);

    asiAjax('listar_categorias', {}, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var select = document.getElementById('asiFiltroCategoria');
            var html_opciones = '<option value="0">Todas las categorias</option>';
            for (var i = 0; i < respuesta.data.length; i++) {
                var cat = respuesta.data[i];
                html_opciones = html_opciones + `<option value="${cat.id}">${asiEsc(cat.nombre)}</option>`;
            }
            select.innerHTML = html_opciones;
        }
    });
}

// 5. Iniciar filtros al abrir el modulo
document.addEventListener('DOMContentLoaded', function() {
    asiIniciarConsolidado();
});
</script>
