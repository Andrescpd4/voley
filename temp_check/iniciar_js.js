
        // 1. Alternar visibilidad de contraseÃ±a
        var btnToggle = document.getElementById('btnToggleClave');
        var inputClave = document.getElementById('clave');
        var iconoClave = document.getElementById('iconoClave');

        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                if (inputClave.type === 'password') {
                    inputClave.type = 'text';
                    iconoClave.className = 'ri-eye-off-fill align-middle';
                } else {
                    inputClave.type = 'password';
                    iconoClave.className = 'ri-eye-fill align-middle';
                }
            });
        }

        // 2. Enviar aceptacion de politica al servidor
        function enviarAceptacionPolitica(url_redireccion) {
            fetch(web_root + 'iniciar-sesion/aceptar_politica', {
                method: 'POST'
            })
            .then(function(respuesta) {
                return respuesta.json();
            })
            .then(function(datos_resp) {
                if (datos_resp.error === false) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Â¡Consentimiento registrado!',
                        text: 'Ingresando al sistema...',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        window.location.href = datos_resp.redirect || url_redireccion || (web_root + 'inicio');
                    }, 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar',
                        text: datos_resp.msg || 'No se pudo guardar la aceptaciÃ³n.'
                    });
                    restaurarBotonIngreso();
                }
            })
            .catch(function(error_conexion) {
                console.error(error_conexion);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexiÃ³n',
                    text: 'No se pudo registrar su consentimiento.'
                });
                restaurarBotonIngreso();
            });
        }

        // 3. Enviar rechazo de politica al servidor
        function enviarRechazoPolitica() {
            fetch(web_root + 'iniciar-sesion/rechazar_politica', {
                method: 'POST'
            })
            .then(function(respuesta) {
                return respuesta.json();
            })
            .then(function(datos_resp) {
                localStorage.removeItem('stp_k_l_t');
                Swal.fire({
                    icon: 'warning',
                    title: 'Acceso cancelado',
                    text: datos_resp.msg || 'No es posible acceder al sistema sin aceptar la polÃ­tica de privacidad.'
                });
                restaurarBotonIngreso();
            })
            .catch(function(error_conexion) {
                console.error(error_conexion);
                localStorage.removeItem('stp_k_l_t');
                restaurarBotonIngreso();
            });
        }

        // 4. Mostrar popup bloqueante de aceptacion de politica
        function mostrarModalPolitica(datos_login) {
            var url_politica = datos_login.politica_url || (web_root + 'politica-privacidad');
            var version_politica = datos_login.politica_version || '1.0';
            var nombre_politica = datos_login.politica_nombre || 'PolÃ­tica de Privacidad';
            var url_destino = datos_login.redirect || (web_root + 'inicio');

            var contenido_html = `<div class="text-start">
                <p class="mb-2">Para ingresar a Voley+ debes leer y aceptar nuestra <strong>${nombre_politica} (VersiÃ³n ${version_politica})</strong>.</p>
                <p class="text-muted small mb-3">Tu consentimiento nos permite gestionar las fichas deportivas, registros de asistencia y comunicaciones de forma legal y segura.</p>
                <div class="p-2 border rounded bg-light text-center mb-3">
                    <a href="${url_politica}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="ri-external-link-line me-1"></i> Abrir y leer polÃ­tica completa
                    </a>
                </div>
                <p class="text-muted small mb-0">Si rechazas los tÃ©rminos no podrÃ¡s acceder a la plataforma.</p>
            </div>`;

            Swal.fire({
                title: 'PolÃ­tica de Privacidad',
                html: contenido_html,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="ri-check-line me-1"></i> Acepto la polÃ­tica',
                cancelButtonText: '<i class="ri-close-line me-1"></i> Rechazar y salir',
                confirmButtonColor: '#405189',
                cancelButtonColor: '#f06548',
                allowOutsideClick: false,
                allowEscapeKey: false,
                focusConfirm: true
            }).then(function(resultado_sweet) {
                if (resultado_sweet.isConfirmed) {
                    enviarAceptacionPolitica(url_destino);
                } else {
                    enviarRechazoPolitica();
                }
            });
        }

        // 5. Restaurar estado del boton ingresar
        function restaurarBotonIngreso() {
            var btnIngresar = document.getElementById('btnIngresar');
            var btnTexto = document.getElementById('btnTexto');
            var btnCargando = document.getElementById('btnCargando');
            btnIngresar.disabled = false;
            btnTexto.style.display = 'inline-block';
            btnCargando.style.display = 'none';
        }

        // 6. Enviar formulario de login vÃ­a AJAX
        var formLogin = document.getElementById('formLogin');
        var btnIngresar = document.getElementById('btnIngresar');
        var btnTexto = document.getElementById('btnTexto');
        var btnCargando = document.getElementById('btnCargando');

        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();

            var usuario = document.getElementById('usuario').value.trim();
            var clave = document.getElementById('clave').value;

            if (usuario === '' || clave === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos obligatorios',
                    text: 'Por favor ingresa tu usuario y contraseÃ±a'
                });
                return;
            }

            // Mostrar estado de carga
            btnIngresar.disabled = true;
            btnTexto.style.display = 'none';
            btnCargando.style.display = 'inline-block';

            var formData = new FormData();
            formData.append('usuario', usuario);
            formData.append('clave', clave);

            fetch(web_root + 'iniciar-sesion/iniciar', {
                method: 'POST',
                body: formData
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (data.error === false) {
                    // Guardar JWT en localStorage
                    if (data.token) {
                        localStorage.setItem('stp_k_l_t', data.token);
                    }

                    // Verificar si requiere firma de politica
                    if (data.politica_pendiente === true) {
                        mostrarModalPolitica(data);
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Â¡Ingreso Exitoso!',
                            text: data.msg || 'Bienvenido al sistema',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = data.redirect || (web_root + 'inicio');
                        }, 1000);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso Denegado',
                        text: data.msg || 'Credenciales incorrectas'
                    });
                    restaurarBotonIngreso();
                }
            })
            .catch(function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de ConexiÃ³n',
                    text: 'No se pudo contactar con el servidor. Verifica tu conexiÃ³n.'
                });
                restaurarBotonIngreso();
            });
        });
    
