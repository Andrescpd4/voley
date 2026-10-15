<?php
// ============================================================
// ASISTENCIA — Tab Jornadas (staff: admin y entrenador)
//
// Historial de jornadas: abiertas y cerradas con sus conteos.
// Las jornadas se inician desde la Sesion en vivo.
// Solo se puede eliminar una abierta sin registros.
//
// Reglas AGENTS.md (§10):
//   - Plantillas con backticks, una funcion por pieza
//   - Bucles for tradicionales, var en espanol
// ============================================================
?>

<!-- Titulo del historial -->
<div class="row g-3 mb-3 align-items-center">
    <div class="col-md-12">
        <h6 class="mb-0 text-muted">Historial de jornadas (las nuevas se inician en la Sesion en vivo)</h6>
    </div>
</div>

<!-- Tabla de clases -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col">Clase</th>
                <th scope="col">Categoria</th>
                <th scope="col">Fecha y horario</th>
                <th scope="col">Lugar</th>
                <th scope="col" class="text-center">Estado</th>
                <th scope="col" class="text-center">Presentes</th>
                <th scope="col" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody id="asiTablaClases">
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Cargando jornadas...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB JORNADAS                                    -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Crear una fila de clase con backticks
function asiCrearFilaClase(item) {
    var id = parseInt(item.id);
    var nombre = asiEsc(item.nombre);
    var lugar = asiEsc(item.lugar);

    var categoria_txt = 'General';
    if (item.categoria_nombre) {
        categoria_txt = asiEsc(item.categoria_nombre);
    }

    var fecha_txt = asiFormatearFecha(item.fecha) + ' ' + item.hora_inicio + ' - ' + item.hora_fin;

    var estado_badge = '';
    if (item.estado === 'cerrada') {
        estado_badge = '<span class="badge bg-secondary-subtle text-secondary">Cerrada</span>';
    } else {
        estado_badge = '<span class="badge bg-success-subtle text-success">Abierta</span>';
    }

    var total_presentes = parseInt(item.total_presentes);
    if (isNaN(total_presentes)) {
        total_presentes = 0;
    }

    // Solo las abiertas sin registros se pueden eliminar
    var botones = '';
    if (item.estado === 'abierta') {
        botones = `<button type="button" class="btn btn-ghost-danger btn-icon btn-sm accion-eliminar_clase" title="Eliminar" onclick="asiEliminarClase(${id})">
                <i class="ri-delete-bin-line"></i>
            </button>`;
    } else {
        botones = '<small class="text-muted">Finalizada</small>';
    }

    return `<tr>
        <td><span class="fw-semibold text-dark">${nombre}</span></td>
        <td><span class="badge bg-light text-dark">${categoria_txt}</span></td>
        <td><i class="ri-calendar-line text-muted me-1"></i>${fecha_txt}</td>
        <td>${lugar}</td>
        <td class="text-center">${estado_badge}</td>
        <td class="text-center"><span class="badge bg-primary">${total_presentes}</span></td>
        <td class="text-end">${botones}</td>
    </tr>`;
}

// 2. Renderizar la lista de clases
function asiRenderizarClases(lista) {
    var cuerpo = document.getElementById('asiTablaClases');
    if (!cuerpo) {
        return;
    }
    if (!lista || lista.length === 0) {
        cuerpo.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4 text-muted">No hay jornadas registradas</td>
        </tr>`;
        return;
    }
    var html_final = '';
    for (var i = 0; i < lista.length; i++) {
        html_final = html_final + asiCrearFilaClase(lista[i]);
    }
    cuerpo.innerHTML = html_final;
}

// 3. Cargar clases desde el backend
function asiCargarListaClases() {
    asiAjax('listar_clases', {}, function(respuesta) {
        if (!respuesta.error) {
            asiRenderizarClases(respuesta.data);
        }
    });
}

// 4. Eliminar una jornada con confirmacion (solo abierta sin registros)
function asiEliminarClase(id) {
    if (typeof Swal === 'undefined') {
        return;
    }
    Swal.fire({
        title: 'Eliminar jornada',
        text: 'Solo se puede eliminar si aun no tiene asistencia registrada.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            asiAjax('eliminar_clase', { id: id }, function(respuesta) {
                if (!respuesta.error) {
                    asiMostrarMsg(respuesta.msg, 'success');
                    asiCargarListaClases();
                    if (typeof asiCargarClasesSesion === 'function') {
                        asiCargarClasesSesion();
                    }
                }
            });
        }
    });
}

// 5. Cargar el historial al abrir el modulo
document.addEventListener('DOMContentLoaded', function() {
    asiCargarListaClases();
});
</script>
