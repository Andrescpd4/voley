<?php
// ============================================================
// ASISTENCIA — Orquestador del modulo
//
// Reglas AGENTS.md (§10):
//   - JS compartido ANTES de los includes (hoisting)
//   - Plantillas con backticks y bucles for tradicionales
//   - Cero emojis
//   - Staff (1, 4, 2): sesion, clases y consolidado
//   - Acudiente (3): solo resumen de sus hijos
// ============================================================

// 1. Rol de la sesion actual
if (isset($_SESSION['usuario_rol'])) {
    $rol_actual = intval($_SESSION['usuario_rol']);
} else {
    $rol_actual = 0;
}

// 2. Definir que pestanas puede ver segun el rol
$es_staff = false;
if ($rol_actual === 1) {
    $es_staff = true;
}
if ($rol_actual === 4) {
    $es_staff = true;
}
if ($rol_actual === 2) {
    $es_staff = true;
}
$es_acudiente = false;
if ($rol_actual === 3) {
    $es_acudiente = true;
}

if (!$es_staff && !$es_acudiente) {
    echo '<div class="alert alert-danger m-3">No tiene permisos para acceder al modulo de Asistencia.</div>';
    return;
}
?>

<!-- ============================================================ -->
<!-- JS COMPARTIDO DEL MODULO (antes de los tabs por hoisting)   -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Peticion AJAX JSON con Authorization
function asiAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    asiMostrarMsg(respuesta.msg, 'error');
                }
                if (callback) {
                    callback(respuesta);
                }
            } catch (e) {
                asiMostrarMsg('Error al procesar la respuesta del servidor', 'error');
                if (callback) {
                    callback({ error: true, msg: 'Error al procesar la respuesta' });
                }
            }
        } else {
            asiMostrarMsg('Error en el servidor (' + xhr.status + ')', 'error');
            if (callback) {
                callback({ error: true, msg: 'Error en el servidor' });
            }
        }
    };

    xhr.onerror = function() {
        asiMostrarMsg('Error de conexion con el servidor', 'error');
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
function asiEsc(valor) {
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
function asiMostrarMsg(texto, tipo) {
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

// 4. Badge segun estado de asistencia
function asiBadgeEstado(estado) {
    if (estado === 'asistio') {
        return '<span class="badge bg-success-subtle text-success">Asistio</span>';
    } else if (estado === 'tarde') {
        return '<span class="badge bg-warning-subtle text-warning">Llegada tarde</span>';
    } else if (estado === 'excusa') {
        return '<span class="badge bg-info-subtle text-info">Excusa</span>';
    } else if (estado === 'no_asistio') {
        return '<span class="badge bg-danger-subtle text-danger">No asistio</span>';
    } else {
        return '<span class="badge bg-light text-dark">' + asiEsc(estado) + '</span>';
    }
}

// 5. Formatear fecha ISO (AAAA-MM-DD) a DD/MM/AAAA
function asiFormatearFecha(fecha_iso) {
    if (!fecha_iso) {
        return '-';
    }
    var partes = fecha_iso.split('-');
    if (partes.length === 3) {
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }
    return fecha_iso;
}

// 6. Extraer solo hora y minutos de un DATETIME (AAAA-MM-DD HH:MM:SS)
function asiFormatearHora(fecha_hora) {
    if (!fecha_hora) {
        return '-';
    }
    var partes = fecha_hora.split(' ');
    if (partes.length === 2) {
        var hm = partes[1].split(':');
        return hm[0] + ':' + hm[1];
    }
    return fecha_hora;
}

// 7. Minutos entre dos DATETIME para el cronometro en vivo
function asiMinutosEntre(inicio_txt, fin_txt) {
    var inicio = new Date(inicio_txt.replace(' ', 'T'));
    var fin = new Date(fin_txt.replace(' ', 'T'));
    var dif = fin - inicio;
    if (isNaN(dif)) {
        return 0;
    }
    if (dif < 0) {
        return 0;
    }
    return Math.floor(dif / 60000);
}

// 8. Hora actual del navegador en formato DATETIME para el cronometro
function asiAhoraTexto() {
    var ahora = new Date();
    var anio = ahora.getFullYear();
    var mes = ahora.getMonth() + 1;
    var dia = ahora.getDate();
    var hora = ahora.getHours();
    var min = ahora.getMinutes();
    var seg = ahora.getSeconds();
    if (mes < 10) { mes = '0' + mes; }
    if (dia < 10) { dia = '0' + dia; }
    if (hora < 10) { hora = '0' + hora; }
    if (min < 10) { min = '0' + min; }
    if (seg < 10) { seg = '0' + seg; }
    return anio + '-' + mes + '-' + dia + ' ' + hora + ':' + min + ':' + seg;
}
</script>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL CON PESTAÑAS                           -->
<!-- ============================================================ -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav Tabs estilo Velzon -->
                    <ul class="nav nav-tabs mb-3" id="asistenciaTabs" role="tablist">
                        <?php if ($es_staff) { ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="tab-sesion-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-sesion-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-sesion-content"
                                aria-selected="true">
                                <i class="ri-qr-scan-2-line me-1"></i> Sesion en vivo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-clases-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-clases-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-clases-content"
                                aria-selected="false">
                                <i class="ri-calendar-check-line me-1"></i> Jornadas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-consolidado-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-consolidado-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-consolidado-content"
                                aria-selected="false">
                                <i class="ri-bar-chart-line me-1"></i> Consolidado
                            </button>
                        </li>
                        <?php } ?>
                        <?php if ($es_acudiente || $rol_actual === 1 || $rol_actual === 4) { ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php if (!$es_staff) { echo 'active'; } ?>"
                                id="tab-hijos-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-hijos-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-hijos-content"
                                aria-selected="false">
                                <i class="ri-parent-line me-1"></i> Mis hijos
                            </button>
                        </li>
                        <?php } ?>
                    </ul>

                    <!-- Contenido de las pestanas -->
                    <div class="tab-content" id="asistenciaTabsContent">
                        <?php if ($es_staff) { ?>
                        <div class="tab-pane fade show active"
                             id="tab-sesion-content"
                             role="tabpanel"
                             aria-labelledby="tab-sesion-link">
                            <?php include_once 'tabs/sesion.php'; ?>
                        </div>
                        <div class="tab-pane fade"
                             id="tab-clases-content"
                             role="tabpanel"
                             aria-labelledby="tab-clases-link">
                            <?php include_once 'tabs/clases.php'; ?>
                        </div>
                        <div class="tab-pane fade"
                             id="tab-consolidado-content"
                             role="tabpanel"
                             aria-labelledby="tab-consolidado-link">
                            <?php include_once 'tabs/consolidado.php'; ?>
                        </div>
                        <?php } ?>
                        <?php if ($es_acudiente || $rol_actual === 1 || $rol_actual === 4) { ?>
                        <div class="tab-pane fade <?php if (!$es_staff) { echo 'show active'; } ?>"
                             id="tab-hijos-content"
                             role="tabpanel"
                             aria-labelledby="tab-hijos-link">
                            <?php include_once 'tabs/mis_hijos.php'; ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
