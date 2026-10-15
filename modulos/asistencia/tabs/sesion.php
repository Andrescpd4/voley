<?php
// ============================================================
// ASISTENCIA — Tab Sesion en vivo (staff: admin y entrenador)
//
// Primer escaneo = entrada (empieza a sumar tiempo).
// Segundo escaneo = salida (se detiene y guarda las horas).
// Finalizar cierra la clase y pone inasistencia a pendientes.
//
// Reglas AGENTS.md (§10):
//   - Plantillas con backticks
//   - Una funcion pequena por pieza de HTML
//   - Bucles for tradicionales y var en espanol
// ============================================================
?>

<!-- Libreria de escaneo (local, lee QR y barras Code128/EAN) -->
<script src="<?php echo WEB_ROOT; ?>plantilla/assets/libs/html5-qrcode/html5-qrcode.min.js"></script>

<!-- Iniciar la jornada de hoy: tema + categoria -->
<div class="card border mb-3" style="border-style: dashed;">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-muted small mb-1">De que trata la jornada</label>
                <input type="text" id="asiTema" class="form-control" value="Entrenamiento" maxlength="100">
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Categoria</label>
                <select id="asiCategoriaJornada" class="form-select">
                    <option value="0">Seleccione la categoria...</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success w-100 accion-iniciar_jornada" onclick="asiIniciarJornada()">
                    <i class="ri-play-line me-1"></i> Iniciar jornada
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Selector de la jornada en curso -->
<div class="row g-3 mb-3 align-items-end">
    <div class="col-md-6">
        <label class="form-label text-muted small mb-1">Clase o entrenamiento en curso</label>
        <select id="asiSesionClase" class="form-select" onchange="asiCargarSesion()">
            <option value="0">Cargando clases...</option>
        </select>
    </div>
    <div class="col-md-3">
        <div class="d-flex gap-2">
            <div class="border rounded px-3 py-2 text-center flex-fill">
                <small class="text-muted d-block">Presentes</small>
                <span class="fw-bold fs-18 text-success" id="asiContadorPresentes">0</span>
            </div>
            <div class="border rounded px-3 py-2 text-center flex-fill">
                <small class="text-muted d-block">Pendientes</small>
                <span class="fw-bold fs-18 text-warning" id="asiContadorPendientes">0</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 text-md-end">
        <button type="button" class="btn btn-danger accion-finalizar" onclick="asiFinalizarClase()">
            <i class="ri-flag-line me-1"></i> Finalizar entrenamiento
        </button>
    </div>
</div>

<!-- Ultimo resultado del escaneo -->
<div id="asiUltimoResultado" class="mb-3"></div>

