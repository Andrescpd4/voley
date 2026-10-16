<?php
// ============================================================
// EVENTOS — Orquestador del modulo (Admin 1, 4 y Entrenador 2)
//
// Reglas AGENTS.md (§10 y §11):
//   - Sin page-title-box ni h4: el titulo global lo pinta cabeza.php
//   - JS compartido ANTES de los includes (hoisting)
//   - Cero emojis
//   - Plantillas con backticks y bucles for tradicionales
// ============================================================

// 1. Validar roles con permiso
if (isset($_SESSION['usuario_rol'])) {
    $rol_actual = intval($_SESSION['usuario_rol']);
} else {
    $rol_actual = 0;
}

$tiene_permiso_modulo = false;
if ($rol_actual === 1) {
    $tiene_permiso_modulo = true;
} else if ($rol_actual === 4) {
    $tiene_permiso_modulo = true;
} else if ($rol_actual === 2) {
    $tiene_permiso_modulo = true;
}

if (!$tiene_permiso_modulo) {
    echo '<div class="alert alert-danger m-3">No tiene permisos para acceder al módulo de Eventos.</div>';
    return;
}
?>

<!-- ============================================================ -->
<!-- JS COMPARTIDO DEL MODULO (antes de los tabs por hoisting)   -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Peticion AJAX JSON con Authorization
function eveAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    eveMostrarMsg(respuesta.msg, 'error');
                }
                if (callback) {
                    callback(respuesta);
                }
            } catch (e) {
                console.error('Error parseando JSON:', e, xhr.responseText);
                eveMostrarMsg('Error al procesar la respuesta del servidor', 'error');
                if (callback) {
                    callback({ error: true, msg: 'Error al procesar la respuesta' });
                }
            }
        } else {
            console.error('Error HTTP:', xhr.status, xhr.responseText);
            eveMostrarMsg('Error en el servidor (' + xhr.status + ')', 'error');
            if (callback) {
                callback({ error: true, msg: 'Error en el servidor' });
            }
        }
    };

    xhr.onerror = function() {
        eveMostrarMsg('Error de conexion con el servidor', 'error');
        if (callback) {
            callback({ error: true, msg: 'Error de conexion' });
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

// 2. Escape HTML contra XSS
function eveEsc(valor) {
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

// 3. Notificaciones Toastify o Swal con colores Velzon
function eveMostrarMsg(texto, tipo) {
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
        if (tipo === 'error') {
            Swal.fire({ text: texto, icon: 'error', toast: true, position: 'top-end', timer: 3500, showConfirmButton: false });
        } else {
            Swal.fire({ text: texto, icon: 'success', toast: true, position: 'top-end', timer: 3500, showConfirmButton: false });
        }
    } else {
        alert(texto);
    }
}

// 4. Badge segun tipo de evento
function eveBadgeTipo(tipo) {
    if (tipo === 'torneo') {
        return '<span class="badge bg-primary-subtle text-primary"><i class="ri-trophy-line me-1"></i> Torneo</span>';
    } else if (tipo === 'entrenamiento') {
        return '<span class="badge bg-info-subtle text-info"><i class="ri-run-line me-1"></i> Entrenamiento</span>';
    } else if (tipo === 'salida') {
        return '<span class="badge bg-success-subtle text-success"><i class="ri-map-pin-line me-1"></i> Salida</span>';
    } else if (tipo === 'viaje') {
        return '<span class="badge bg-warning-subtle text-warning"><i class="ri-flight-takeoff-line me-1"></i> Viaje</span>';
    } else if (tipo === 'reunion') {
        return '<span class="badge bg-secondary-subtle text-secondary"><i class="ri-team-line me-1"></i> Reunión</span>';
    } else if (tipo === 'actividad') {
        return '<span class="badge bg-purple-subtle text-purple" style="background-color: rgba(194,69,139,0.15); color: #c2458b;"><i class="ri-star-line me-1"></i> Actividad</span>';
    } else {
        return '<span class="badge bg-light text-dark"><i class="ri-calendar-event-line me-1"></i> ' + eveEsc(tipo) + '</span>';
    }
}

// 5. Badge segun estado de convocatoria
function eveBadgeEstadoConvocada(estado) {
    if (estado === 'convocada') {
        return '<span class="badge bg-primary-subtle text-primary">Convocada</span>';
    } else if (estado === 'confirmada') {
        return '<span class="badge bg-success-subtle text-success">Confirmada</span>';
    } else if (estado === 'rechazada') {
        return '<span class="badge bg-danger-subtle text-danger">Rechazada</span>';
    } else if (estado === 'asistio') {
        return '<span class="badge bg-info-subtle text-info">Asistió</span>';
    } else if (estado === 'no_asistio') {
        return '<span class="badge bg-secondary-subtle text-secondary">No Asistió</span>';
    } else {
        return '<span class="badge bg-light text-dark">' + eveEsc(estado) + '</span>';
    }
}

// 6. Formatear fecha legible (DD/MM/AAAA)
function eveFormatearFecha(fecha_iso) {
    if (!fecha_iso) {
        return '-';
    }
    var partes = fecha_iso.split('-');
    if (partes.length === 3) {
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }
    return fecha_iso;
}
</script>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL CON PESTAÑAS                           -->
<!-- ============================================================ -->
<div class="tab-content">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="card-body">
                    <!-- Nav Tabs estilo Velzon -->
                    <ul class="nav nav-tabs mb-3" id="eventosTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="tab-lista-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-lista-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-lista-content"
                                aria-selected="true">
                                <i class="ri-calendar-event-line me-1"></i> Eventos y Torneos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-convocadas-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-convocadas-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-convocadas-content"
                                aria-selected="false">
                                <i class="ri-team-line me-1"></i> Convocatorias
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-autorizaciones-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-autorizaciones-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-autorizaciones-content"
                                aria-selected="false">
                                <i class="ri-shield-check-line me-1"></i> Autorizaciones
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las pestanas -->
                    <div class="tab-content" id="eventosTabsContent">
                        <div class="tab-pane fade show active"
                             id="tab-lista-content"
                             role="tabpanel"
                             aria-labelledby="tab-lista-link">
                            <?php include_once 'tabs/lista.php'; ?>
                        </div>
                        <div class="tab-pane fade"
                             id="tab-convocadas-content"
                             role="tabpanel"
                             aria-labelledby="tab-convocadas-link">
                            <?php include_once 'tabs/convocadas.php'; ?>
                        </div>
                        <div class="tab-pane fade"
                             id="tab-autorizaciones-content"
                             role="tabpanel"
                             aria-labelledby="tab-autorizaciones-link">
                            <?php include_once 'tabs/autorizaciones.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
