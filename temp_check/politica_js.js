
// Aceptar la politica desde la vista embebida
function politicaAceptarDesdeVista() {
    var boton_aceptar = document.getElementById('btnAceptarPolVista');
    if (boton_aceptar) {
        boton_aceptar.disabled = true;
    }

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
                title: 'Consentimiento registrado',
                text: 'Gracias por aceptar los tÃ©rminos.',
                timer: 1400,
                showConfirmButton: false
            });
            setTimeout(function() {
                window.location.href = web_root + 'inicio';
            }, 1200);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos_resp.msg || 'No se pudo guardar la aceptaciÃ³n.'
            });
            if (boton_aceptar) {
                boton_aceptar.disabled = false;
            }
        }
    })
    .catch(function(error_red) {
        console.error(error_red);
        if (boton_aceptar) {
            boton_aceptar.disabled = false;
        }
    });
}

// Rechazar la politica y cerrar sesion
function politicaRechazarDesdeVista() {
    Swal.fire({
        title: 'Â¿Rechazar polÃ­tica?',
        text: 'Si rechazas los tÃ©rminos se cerrarÃ¡ tu sesiÃ³n y no podrÃ¡s ingresar a la plataforma.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'SÃ­, rechazar y salir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#405189'
    }).then(function(resultado_swal) {
        if (resultado_swal.isConfirmed) {
            fetch(web_root + 'iniciar-sesion/rechazar_politica', {
                method: 'POST'
            })
            .then(function() {
                window.location.href = web_root + 'iniciar-sesion';
            })
            .catch(function() {
                window.location.href = web_root + 'iniciar-sesion';
            });
        }
    });
}