<div class="row g-3">
    <!-- Columna izquierda: camara y registro manual -->
    <div class="col-lg-5">
        <div class="card border mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="ri-camera-line me-1"></i> Escanear carnet o codigo</h6>
            </div>
            <div class="card-body">
                <!-- Vista previa de la camara -->
                <div id="asiLector" class="border rounded mb-3" style="min-height: 120px;"></div>
                <div class="d-flex gap-2 mb-3">
                    <button type="button" id="asiBtnIniciarCamara" class="btn btn-primary btn-sm accion-marcar" onclick="asiIniciarCamara()">
                        <i class="ri-play-line me-1"></i> Iniciar camara
                    </button>
                    <button type="button" id="asiBtnDetenerCamara" class="btn btn-light btn-sm d-none" onclick="asiDetenerCamara()">
                        <i class="ri-stop-line me-1"></i> Detener
                    </button>
                </div>
                <small class="text-muted d-block">La camara necesita permiso del navegador y conexion segura (HTTPS). Si no abre, use el registro manual.</small>
            </div>
        </div>

        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="ri-keyboard-line me-1"></i> Registro manual por cedula</h6>
            </div>
            <div class="card-body">
                <label class="form-label text-muted small">Numero de documento de la deportista</label>
                <div class="input-group">
                    <input type="text" id="asiDocumentoManual" class="form-control" placeholder="Ej: 1023456789" autocomplete="off">
                    <button type="button" class="btn btn-success accion-marcar" onclick="asiRegistrarManual()">
                        <i class="ri-check-line me-1"></i> Registrar
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Sirve cuando la deportista no trae el carnet. Marca entrada o salida igual que el escaneo.</small>
            </div>
        </div>
    </div>

    <!-- Columna derecha: presentes y pendientes -->
    <div class="col-lg-7">
        <div class="card border mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="ri-team-line me-1"></i> Presentes en la sesion</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Deportista</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Entrada</th>
                                <th scope="col">Salida</th>
                                <th scope="col">Tiempo</th>
                            </tr>
                        </thead>
                        <tbody id="asiTablaPresentes">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Seleccione una clase para ver la sesion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="ri-user-unfollow-line me-1"></i> Pendientes por llegar</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Deportista</th>
                                <th scope="col">Documento</th>
                                <th scope="col" class="text-end">Excusa</th>
                            </tr>
                        </thead>
                        <tbody id="asiTablaPendientes">
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Sin pendientes</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB SESION                                      -->
<!-- ============================================================ -->
<script type="text/javascript">
// Estado local de la sesion en curso
var asiClaseActual = 0;
var asiCamara = null;
var asiCamaraActiva = false;
var asiUltimoCodigo = '';
var asiUltimoTiempo = 0;
var asiListaPresentes = [];

// 1. Crear una fila de presente con backticks
function asiCrearFilaPresente(item) {
    var nombre = asiEsc(item.nombre_completo);
    var documento = asiEsc(item.identificacion);
    var badge = asiBadgeEstado(item.estado);
    var hora_entrada = asiFormatearHora(item.hora_entrada);
    var hora_salida = asiFormatearHora(item.hora_salida);

    // Tiempo: si ya salio muestra minutos fijos, si no muestra cronometro vivo
    var tiempo_html = '';
    if (item.hora_salida) {
        tiempo_html = `<span class="badge bg-light text-dark">${item.minutos} min</span>`;
    } else if (item.hora_entrada) {
        tiempo_html = `<span class="badge bg-primary-subtle text-primary asi-tiempo" data-entrada="${asiEsc(item.hora_entrada)}">0 min</span>`;
    } else {
        tiempo_html = '<span class="text-muted">-</span>';
    }

    return `<tr>
        <td>
            <div class="fw-semibold text-dark">${nombre}</div>
            <small class="text-muted">${documento}</small>
        </td>
        <td>${badge}</td>
        <td>${hora_entrada}</td>
        <td>${hora_salida}</td>
        <td>${tiempo_html}</td>
    </tr>`;
}

// 2. Crear una fila de pendiente con boton de excusa
function asiCrearFilaPendiente(item) {
    var nombre = asiEsc(item.nombre_completo);
    var documento = asiEsc(item.identificacion);
    var id_deportista = parseInt(item.deportista_id);

    return `<tr>
        <td><span class="fw-medium">${nombre}</span></td>
        <td><code>${documento}</code></td>
        <td class="text-end">
            <button type="button" class="btn btn-soft-info btn-sm accion-marcar_excusa" onclick="asiPedirExcusa(${id_deportista}, '${documento}')">
                <i class="ri-mail-line me-1"></i> Excusa
            </button>
        </td>
    </tr>`;
}

