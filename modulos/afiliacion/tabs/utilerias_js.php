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

// 4. Mostrar notificaciones Toast con los tokens del tema Velzon
function afiliacionMostrarMsg(texto, tipo) {
    if (typeof Toastify !== 'undefined') {
        var colorFondo = 'var(--vz-primary)';
        if (tipo === 'success') { colorFondo = 'var(--vz-success)'; }
        if (tipo === 'error') { colorFondo = 'var(--vz-danger)'; }
        if (tipo === 'warning') { colorFondo = 'var(--vz-warning)'; }
        if (tipo === 'info') { colorFondo = 'var(--vz-info)'; }

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

    // Nota de contraste: el blanco sobre success (2.64) y danger (3.15) no se lee;
    // por eso esos badges llevan texto oscuro (dark sobre success 5.83, sobre danger 4.89)
    if (estado === 'aprobado' || estado === 'activo') {
        claseCss = 'bg-success text-dark';
        textoEstado = (estado === 'aprobado') ? 'Aprobado' : 'Activo';
    } else if (estado === 'pendiente_revision') {
        claseCss = 'bg-warning text-dark';
        textoEstado = 'Pendiente Revision';
    } else if (estado === 'requiere_info') {
        claseCss = 'bg-warning text-dark';
        textoEstado = 'Requiere Informacion';
    } else if (estado === 'no_aprobado' || estado === 'rechazado') {
        claseCss = 'bg-danger text-dark';
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
// El numero va en blanco sobre primary (7.62 OK) y en oscuro sobre success/warning
// porque el blanco ahi no se lee (success 2.64, warning 1.76)
function afiliacionBarraProgreso(porcentaje) {
    var p = parseInt(porcentaje) || 0;
    var colorBarra = 'bg-primary';
    var colorTexto = '';
    if (p === 100) {
        colorBarra = 'bg-success';
        colorTexto = 'text-dark';
    } else if (p >= 50) {
        colorBarra = 'bg-warning';
        colorTexto = 'text-dark';
    }

    return `<div class="progress" style="height: 18px;">
        <div class="progress-bar ${colorBarra} ${colorTexto} fw-bold" role="progressbar" style="width: ${p}%;" aria-valuenow="${p}" aria-valuemin="0" aria-valuemax="100">
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

// 8. Confirmacion con el estilo del sistema (SweetAlert2 global; confirm nativo solo de respaldo)
function afiliacionConfirmar(titulo, texto, texto_boton, al_confirmar) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: titulo,
            text: texto,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: texto_boton,
            cancelButtonText: 'Cancelar'
        }).then(function(resultado) {
            if (resultado.isConfirmed) {
                al_confirmar();
            }
        });
    } else {
        if (confirm(texto)) {
            al_confirmar();
        }
    }
}

// 9. Devolver texto seguro (si viene vacio, usar el valor por defecto)
function afiliacionTexto(valor, defecto) {
    if (valor === null || valor === undefined || valor === '') {
        return defecto;
    }
    return valor;
}

// 10. Unir partes de un nombre ignorando las vacias
function afiliacionUnirNombres(parte1, parte2, parte3, parte4) {
    var partes = [];
    if (parte1) { partes.push(parte1); }
    if (parte2) { partes.push(parte2); }
    if (parte3) { partes.push(parte3); }
    if (parte4) { partes.push(parte4); }
    return partes.join(' ');
}

// 11. Normalizar la ficha de afiliacion a un solo formato
// El admin recibe campos planos (deportista_nombre1...), el acudiente recibe
// objeto anidado (nombre1... + acudiente{...}). Ambos quedan igual aqui.
function afiliacionNormalizarFicha(d) {
    var ficha = {};
    var acu = null;
    if (d.acudiente && typeof d.acudiente === 'object') {
        acu = d.acudiente;
    }

    // Datos del deportista (forma admin o forma acudiente)
    ficha.dep_nombre = afiliacionUnirNombres(
        afiliacionTexto(d.deportista_nombre1, d.nombre1),
        afiliacionTexto(d.deportista_nombre2, d.nombre2),
        afiliacionTexto(d.deportista_apellido1, d.apellido1),
        afiliacionTexto(d.deportista_apellido2, d.apellido2)
    );
    ficha.dep_doc_tipo = afiliacionTexto(d.deportista_tipo_documento, d.tipo_documento);
    ficha.dep_doc_num = afiliacionTexto(d.deportista_identificacion, d.identificacion);
    ficha.fecha_nac = afiliacionTexto(d.deportista_fecha_nacimiento, d.fecha_nacimiento);
    ficha.genero = afiliacionTexto(d.deportista_genero, d.genero);
    ficha.eps = afiliacionTexto(d.deportista_eps, d.eps);
    ficha.rh = afiliacionTexto(d.deportista_rh, d.rh);
    ficha.categoria = afiliacionTexto(d.categoria_nombre, '');
    ficha.alergias = afiliacionTexto(d.deportista_alergias, d.alergias);
    ficha.contacto_nombre = afiliacionTexto(d.deportista_contacto_emergencia, d.contacto_emergencia_nombre);
    ficha.contacto_tel = afiliacionTexto(d.deportista_telefono_emergencia, d.contacto_emergencia_telefono);
    ficha.direccion = afiliacionTexto(d.deportista_direccion, '');
    ficha.celular = afiliacionTexto(d.deportista_celular, '');
    ficha.correo = afiliacionTexto(d.deportista_correo, '');
    ficha.observaciones = afiliacionTexto(d.observaciones, '');
    ficha.estado = afiliacionTexto(d.estado, '');
    ficha.fecha_solicitud = afiliacionTexto(d.fecha_solicitud, d.created_at);

    // Datos del acudiente (forma admin o forma acudiente)
    if (acu !== null) {
        ficha.acu_nombre = afiliacionUnirNombres(acu.nombre1, acu.nombre2, acu.apellido1, acu.apellido2);
        ficha.acu_doc_tipo = afiliacionTexto(acu.tipo_documento, '');
        ficha.acu_doc_num = afiliacionTexto(acu.identificacion, '');
        ficha.acu_cel = afiliacionTexto(acu.celular, '');
        ficha.acu_correo = afiliacionTexto(acu.correo, '');
        ficha.acu_dir = afiliacionTexto(acu.direccion, '');
        ficha.parentesco = afiliacionTexto(d.parentesco, 'Acudiente');
    } else {
        ficha.acu_nombre = afiliacionUnirNombres(d.acudiente_nombre1, d.acudiente_nombre2, d.acudiente_apellido1, d.acudiente_apellido2);
        ficha.acu_doc_tipo = afiliacionTexto(d.acudiente_tipo_documento, '');
        ficha.acu_doc_num = afiliacionTexto(d.acudiente_identificacion, '');
        ficha.acu_cel = afiliacionTexto(d.acudiente_celular, '');
        ficha.acu_correo = afiliacionTexto(d.acudiente_correo, '');
        ficha.acu_dir = afiliacionTexto(d.acudiente_direccion, '');
        ficha.parentesco = afiliacionTexto(d.acudiente_parentesco, 'Acudiente');
    }

    // Documentos adjuntos (misma forma en ambos casos)
    ficha.documentos = [];
    if (d.documentos && d.documentos.length) {
        ficha.documentos = d.documentos;
    }

    // Firma del acudiente (solo llega en la vista admin)
    ficha.firma_acudiente = null;
    if (d.firma_acudiente && typeof d.firma_acudiente === 'object') {
        ficha.firma_acudiente = d.firma_acudiente;
    }

    return ficha;
}

// 12. Armar una fila de documento para la ficha (backticks)
function afiliacionCrearFilaDocFicha(doc) {
    var tipo = afiliacionEsc(doc.tipo_nombre);
    var archivo = afiliacionEsc(doc.archivo);
    var original = afiliacionEsc(afiliacionTexto(doc.archivo_original, ''));
    var estado = afiliacionEsc(afiliacionTexto(doc.estado, ''));
    var detalleHtml = '';
    if (original !== '') {
        detalleHtml = `<small class="text-muted d-block">${original}</small>`;
    }
    return `
    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
        <div>
            <strong>${tipo}</strong>
            ${detalleHtml}
        </div>
        <div>
            <span class="badge bg-light text-dark me-2">${estado}</span>
            <a href="${archivo}" target="_blank" class="btn btn-sm btn-primary btn-afili-accion" aria-label="Ver documento ${tipo}"><i class="ri-eye-line me-1"></i>Ver</a>
        </div>
    </li>`;
}

// 13. Constructor UNICO de la ficha de afiliacion (lo usan el admin y el acudiente)
// Si es_admin es true muestra el bloque de dictamen; si no, el de observaciones del club
function afiliacionArmarFichaHtml(ficha, es_admin) {
    var depNombre = afiliacionEsc(afiliacionTexto(ficha.dep_nombre, 'Sin nombre'));
    var depDoc = afiliacionEsc(afiliacionTexto(ficha.dep_doc_tipo, '') + ' ' + afiliacionTexto(ficha.dep_doc_num, ''));
    var depFechaNac = afiliacionEsc(afiliacionTexto(ficha.fecha_nac, '-'));
    var depGenero = 'Otro';
    if (ficha.genero === 'F') { depGenero = 'Femenino'; }
    if (ficha.genero === 'M') { depGenero = 'Masculino'; }
    var depEps = afiliacionEsc(afiliacionTexto(ficha.eps, '-'));
    var depRh = afiliacionEsc(afiliacionTexto(ficha.rh, '-'));
    var depCat = afiliacionEsc(afiliacionTexto(ficha.categoria, '-'));
    var depAlergias = afiliacionEsc(afiliacionTexto(ficha.alergias, 'Ninguna'));
    var depContacto = afiliacionEsc(afiliacionTexto(ficha.contacto_nombre, '-') + ' (' + afiliacionTexto(ficha.contacto_tel, '-') + ')');
    var depObs = afiliacionEsc(afiliacionTexto(ficha.observaciones, 'Sin observaciones'));
    var fechaSol = afiliacionEsc(afiliacionTexto(ficha.fecha_solicitud, '-'));
    var badgeEstado = afiliacionBadgeEstado(ficha.estado);

    // Filas opcionales: solo salen si el dato existe (el admin las trae, el acudiente no)
    var filasExtraDep = '';
    if (ficha.celular !== '') {
        filasExtraDep += `<tr><td class="text-muted">Celular:</td><td>${afiliacionEsc(ficha.celular)}</td></tr>`;
    }
    if (ficha.correo !== '') {
        filasExtraDep += `<tr><td class="text-muted">Correo:</td><td>${afiliacionEsc(ficha.correo)}</td></tr>`;
    }
    if (ficha.direccion !== '') {
        filasExtraDep += `<tr><td class="text-muted">Direccion:</td><td>${afiliacionEsc(ficha.direccion)}</td></tr>`;
    }

    // Datos del acudiente
    var acuNombre = afiliacionEsc(afiliacionTexto(ficha.acu_nombre, 'Sin nombre'));
    var acuDoc = afiliacionEsc(afiliacionTexto(ficha.acu_doc_tipo, '') + ' ' + afiliacionTexto(ficha.acu_doc_num, ''));
    var acuTel = afiliacionEsc(afiliacionTexto(ficha.acu_cel, '-'));
    var acuCorreo = afiliacionEsc(afiliacionTexto(ficha.acu_correo, '-'));
    var parentesco = afiliacionEsc(ficha.parentesco);
    var filaDirAcu = '';
    if (ficha.acu_dir !== '') {
        filaDirAcu = `<tr><td class="text-muted">Direccion:</td><td>${afiliacionEsc(ficha.acu_dir)}</td></tr>`;
    }

    // Documentos adjuntos
    var docsHtml = '';
    if (ficha.documentos.length === 0) {
        docsHtml = '<li class="list-group-item text-muted">No hay documentos adjuntos a esta solicitud.</li>';
    } else {
        for (var i = 0; i < ficha.documentos.length; i++) {
            docsHtml += afiliacionCrearFilaDocFicha(ficha.documentos[i]);
        }
    }

    // Bloque final: dictamen para el admin, observaciones para el acudiente
    var bloqueFinal = '';
    if (es_admin) {
        bloqueFinal = `
        <div class="col-12">
            <div class="card border border-warning-subtle">
                <div class="card-header bg-warning-subtle text-dark">
                    <h6 class="mb-0"><i class="ri-feedback-line me-1"></i> Observaciones y Dictamen Administrativo</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${depObs}</p>
                </div>
            </div>
        </div>`;
    } else {
        bloqueFinal = `
        <div class="col-12">
            <div class="alert alert-secondary mb-0">
                <strong>Observaciones del Club:</strong><br>
                ${depObs}
            </div>
        </div>`;
    }

    // Bloque de firma del acudiente (solo visible para el admin revisor)
    var bloqueFirmaAdmin = '';
    if (es_admin && ficha.firma_acudiente && ficha.firma_acudiente.data_url) {
        var firmaDataUrl = ficha.firma_acudiente.data_url;
        var firmaFecha = afiliacionEsc(ficha.firma_acudiente.fecha_firma || '');
        bloqueFirmaAdmin = `
        <div class="col-12">
            <div class="card border">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 text-primary">
                        <i class="ri-pen-nib-line me-1"></i> Firma Manuscrita del Acudiente
                    </h6>
                    <small class="text-muted">Fecha de firma: ${firmaFecha}</small>
                </div>
                <div class="card-body text-center bg-light-subtle">
                    <img src="${firmaDataUrl}" class="img-fluid border rounded bg-white p-2" style="max-height: 160px;" alt="Firma del acudiente">
                    <p class="text-muted small mt-2 mb-0">Esta firma respalda la aceptación de las políticas del club y el registro de la deportista.</p>
                </div>
            </div>
        </div>`;
    } else if (es_admin) {
        bloqueFirmaAdmin = `
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                <i class="ri-alert-line me-1"></i> <strong>Sin firma registrada:</strong> esta solicitud aún no cuenta con firma electrónica del acudiente.
            </div>
        </div>`;
    }

    return `
    <div class="row g-3">
        <div class="col-12 d-flex justify-content-between align-items-center pb-2 border-bottom">
            <div>
                <h4 class="mb-0 text-primary">${depNombre}</h4>
                <small class="text-muted">Documento: ${depDoc} &middot; Solicitado: ${fechaSol}</small>
            </div>
            <div>${badgeEstado}</div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-user-smile-line me-1"></i> Ficha del Deportista</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Documento:</td><td class="fw-medium">${depDoc}</td></tr>
                        <tr><td class="text-muted">Nacimiento:</td><td>${depFechaNac}</td></tr>
                        <tr><td class="text-muted">Genero:</td><td>${depGenero}</td></tr>
                        <tr><td class="text-muted">Categoria:</td><td class="fw-medium">${depCat}</td></tr>
                        <tr><td class="text-muted">EPS / SISBEN:</td><td>${depEps}</td></tr>
                        <tr><td class="text-muted">Grupo RH:</td><td>${depRh}</td></tr>
                        ${filasExtraDep}
                        <tr><td class="text-muted">Alergias:</td><td>${depAlergias}</td></tr>
                        <tr><td class="text-muted">Emergencia:</td><td>${depContacto}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-parent-line me-1"></i> Informacion del Acudiente</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Nombre:</td><td class="fw-medium">${acuNombre}</td></tr>
                        <tr><td class="text-muted">Parentesco:</td><td class="text-capitalize">${parentesco}</td></tr>
                        <tr><td class="text-muted">Documento:</td><td>${acuDoc}</td></tr>
                        <tr><td class="text-muted">Celular:</td><td>${acuTel}</td></tr>
                        <tr><td class="text-muted">Correo:</td><td>${acuCorreo}</td></tr>
                        ${filaDirAcu}
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-folder-shield-line me-1"></i> Documentos Verificables</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        ${docsHtml}
                    </ul>
                </div>
            </div>
        </div>

        ${bloqueFirmaAdmin}

        ${bloqueFinal}
    </div>`;
}
</script>