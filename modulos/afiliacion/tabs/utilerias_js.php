<?php
// afiliacion/tabs/utilerias_js.php - Utilidades JS compartidas entre tabs
// Se incluye en formulario.php ANTES de los tabs (function declarations son hoisted)
?>
<script type="text/javascript">
// ===== UTILIDADES GLOBALES DEL MODULO AFILIACION =====

// afiliacionAjax - peticion AJAX con token
function afiliacionAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    afiliacionMostrarMsg(respuesta.msg, 'error');
                } else if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parsing response:', e);
                afiliacionMostrarMsg('Error al procesar respuesta', 'error');
            }
        } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            afiliacionMostrarMsg('Error en el servidor', 'error');
        }
    };

    xhr.onerror = function() {
        console.error('Network error');
        afiliacionMostrarMsg('Error de conexion', 'error');
    };

    var params = new URLSearchParams();
    for (var key in datos) {
        if (datos[key] !== null && datos[key] !== undefined) {
            params.append(key, datos[key]);
        }
    }
    xhr.send(params.toString());
}

// afiliacionAjaxFormData - peticion AJAX con FormData (para subida de archivos)
function afiliacionAjaxFormData(accion, formData, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    afiliacionMostrarMsg(respuesta.msg, 'error');
                } else if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parsing response:', e);
                afiliacionMostrarMsg('Error al procesar respuesta', 'error');
            }
        } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            afiliacionMostrarMsg('Error en el servidor', 'error');
        }
    };

    xhr.onerror = function() {
        console.error('Network error');
        afiliacionMostrarMsg('Error de conexion', 'error');
    };

    xhr.send(formData);
}

// afiliacionEsc - escape HTML basico (XSS protection)
function afiliacionEsc(valor) {
    if (!valor) return '';
    return valor.toString()
        .replace(/&/g, '&')
        .replace(/</g, '<')
        .replace(/>/g, '>')
        .replace(/"/g, '"')
        .replace(/'/g, '&#039;');
}

// afiliacionMostrarMsg - mostrar mensaje toast
function afiliacionMostrarMsg(texto, tipo) {
    if (typeof Toastify !== 'undefined') {
        var color = '#405189';
        if (tipo == 'success') color = '#0ab39c';
        if (tipo == 'error') color = '#f06548';
        if (tipo == 'warning') color = '#f7b84b';
        Toastify({ text: texto, duration: 3000, gravity: 'top', position: 'right', style: { background: color } }).showToast();
    } else {
        alert(texto);
    }
}

// afiliacionDebounce - debounce para busqueda
function afiliacionDebounce(func, delay) {
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

// afiliacionFormatearEstado - retorna clase CSS y texto para badge de estado
function afiliacionFormatearEstado(estado) {
    var estadoClass = 'bg-secondary';
    var estadoTexto = estado;
    if (estado == 'aprobado' || estado == 'activo') { estadoClass = 'bg-success'; }
    else if (estado == 'pendiente') { estadoClass = 'bg-info'; }
    else if (estado == 'en_revision') { estadoClass = 'bg-warning'; }
    else if (estado == 'rechazado' || estado == 'no_aprobado') { estadoClass = 'bg-danger'; }
    else if (estado == 'borrador') { estadoClass = 'bg-secondary'; }
    return { class: estadoClass, texto: estadoTexto };
}

// afiliacionCrearBotonesAccion - crear botones de accion para tabla
function afiliacionCrearBotonesAccion(id, es_admin) {
    var botones = '<div class="d-flex justify-content-center gap-1">';
    botones += '<button class="btn btn-sm btn-soft-info afiliacion-btn-accion" onclick="afiliacionVer(' + id + ')" title="Ver detalle"><i class="ri-eye-line"></i></button>';
    if (es_admin) {
        botones += '<button class="btn btn-sm btn-soft-primary afiliacion-btn-accion accion-modificar" onclick="afiliacionAbrirEstado(' + id + ')" title="Cambiar estado"><i class="ri-edit-line"></i></button>';
        botones += '<button class="btn btn-sm btn-soft-danger afiliacion-btn-accion accion-eliminar" onclick="afiliacionEliminar(' + id + ')" title="Eliminar"><i class="ri-delete-bin-line"></i></button>';
    }
    botones += '</div>';
    return botones;
}

// afiliacionCrearProgreso - crear barra de progreso HTML
function afiliacionCrearProgreso(porcentaje) {
    var p = parseInt(porcentaje) || 0;
    return '<div class="progress" style="height: 20px;">' +
        '<div class="progress-bar" role="progressbar" style="width: ' + p + '%;">' + p + '%</div>' +
        '</div>';
}

// afiliacionCrearPDFLink - crear link o boton para PDF
function afiliacionCrearPDFLink(pdf_ruta, es_admin) {
    if (pdf_ruta && pdf_ruta !== '') {
        if (es_admin) {
            return '<a href="' + page_root + 'descarga.php?archivo=' + pdf_ruta + '" class="btn btn-sm btn-link text-primary" target="_blank" title="Ver PDF"><i class="ri-file-pdf-line"></i> Ver</a>';
        } else {
            return '<a href="' + page_root + 'descarga.php?archivo=' + pdf_ruta + '" class="btn btn-sm btn-link text-primary" target="_blank" title="Ver PDF"><i class="ri-file-pdf-line"></i> Ver PDF</a>';
        }
    }
    return '<span class="text-muted">—</span>';
}
</script>