// 3. Renderizar presentes y pendientes en sus tablas
function asiRenderizarSesion(presentes, pendientes) {
    var cuerpo_presentes = document.getElementById('asiTablaPresentes');
    var cuerpo_pendientes = document.getElementById('asiTablaPendientes');

    // Contadores de la parte superior
    document.getElementById('asiContadorPresentes').textContent = presentes.length;
    document.getElementById('asiContadorPendientes').textContent = pendientes.length;

    // Tabla de presentes
    if (presentes.length === 0) {
        cuerpo_presentes.innerHTML = `<tr>
            <td colspan="5" class="text-center py-4 text-muted">Aun no hay marcas en esta clase</td>
        </tr>`;
    } else {
        var html_presentes = '';
        for (var i = 0; i < presentes.length; i++) {
            html_presentes = html_presentes + asiCrearFilaPresente(presentes[i]);
        }
        cuerpo_presentes.innerHTML = html_presentes;
    }

    // Tabla de pendientes
    if (pendientes.length === 0) {
        cuerpo_pendientes.innerHTML = `<tr>
            <td colspan="3" class="text-center py-4 text-muted">Todas las deportistas ya tienen marca</td>
        </tr>`;
    } else {
        var html_pendientes = '';
        for (var j = 0; j < pendientes.length; j++) {
            html_pendientes = html_pendientes + asiCrearFilaPendiente(pendientes[j]);
        }
        cuerpo_pendientes.innerHTML = html_pendientes;
    }

    // Actualizar los cronometros de inmediato
    asiActualizarTiempos();
}

// 4. Actualizar cada cronometro vivo (minutos desde la entrada)
function asiActualizarTiempos() {
    var relojes = document.getElementsByClassName('asi-tiempo');
    var ahora_txt = asiAhoraTexto();
    for (var i = 0; i < relojes.length; i++) {
        var entrada_txt = relojes[i].getAttribute('data-entrada');
        var minutos = asiMinutosEntre(entrada_txt, ahora_txt);
        relojes[i].textContent = minutos + ' min';
    }
}

// 5. Mostrar el ultimo resultado del escaneo o registro
function asiMostrarResultado(tipo, texto) {
    var caja = document.getElementById('asiUltimoResultado');
    var clase_color = 'alert-info';
    var icono = 'ri-information-line';
    if (tipo === 'entrada') {
        clase_color = 'alert-success';
        icono = 'ri-login-circle-line';
    }
    if (tipo === 'salida') {
        clase_color = 'alert-primary';
        icono = 'ri-logout-circle-line';
    }
    if (tipo === 'error') {
        clase_color = 'alert-danger';
        icono = 'ri-error-warning-line';
    }
    caja.innerHTML = `<div class="alert ${clase_color} mb-0 py-2">
        <i class="${icono} me-1"></i> ${asiEsc(texto)}
    </div>`;
}

// 6. Sonido corto de confirmacion al escanear
function asiBeep() {
    try {
        var contexto = new (window.AudioContext || window.webkitAudioContext)();
        var tono = contexto.createOscillator();
        var salida = contexto.createGain();
        tono.connect(salida);
        salida.connect(contexto.destination);
        tono.frequency.value = 880;
        tono.start();
        tono.stop(contexto.currentTime + 0.15);
    } catch (e) {
        // Si el navegador no permite audio, se sigue sin sonido
    }
}

// 7. Cargar clases abiertas en el selector de sesion
function asiCargarClasesSesion() {
    asiAjax('listar_clases', {}, function(respuesta) {
        var select = document.getElementById('asiSesionClase');
        if (respuesta.error) {
            return;
        }
        var hoy = asiAhoraTexto().slice(0, 10);
        var html_opciones = '<option value="0">Seleccione la clase...</option>';
        var id_hoy = 0;
        for (var i = 0; i < respuesta.data.length; i++) {
            var clase = respuesta.data[i];
            if (clase.estado !== 'abierta') {
                continue;
            }
            var texto = asiFormatearFecha(clase.fecha) + ' - ' + asiEsc(clase.nombre);
            if (clase.categoria_nombre) {
                texto = texto + ' (' + asiEsc(clase.categoria_nombre) + ')';
            }
            html_opciones = html_opciones + `<option value="${clase.id}">${texto}</option>`;
            if (clase.fecha === hoy && id_hoy === 0) {
                id_hoy = clase.id;
            }
        }
        select.innerHTML = html_opciones;
        // Preseleccionar la clase de hoy si existe
        if (id_hoy !== 0) {
            select.value = id_hoy;
        }
        asiCargarSesion();
    });
}

