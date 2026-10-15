<?php
// ============================================================
// ASISTENCIA — Tab Mis hijos (acudiente: solo sus hijos)
//
// Historial de asistencia de cada hija con horas.
// El backend filtra por el acudiente en sesion.
//
// Reglas AGENTS.md (§10):
//   - Plantillas con backticks, una funcion por pieza
//   - Bucles for tradicionales, var en espanol
// ============================================================
?>

<!-- Resumen por hija -->
<div class="row g-3 mb-3" id="asiResumenHijos"></div>

<!-- Historial detallado -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col">Hija</th>
                <th scope="col">Fecha</th>
                <th scope="col">Clase</th>
                <th scope="col">Estado</th>
                <th scope="col">Entrada</th>
                <th scope="col">Salida</th>
                <th scope="col" class="text-center">Tiempo</th>
            </tr>
        </thead>
        <tbody id="asiTablaHijos">
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Cargando historial...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB MIS HIJOS                                   -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Crear una tarjeta de resumen por hija con backticks
function asiCrearTarjetaHija(nombre, total_clases, total_asistencias, total_horas) {
    var porcentaje = 0;
    if (total_clases > 0) {
        porcentaje = Math.round(total_asistencias * 100 / total_clases);
    }
    return `<div class="col-md-4">
        <div class="card border mb-0">
            <div class="card-body">
                <h6 class="fw-bold mb-1">${asiEsc(nombre)}</h6>
                <small class="text-muted d-block mb-2">${total_asistencias} asistencias de ${total_clases} clases</small>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary">${porcentaje}% asistencia</span>
                    <span class="fw-bold text-success">${total_horas} h</span>
                </div>
            </div>
        </div>
    </div>`;
}

// 2. Crear una fila del historial con backticks
function asiCrearFilaHija(item) {
    var hija = asiEsc(item.hija_nombre);
    var fecha_txt = asiFormatearFecha(item.fecha);
    var clase_txt = asiEsc(item.clase_nombre);
    if (!item.clase_nombre) {
        clase_txt = 'Sin clases registradas';
        fecha_txt = '-';
    }
    var badge = asiBadgeEstado(item.estado);
    var entrada = asiFormatearHora(item.hora_entrada);
    var salida = asiFormatearHora(item.hora_salida);

    var tiempo_txt = '-';
    if (item.minutos && parseInt(item.minutos) > 0) {
        tiempo_txt = item.minutos + ' min';
    }

    return `<tr>
        <td><span class="fw-semibold">${hija}</span></td>
        <td>${fecha_txt}</td>
        <td>${clase_txt}</td>
        <td>${badge}</td>
        <td>${entrada}</td>
        <td>${salida}</td>
        <td class="text-center">${tiempo_txt}</td>
    </tr>`;
}

// 3. Renderizar resumen e historial de los hijos
function asiRenderizarHijos(lista) {
    var cuerpo = document.getElementById('asiTablaHijos');
    var resumen = document.getElementById('asiResumenHijos');
    if (!cuerpo || !resumen) {
        return;
    }

    // Filtrar solo filas con clase registrada
    var con_clase = [];
    for (var i = 0; i < lista.length; i++) {
        if (lista[i].clase_nombre) {
            con_clase.push(lista[i]);
        }
    }

    if (con_clase.length === 0) {
        cuerpo.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4 text-muted">Aun no hay asistencia registrada para sus hijas</td>
        </tr>`;
        resumen.innerHTML = '';
        return;
    }

    // Agrupar totales por hija
    var nombres = [];
    var totales_clases = [];
    var totales_asistencias = [];
    var totales_minutos = [];
    for (var j = 0; j < con_clase.length; j++) {
        var fila = con_clase[j];
        var posicion = -1;
        for (var k = 0; k < nombres.length; k++) {
            if (nombres[k] === fila.hija_nombre) {
                posicion = k;
            }
        }
        if (posicion === -1) {
            nombres.push(fila.hija_nombre);
            totales_clases.push(1);
            totales_asistencias.push(0);
            totales_minutos.push(0);
            posicion = nombres.length - 1;
        } else {
            totales_clases[posicion] = totales_clases[posicion] + 1;
        }
        if (fila.estado === 'asistio' || fila.estado === 'tarde') {
            totales_asistencias[posicion] = totales_asistencias[posicion] + 1;
        }
        totales_minutos[posicion] = totales_minutos[posicion] + parseInt(fila.minutos || 0);
    }

    // Pintar tarjetas de resumen
    var html_resumen = '';
    for (var m = 0; m < nombres.length; m++) {
        var horas = Math.round((totales_minutos[m] / 60) * 10) / 10;
        html_resumen = html_resumen + asiCrearTarjetaHija(nombres[m], totales_clases[m], totales_asistencias[m], horas);
    }
    resumen.innerHTML = html_resumen;

    // Pintar historial completo
    var html_final = '';
    for (var n = 0; n < con_clase.length; n++) {
        html_final = html_final + asiCrearFilaHija(con_clase[n]);
    }
    cuerpo.innerHTML = html_final;
}

// 4. Cargar el resumen de los hijos desde el backend
function asiCargarHijos() {
    asiAjax('resumen_hijos', {}, function(respuesta) {
        if (!respuesta.error) {
            asiRenderizarHijos(respuesta.data);
        }
    });
}

// 5. Cargar al abrir el modulo
document.addEventListener('DOMContentLoaded', function() {
    asiCargarHijos();
});
</script>
