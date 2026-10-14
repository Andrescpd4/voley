// ============================================================
// SW.VOLEY+ — Service Worker solo para push del navegador
//
// No guarda paginas en cache: solo muestra el aviso y abre la app.
// Se registra desde el perfil (boton Activar notificaciones).
// ============================================================

// Recibir un push enviado desde el servidor
self.addEventListener('push', function(evento) {
    // Datos que envia el servidor en JSON (con texto por defecto)
    var datos = {
        title: 'Voley+',
        body: '',
        url: 'inicio'
    };
    if (evento.data) {
        try {
            var recibido = evento.data.json();
            if (recibido.title) {
                datos.title = recibido.title;
            }
            if (recibido.body) {
                datos.body = recibido.body;
            }
            if (recibido.url) {
                datos.url = recibido.url;
            }
        } catch (error_datos) {
            // Si no es JSON, mostrar el texto tal cual
            datos.body = evento.data.text();
        }
    }
    // Icono del club resuelto contra el alcance (funciona en local y hosting)
    var icono = 'img/logo-icon.png';
    if (self.registration.scope) {
        icono = self.registration.scope + 'img/logo-icon.png';
    }
    // Mostrar el aviso en el sistema (movil o escritorio)
    evento.waitUntil(
        self.registration.showNotification(datos.title, {
            body: datos.body,
            icon: icono,
            badge: icono,
            vibrate: [200, 100, 200],
            data: { url: datos.url }
        })
    );
});

// Al tocar el aviso: abrir o enfocar la pagina de destino
self.addEventListener('notificationclick', function(evento) {
    evento.notification.close();
    var destino = 'inicio';
    if (evento.notification.data.url) {
        destino = evento.notification.data.url;
    }
    // Resolver el destino contra el alcance del registro
    var url_final = destino;
    if (self.registration.scope) {
        // Si ya es absoluta, se deja; si no, se pega al alcance
        if (destino.indexOf('http') !== 0) {
            url_final = self.registration.scope + destino;
        } else {
            url_final = destino;
        }
    }
    evento.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(lista) {
            for (var i = 0; i < lista.length; i++) {
                if (lista[i].url.indexOf(destino) !== -1) {
                    return lista[i].focus();
                }
            }
            // Sin pestana abierta, abrir el destino
            return clients.openWindow(url_final);
        })
    );
});