// 7b. Cargar categorias en el combo de iniciar jornada
function asiCargarCategoriasJornada() {
    asiAjax('listar_categorias', {}, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var select = document.getElementById('asiCategoriaJornada');
            var html_opciones = '<option value="0">Seleccione la categoria...</option>';
            for (var i = 0; i < respuesta.data.length; i++) {
                var cat = respuesta.data[i];
                html_opciones = html_opciones + `<option value="${cat.id}">${asiEsc(cat.nombre)}</option>`;
            }
            select.innerHTML = html_opciones;
        }
    });
}

// 7c. Iniciar la jornada de hoy con tema y categoria
function asiIniciarJornada() {
    var tema = document.getElementById('asiTema').value;
    var categoria = document.getElementById('asiCategoriaJornada').value;
    asiAjax('iniciar_jornada', { tema: tema, categoria_id: categoria }, function(respuesta) {
        if (!respuesta.error) {
            asiMostrarMsg(respuesta.msg, 'success');
            asiCargarClasesSesion();
            // Preseleccionar la jornada recien iniciada
            var select = document.getElementById('asiSesionClase');
            select.value = respuesta.id;
            asiCargarSesion();
            if (typeof asiCargarListaClases === 'function') {
                asiCargarListaClases();
            }
        }
    });
}
// 8. Cargar presentes y pendientes de la clase elegida
function asiCargarSesion() {
    var select = document.getElementById('asiSesionClase');
    asiClaseActual = parseInt(select.value);
    if (isNaN(asiClaseActual)) {
        asiClaseActual = 0;
    }
    if (asiClaseActual === 0) {
        asiListaPresentes = [];
        asiRenderizarSesion([], []);
        return;
    }
    asiAjax('sesion_en_vivo', { clase_id: asiClaseActual }, function(respuesta) {
        if (!respuesta.error) {
            asiListaPresentes = respuesta.presentes;
            asiRenderizarSesion(respuesta.presentes, respuesta.pendientes);
        }
    });
}

// 9. Recibir un codigo leido por la camara (con antirrebote)
function asiAlEscanear(texto) {
    var codigo = texto.trim();
    if (codigo === '') {
        return;
    }
    var ahora_ms = Date.now();
    if (codigo === asiUltimoCodigo && (ahora_ms - asiUltimoTiempo) < 3000) {
        return;
    }
    asiUltimoCodigo = codigo;
    asiUltimoTiempo = ahora_ms;
    asiBeep();
    asiMarcarDocumento(codigo, 'qr');
}

// 10. Enviar un documento al backend (entrada o salida automatica)
function asiMarcarDocumento(documento, origen) {
    if (asiClaseActual === 0) {
        asiMostrarResultado('error', 'Primero seleccione la clase o entrenamiento.');
        return;
    }
    asiAjax('marcar', { clase_id: asiClaseActual, documento: documento, origen: origen }, function(respuesta) {
        if (!respuesta.error) {
            asiMostrarResultado(respuesta.movimiento, respuesta.msg);
            asiMostrarMsg(respuesta.msg, 'success');
            asiCargarSesion();
        } else {
            asiMostrarResultado('error', respuesta.msg);
        }
    });
}

// 11. Registrar con la cedula escrita a mano
function asiRegistrarManual() {
    var campo = document.getElementById('asiDocumentoManual');
    var documento = campo.value.trim();
    if (documento === '') {
        asiMostrarMsg('Escriba el numero de documento.', 'warning');
        return;
    }
    asiBeep();
    asiMarcarDocumento(documento, 'manual');
    campo.value = '';
    campo.focus();
}

