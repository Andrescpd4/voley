<?php
// afiliacion/tabs/utilerias_js.php - Utilidades JS compartidas entre tabs
// Se incluye en formulario.php ANTES de los tabs (function declarations son hoisted)
?>
<script type="text/javascript">
// ============================================================
// UTILIDADES GLOBALES DEL MODULO AFILIACION
// ============================================================

// 1. Peticion AJAX con JSON y Authorization header
function afiliacionAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                // Mostrar el mensaje pero SIEMPRE avisar al llamador para no dejar botones bloqueados
                if (respuesta.error) {
                    afiliacionMostrarMsg(respuesta.msg, 'error');
                }
                if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parseando JSON:', e, xhr.responseText);
                afiliacionMostrarMsg('Error al procesar la respuesta del servidor', 'error');
                if (callback) {
                    callback({ error: true, msg: 'Error al procesar la respuesta del servidor' });
                }
            }
        } else {
            console.error('Error HTTP:', xhr.status, xhr.responseText);
            afiliacionMostrarMsg('Error en el servidor (' + xhr.status + ')', 'error');
            if (callback) {
                callback({ error: true, msg: 'Error en el servidor (' + xhr.status + ')' });
            }
        }
    };

    xhr.onerror = function() {
        console.error('Error de red');
        afiliacionMostrarMsg('Error de conexion con el servidor', 'error');
        if (callback) {
            callback({ error: true, msg: 'Error de conexion con el servidor' });
        }
    };

    var params = new URLSearchParams();
    for (var clave in datos) {
        if (datos[clave] !== null && datos[clave] !== undefined) {
            params.append(clave, datos[clave]);
        }
    }
    xhr.send(params.toString());
}

// 2. Peticion AJAX con FormData (para subida de archivos)
function afiliacionAjaxFormData(accion, formData, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                // Mostrar el mensaje pero SIEMPRE avisar al llamador para no dejar botones bloqueados
                if (respuesta.error) {
                    afiliacionMostrarMsg(respuesta.msg, 'error');
                }
                if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parseando JSON FormData:', e, xhr.responseText);
                afiliacionMostrarMsg('Error al procesar la respuesta del servidor', 'error');
                if (callback) {
                    callback({ error: true, msg: 'Error al procesar la respuesta del servidor' });
                }
            }
        } else {
            console.error('Error HTTP:', xhr.status, xhr.responseText);
            afiliacionMostrarMsg('Error en el servidor (' + xhr.status + ')', 'error');
            if (callback) {
                callback({ error: true, msg: 'Error en el servidor (' + xhr.status + ')' });
            }
        }
    };

    xhr.onerror = function() {
        console.error('Error de red FormData');
        afiliacionMostrarMsg('Error de conexion con el servidor', 'error');
        if (callback) {
            callback({ error: true, msg: 'Error de conexion con el servidor' });
        }
    };

    xhr.send(formData);
}

// 3. Escape de caracteres HTML para prevenir XSS
function afiliacionEsc(valor) {
    if (!valor && valor !== 0) {
        return '';
    }
    return valor.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// 4. Mostrar notificaciones Toast con colores del tema Voley+
function afiliacionMostrarMsg(texto, tipo) {
    if (typeof Toastify !== 'undefined') {
        var colorFondo = '#405189';
        if (tipo === 'success') { colorFondo = '#0ab39c'; }
        if (tipo === 'error') { colorFondo = '#f06548'; }
        if (tipo === 'warning') { colorFondo = '#f7b84b'; }
        if (tipo === 'info') { colorFondo = '#405189'; }

        Toastify({
            text: texto,
            duration: 3500,
            gravity: 'top',
            position: 'right',
            style: { background: colorFondo }
        }).showToast();
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            text: texto,
            icon: tipo === 'error' ? 'error' : (tipo === 'success' ? 'success' : 'info'),
            toast: true,
            position: 'top-end',
            timer: 3500,
            showConfirmButton: false
        });
    } else {
        alert(texto);
    }
}

// 5. Formatear badges de estado con colores Voley+
function afiliacionBadgeEstado(estado) {
    var claseCss = 'bg-secondary';
    var textoEstado = estado;

    if (estado === 'aprobado' || estado === 'activo') {
        claseCss = 'bg-success';
        textoEstado = (estado === 'aprobado') ? 'Aprobado' : 'Activo';
    } else if (estado === 'pendiente_revision') {
        claseCss = 'bg-warning text-dark';
        textoEstado = 'Pendiente Revision';
    } else if (estado === 'requiere_info') {
        claseCss = 'bg-warning text-dark';
        textoEstado = 'Requiere Informacion';
    } else if (estado === 'no_aprobado' || estado === 'rechazado') {
        claseCss = 'bg-danger';
        textoEstado = (estado === 'no_aprobado') ? 'No Aprobado' : 'Rechazado';
    } else if (estado === 'borrador') {
        claseCss = 'bg-secondary';
        textoEstado = 'Borrador';
    } else if (estado === 'inactivo') {
        claseCss = 'bg-dark';
        textoEstado = 'Inactivo';
    }

    return `<span class="badge ${claseCss}">${afiliacionEsc(textoEstado)}</span>`;
}

// 6. Barra de progreso visual
function afiliacionBarraProgreso(porcentaje) {
    var p = parseInt(porcentaje) || 0;
    var colorBarra = 'bg-primary';
    if (p === 100) { colorBarra = 'bg-success'; }
    else if (p >= 50) { colorBarra = 'bg-warning'; }

    return `<div class="progress" style="height: 18px;">
        <div class="progress-bar ${colorBarra}" role="progressbar" style="width: ${p}%;" aria-valuenow="${p}" aria-valuemin="0" aria-valuemax="100">
            ${p}%
        </div>
    </div>`;
}

// 7. Debounce para evitar llamadas repetidas
function afiliacionDebounce(funcion, espera) {
    var temporizador;
    return function() {
        var contexto = this;
        var argumentos = arguments;
        clearTimeout(temporizador);
        temporizador = setTimeout(function() {
            funcion.apply(contexto, argumentos);
        }, espera);
    };
}
</script>