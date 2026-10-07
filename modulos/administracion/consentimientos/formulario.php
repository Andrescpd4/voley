<?php
// ============================================================
// CONSENTIMIENTOS — Orquestador de pestanas (solo admin 1 y 4)
//
// Reglas AGENTS.md (§10 y §11):
//   - Sin page-title-box ni h4: el titulo global lo pinta cabeza.php
//   - JS compartido ANTES de los includes (hoisting)
//   - Cero emojis
// ============================================================

// 1. Solo administradores pueden ver este modulo
if (isset($_SESSION['usuario_rol'])) {
    $rol_actual = intval($_SESSION['usuario_rol']);
} else {
    $rol_actual = 0;
}

if ($rol_actual === 1) {
    $es_admin_cons = true;
} else if ($rol_actual === 4) {
    $es_admin_cons = true;
} else {
    $es_admin_cons = false;
}

if (!$es_admin_cons) {
    echo '<div class="alert alert-danger m-3">No tiene permisos para acceder al módulo de Consentimientos.</div>';
    return;
}
?>

<!-- ============================================================ -->
<!-- JS COMPARTIDO DEL MODULO (antes de los tabs por hoisting)   -->
<!-- ============================================================ -->
<script type="text/javascript">
// 1. Peticion AJAX JSON con Authorization
function consAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    consMostrarMsg(respuesta.msg, 'error');
                }
                if (callback) {
                    callback(respuesta);
                }
            } catch (e) {
                console.error('Error parseando JSON:', e, xhr.responseText);
                consMostrarMsg('Error al procesar la respuesta del servidor', 'error');
                if (callback) {
                    callback({ error: true, msg: 'Error al procesar la respuesta' });
                }
            }
        } else {
            console.error('Error HTTP:', xhr.status, xhr.responseText);
            consMostrarMsg('Error en el servidor (' + xhr.status + ')', 'error');
            if (callback) {
                callback({ error: true, msg: 'Error en el servidor' });
            }
        }
    };

    xhr.onerror = function() {
        consMostrarMsg('Error de conexion con el servidor', 'error');
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
function consEsc(valor) {
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

// 3. Notificaciones Toastify con colores Velzon
function consMostrarMsg(texto, tipo) {
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

// 4. Badge de clase con variantes suaves (texto oscuro legible)
function consBadgeClase(clase) {
    if (clase === 'obligatorio') {
        return '<span class="badge bg-danger-subtle text-danger">Obligatorio</span>';
    } else {
        return '<span class="badge bg-info-subtle text-info">Informativo</span>';
    }
}

// 5. Badge de estado si/no con variantes suaves
function consBadgeActivo(activo) {
    if (parseInt(activo) === 1) {
        return '<span class="badge bg-success-subtle text-success">Activo</span>';
    } else {
        return '<span class="badge bg-secondary-subtle text-secondary">Inactivo</span>';
    }
}
</script>

<!-- ============================================================ -->
<!-- CONTENEDOR PRINCIPAL CON PESTANAS                           -->
<!-- ============================================================ -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav Tabs estilo Velzon -->
                    <ul class="nav nav-tabs mb-3" id="consentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="tab-tipos-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-tipos-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-tipos-content"
                                aria-selected="true">
                                <i class="ri-file-text-line me-1"></i> Documentos de consentimiento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-historial-link"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-historial-content"
                                type="button"
                                role="tab"
                                aria-controls="tab-historial-content"
                                aria-selected="false">
                                <i class="ri-history-line me-1"></i> Historial de firmas
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las pestanas -->
                    <div class="tab-content" id="consentTabsContent">
                        <div class="tab-pane fade show active"
                             id="tab-tipos-content"
                             role="tabpanel"
                             aria-labelledby="tab-tipos-link">
                            <?php include_once 'tabs/tipos.php'; ?>
                        </div>
                        <div class="tab-pane fade"
                             id="tab-historial-content"
                             role="tabpanel"
                             aria-labelledby="tab-historial-link">
                            <?php include_once 'tabs/historial.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