// 12. Iniciar la camara para escanear carnets
function asiIniciarCamara() {
    if (typeof Html5Qrcode === 'undefined') {
        asiMostrarMsg('No se pudo cargar el lector. Use el registro manual.', 'error');
        return;
    }
    if (asiCamaraActiva) {
        return;
    }
    var btn_iniciar = document.getElementById('asiBtnIniciarCamara');
    var btn_detener = document.getElementById('asiBtnDetenerCamara');
    btn_iniciar.disabled = true;

    asiCamara = new Html5Qrcode('asiLector');

    // 12a. Armar la configuracion del lector
    var config = { fps: 10, qrbox: { width: 250, height: 250 } };
    if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
        config.formatsToSupport = [
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.CODE_39
        ];
    }

    // 12b. Encender la camara trasera del dispositivo
    asiCamara.start({ facingMode: 'environment' }, config, asiAlEscanear, function(mensaje) {
        // Se ignora cada cuadro sin codigo para no llenar la consola
    }).then(function() {
        asiCamaraActiva = true;
        btn_iniciar.classList.add('d-none');
        btn_detener.classList.remove('d-none');
    }).catch(function(error) {
        btn_iniciar.disabled = false;
        asiMostrarMsg('No se pudo abrir la camara. Revise el permiso o use el registro manual.', 'error');
    });
}

// 13. Detener la camara para ahorrar bateria
function asiDetenerCamara() {
    if (!asiCamaraActiva || !asiCamara) {
        return;
    }
    var btn_iniciar = document.getElementById('asiBtnIniciarCamara');
    var btn_detener = document.getElementById('asiBtnDetenerCamara');
    asiCamara.stop().then(function() {
        asiCamara.clear();
        asiCamaraActiva = false;
        btn_iniciar.disabled = false;
        btn_iniciar.classList.remove('d-none');
        btn_detener.classList.add('d-none');
        document.getElementById('asiLector').innerHTML = '';
    }).catch(function(error) {
        asiCamaraActiva = false;
    });
}

// 14. Pedir el motivo de excusa y registrarla
function asiPedirExcusa(deportista_id, documento) {
    if (typeof Swal === 'undefined') {
        return;
    }
    Swal.fire({
        title: 'Registrar excusa',
        text: 'Documento: ' + documento,
        input: 'text',
        inputPlaceholder: 'Motivo de la excusa (opcional)',
        showCancelButton: true,
        confirmButtonText: 'Guardar excusa',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0ab39c'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            var motivo = '';
            if (resultado.value) {
                motivo = resultado.value;
            }
            asiAjax('marcar_excusa', { clase_id: asiClaseActual, deportista_id: deportista_id, observaciones: motivo }, function(respuesta) {
                if (!respuesta.error) {
                    asiMostrarMsg(respuesta.msg, 'success');
                    asiCargarSesion();
                }
            });
        }
    });
}

// 15. Finalizar el entrenamiento (cierra y pone inasistencias)
function asiFinalizarClase() {
    if (asiClaseActual === 0) {
        asiMostrarMsg('Seleccione la clase a finalizar.', 'warning');
        return;
    }
    if (typeof Swal === 'undefined') {
        return;
    }
    Swal.fire({
        title: 'Finalizar entrenamiento',
        text: 'Se cerrara la clase y las pendientes quedaran como inasistencia. Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, finalizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            asiDetenerCamara();
            asiAjax('finalizar', { clase_id: asiClaseActual }, function(respuesta) {
                if (!respuesta.error) {
                    asiMostrarMsg(respuesta.msg, 'success');
                    asiMostrarResultado('salida', respuesta.msg);
                    asiCargarClasesSesion();
                }
            });
        }
    });
}

// 16. Enter en el campo manual registra de inmediato
document.addEventListener('DOMContentLoaded', function() {
    var campo = document.getElementById('asiDocumentoManual');
    if (campo) {
        campo.addEventListener('keypress', function(evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                asiRegistrarManual();
            }
        });
    }
    asiCargarClasesSesion();
    asiCargarCategoriasJornada();
    // Refrescar cronometros cada 30 segundos
    setInterval(asiActualizarTiempos, 30000);
});
</script>
