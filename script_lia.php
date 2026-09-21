<?php
// ============================================================
// SCRIPT_LIA.PHP — Scripts globales personalizados
// Se incluye en cabeza.php
// ============================================================
?>
<!-- Scripts personalizados globales -->
<script>
    // Funciones utilitarias globales
    window.VoleyPlus = {
        // Mostrar toast
        toast: function(mensaje, tipo = 'info') {
            if (typeof Toastify !== 'undefined') {
                var colores = {
                    'exito': 'linear-gradient(to right, #00b09b, #96c93d)',
                    'error': 'linear-gradient(to right, #ff5f6d, #ffc371)',
                    'info':  'linear-gradient(to right, #1e88e5, #42a5f5)',
                    'aviso': 'linear-gradient(to right, #f7971e, #ffd200)'
                };
                Toastify({
                    text: mensaje,
                    duration: 5000,
                    gravity: 'top',
                    position: 'right',
                    style: { background: colores[tipo] || colores['info'] },
                    close: true
                }).showToast();
            }
        },
        
        // Confirmación SweetAlert2
        confirmar: function(titulo, texto, callback) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed && callback) callback();
            });
        },
        
        // AJAX genérico
        ajax: function(url, datos, callback, metodo = 'POST') {
            var fd = new FormData();
            for (var key in datos) {
                if (Array.isArray(datos[key])) {
                    datos[key].forEach(function(v) { fd.append(key + '[]', v); });
                } else {
                    fd.append(key, datos[key]);
                }
            }
            fetch(url, {
                method: metodo,
                headers: { 'Authorization': localStorage.getItem('stp_k_l_t') || '' },
                body: fd
            })
            .then(function(r) { return r.json(); })
            .then(function(data) { if (callback) callback(data); })
            .catch(function(e) { console.error(e); VoleyPlus.toast('Error de conexión', 'error'); });
        },
        
        // Escape HTML
        esc: function(texto) {
            var div = document.createElement('div');
            div.textContent = texto;
            return div.innerHTML;
        }
    };
</script>