<?php
// ============================================================
// DISENO — Formulario (Orquestador)
// ============================================================
define("MODULO", "diseno");

// Incluir CSS y JS compartidos ANTES de los tabs
?>
<style id="dynamic-theme"></style>

<script type="text/javascript">
// Funciones compartidas (hoisted)
function gcAjax(accion, datosExtra, callback) {
    var datos = {};
    if (typeof datosExtra === 'object') {
        for (var key in datosExtra) {
            datos[key] = datosExtra[key];
        }
    }
    $.ajax({
        url: page_root + accion,
        type: 'POST',
        dataType: 'json',
        data: $.param(datos),
        headers: {
            'Authorization': TOKEN_GLOBAL
        },
        success: function(r) {
            if (typeof callback === 'function') {
                callback(r);
            }
        },
        error: function() {
            alert('Error de comunicación con el servidor');
        }
    });
}

function gcEsc(html) {
    var div = document.createElement('div');
    div.textContent = html;
    return div.innerHTML;
}

function gcAplicarTema(cssTexto) {
    var style = document.getElementById('dynamic-theme');
    if (style) {
        style.innerHTML = cssTexto;
    } else {
        style = document.createElement('style');
        style.id = 'dynamic-theme';
        style.innerHTML = cssTexto;
        document.head.appendChild(style);
    }
}

function gcObtenerYAplicarTema() {
    gcAjax('obtener', {}, function(r) {
        if (r.error === false && r.data) {
            // Generar CSS a partir de los datos (lo hacemos aquí para evitar otra petición)
            var css = ':root {';
            css += '--bs-primary: ' + r.data.primaria + ';';
            css += '--bs-secondary: ' + r.data.secundaria + ';';
            css += '--bs-success: ' + r.data.exito + ';';
            css += '--bs-info: ' + r.data.info + ';';
            css += '--bs-warning: ' + r.data.advertencia + ';';
            css += '--bs-danger: ' + r.data.peligro + ';';
            css += '--bs-light: ' + r.data.claro + ';';
            css += '--bs-dark: ' + r.data.oscuro + ';';
            // Grises
            for (var i = 100; i <= 900; i += 100) {
                var valor = r.data['gris' + i];
                if (valor) {
                    css += '--bs-gray-' + i + ': ' + valor + ';';
                }
            }
            // Fuentes
            if (r.data.fuente_base) {
                css += '--bs-font-sans-serif: "' + r.data.fuente_base + '";';
            }
            if (r.data.fuente_titulo) {
                css += '--bs-font-serif: "' + r.data.fuente_titulo + '";';
            }
            css += '--bs-font-base-size: ' + (r.data.tamano_base || 1) + 'rem;';
            css += '--bs-spacing-unit: ' + (r.data.escala_espaciado || 1) + 'rem;';
            css += '}';
            // Modo oscuro: si está activado, agregar clase al html
            if (r.data.dark_mode == 1) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            gcAplicarTema(css);
            // Llenar el formulario con los datos obtenidos
            gcRellenarFormulario(r.data);
        }
    });
}

function gcRellenarFormulario(data) {
    // Colores
    var camposColor = ['primaria','secundaria','exito','info','advertencia','peligro','claro','oscuro'];
    for (var i = 0; i < camposColor.length; i++) {
        var campo = camposColor[i];
        var input = document.getElementById(campo);
        if (input && data[campo]) {
            input.value = data[campo];
        }
    }
    // Grises
    for (var i = 100; i <= 900; i += 100) {
        var campo = 'gris' + i;
        var input = document.getElementById(campo);
        if (input && data[campo]) {
            input.value = data[campo];
        }
    }
    // Fuentes
    if (data.fuente_base) {
        document.getElementById('fuente_base').value = data.fuente_base;
    }
    if (data.fuente_titulo) {
        document.getElementById('fuente_titulo').value = data.fuente_titulo;
    }
    // Tamaño base
    if (data.tamano_base !== null && data.tamano_base !== '') {
        document.getElementById('tamano_base').value = data.tamano_base;
    }
    // Escala de espaciado
    if (data.escala_espaciado !== null && data.escala_espaciado !== '') {
        document.getElementById('escala_espaciado').value = data.escala_espaciado;
    }
    // Dark mode
    if (data.dark_mode !== null && data.dark_mode !== '') {
        document.getElementById('dark_mode').checked = (data.dark_mode == 1);
    }
}

// Función para obtener todos los datos del formulario
function getFormData() {
    var form = document.getElementById('form-diseno');
    if (!form) return {};
    var elementos = form.elements;
    var datos = {};
    for (var i = 0; i < elementos.length; i++) {
        var elemento = elementos[i];
        var nombre = elemento.name;
        if (!nombre) continue;
        var tipo = elemento.type;
        if (tipo === 'checkbox') {
            datos[nombre] = elemento.checked ? 1 : 0;
        } else if (tipo === 'radio') {
            if (elemento.checked) {
                datos[nombre] = elemento.value;
            }
        } else if (tipo === 'file') {
            // Ignorar archivos por ahora
        } else if (elemento.tagName.toLowerCase() === 'select' && elemento.multiple) {
            // Manejar selects múltiples
            var opciones = [];
            for (var j = 0; j < elemento.options.length; j++) {
                if (elemento.options[j].selected) {
                    opciones.push(elemento.options[j].value);
                }
            }
            datos[nombre] = opciones;
        } else {
            datos[nombre] = elemento.value;
        }
    }
    return datos;
}

// Inicializar al cargar
jQuery(document).ready(function($) {
    gcObtenerYAplicarTema();
});

// Función para guardar tema desde los tabs
function gcGuardarTema(datos, callback) {
    gcAjax('guardar', datos, function(r) {
        if (r.error === false) {
            alert('Tema guardado con éxito');
        } else {
            alert('Error: ' + r.msg);
        }
        if (callback) callback(r);
    });
}
</script>

<?php
// Ahora incluir el formulario específico (el contenido real)
include_once 'formulario_content.php';
?>