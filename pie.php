<?php
// ============================================================
// PIE.PHP — Footer global del sistema
//
// Se incluye DESPUES del formulario.php de cada modulo.
// Contiene:
//   - Footer HTML
//   - JS de Bootstrap, App global
//   - JS que envia TOKEN_GLOBAL en cada peticion AJAX
//   - JS que oculta botones segun permisos del rol
//   - JS para breadcrumb y menu activo
// ============================================================
?>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> &copy; Voley+ · <a href="<?php echo WEB_ROOT ?>politica-privacidad" class="text-decoration-none text-muted">Política de Privacidad</a>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Desarrollado por Voley+
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Loading overlay -->
    <div id="loading" style="display: none; width: 100%; background: #0009; position: fixed; height: 1200px; color: white; top: 0; z-index: 999999; padding-top: 25%; text-align: center;">
        Por favor espere, cargando...<br>
        <img src="<?php echo WEB_ROOT ?>img/pb.gif">
    </div>

    <!-- Loading JS -->
    <script type="text/javascript">
        $(document).ajaxStart(function() {
            $("#loading").show();
        });
        $(document).ajaxStop(function() {
            $("#loading").hide();
        });
    </script>

    <!-- JAVASCRIPT -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/feather-icons/feather.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>

    <!-- apexcharts -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- App js -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/app.js"></script>

    <!-- Toggler propio del menu hamburguesa (no depende de app.js) -->
    <script type="text/javascript">
        // El app.js de Velzon a veces no ata el clic del hamburguesa.
        // Este bloque replica ese comportamiento para que el boton
        // funcione en PC, tablet y movil aunque app.js este caido.
        (function() {
            // Evitar doble instalacion si pie.php se incluye dos veces
            if (window.voleyHamburguesaListo) {
                return;
            }
            window.voleyHamburguesaListo = true;

            // Animar el icono de 3 lineas (igual que hace Velzon)
            function voleyAnimarIcono() {
                // Solo animar en pantallas grandes como hace el original
                if (document.documentElement.clientWidth > 767) {
                    var icono = document.querySelector('.hamburger-icon');
                    if (icono) {
                        icono.classList.toggle('open');
                    }
                }
            }

            // Abrir o cerrar el menu lateral segun el ancho de pantalla
            function voleyAlternarMenu() {
                var ancho = document.documentElement.clientWidth;
                var disposicion = document.documentElement.getAttribute('data-layout');
                // Solo manejamos la disposicion vertical que usa el sistema
                if (disposicion !== 'vertical') {
                    return;
                }
                voleyAnimarIcono();
                if (ancho > 1025) {
                    // PC grande: alternar entre menu ancho y menu angosto
                    document.body.classList.remove('vertical-sidebar-enable');
                    if (document.documentElement.getAttribute('data-sidebar-size') === 'lg') {
                        document.documentElement.setAttribute('data-sidebar-size', 'sm');
                    } else {
                        document.documentElement.setAttribute('data-sidebar-size', 'lg');
                    }
                } else if (ancho > 767) {
                    // Tablet: alternar entre menu angosto y menu oculto
                    document.body.classList.remove('vertical-sidebar-enable');
                    if (document.documentElement.getAttribute('data-sidebar-size') === 'sm') {
                        document.documentElement.setAttribute('data-sidebar-size', '');
                    } else {
                        document.documentElement.setAttribute('data-sidebar-size', 'sm');
                    }
                } else {
                    // Movil: mostrar u ocultar el menu sobre la pantalla
                    document.body.classList.toggle('vertical-sidebar-enable');
                    document.documentElement.setAttribute('data-sidebar-size', 'lg');
                }

                // Sincronizar estado para lectores de pantalla
                var botonMenu = document.getElementById('topnav-hamburger-icon');
                if (botonMenu) {
                    var menuVisible = false;
                    if (ancho > 767) {
                        menuVisible = document.documentElement.getAttribute('data-sidebar-size') === 'lg';
                    } else {
                        menuVisible = document.body.classList.contains('vertical-sidebar-enable');
                    }
                    var textoExpandido = 'false';
                    if (menuVisible) {
                        textoExpandido = 'true';
                    }
                    botonMenu.setAttribute('aria-expanded', textoExpandido);
                }
            }

            // Cerrar el menu movil cuando se toca el fondo oscuro
            function voleyCerrarMenuMovil() {
                document.body.classList.remove('vertical-sidebar-enable');
            }

            // Si se agranda a PC o tablet con el menu movil abierto, cerrarlo
            // para que la clase no trabe el layout de escritorio al rotar o redimensionar
            window.addEventListener('resize', function() {
                if (document.documentElement.clientWidth > 767) {
                    document.body.classList.remove('vertical-sidebar-enable');
                }
            });

            // Escuchar clics en captura para ganarle al handler de app.js.
            // Si app.js esta vivo hace lo mismo, asi que lo frenamos para
            // no alternar dos veces y que parezca que no hace nada.
            document.addEventListener('click', function(evento) {
                var boton = null;
                if (evento.target.closest) {
                    boton = evento.target.closest('#topnav-hamburger-icon');
                }
                if (boton) {
                    evento.stopPropagation();
                    evento.preventDefault();
                    voleyAlternarMenu();
                    return;
                }
                var fondo = null;
                if (evento.target.closest) {
                    fondo = evento.target.closest('.vertical-overlay');
                }
                if (fondo) {
                    evento.stopPropagation();
                    voleyCerrarMenuMovil();
                }
            }, true);
        })();
    </script>

    <!-- Toastify -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/toastify-js/src/toastify.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/js/pages/select2.init.js"></script>

    <!-- Dropzone -->
    <script src="<?php echo WEB_ROOT ?>plantilla/assets/libs/dropzone/dropzone-min.js"></script>

    <!-- Enviar TOKEN_GLOBAL en cada peticion AJAX -->
    <script type="text/javascript">
        $(document).ajaxSend(function(e, xhr, opt) {
            xhr.setRequestHeader("Authorization", localStorage.getItem("stp_k_l_t"));
        });
    </script>

    <!-- Ocultar botones segun permisos y activar menu -->
    <script type="text/javascript">
        $(document).ready(function(e) {
            setTimeout(function() {
                <?php
                // Ocultar botones de acciones que el rol no tiene permiso
                if (isset($_SESSION['usuario_rol'])) {
                    $sql = "SELECT a.accion, a.requiere_permiso, pa.id AS permiso
                            FROM admin_accion a
                            LEFT JOIN admin_permiso_accion pa ON a.id = pa.accion AND pa.rol = '" . $_SESSION['usuario_rol'] . "'
                            WHERE a.menu = '" . MENU . "' AND a.requiere_permiso = 'S'";
                    $rs = @$db->select_all($sql);
                    if (is_array($rs)) {
                        foreach ($rs as $rw) {
                            if ($rw['permiso'] == "") {
                                echo '$(".accion-' . $rw['accion'] . '").hide();';
                            }
                        }
                    }
                }
                ?>
            }, 2000);

            // Activar menu actual
            <?php
            if (defined('MENU')) {
                $rw = @$db->select_row("SELECT * FROM admin_menu WHERE menu = '" . MENU . "'");
                if ($rw) {
                    $ruta = "";
                    if ($rw['padre'] != "") {
                        $padre = @$db->select_row("SELECT * FROM admin_menu WHERE menu = '" . $rw['padre'] . "'");
                        if ($padre) {
                            $ruta = '<ol class="breadcrumb m-0"><li class="breadcrumb-item"><a href="' . WEB_ROOT . 'inicio"></a></li><li class="breadcrumb-item">' . $padre["nombre"] . '</li><li class="breadcrumb-item active">' . $rw["nombre"] . '</li></ol>';
                            echo '$(".menur").removeClass("active");';
                            echo '$(".menup-' . $rw['menu'] . '").addClass("active");';
                            echo '$(".menup-' . $rw['padre'] . '").addClass("active");';
                            echo '$("#menu-' . $padre['menu'] . '").addClass("open");';
                        }
                    } else {
                        echo '$(".menup-' . $rw['menu'] . '").addClass("active");';
                    }
                    echo '$("#ruta_global").html("' . addslashes($ruta) . '");';
                }
            }
            ?>

            $("#titulo_ubicacion_frm").html("<?php echo defined('RUTA_MENU') ? RUTA_MENU : '' ?>");
            $("input[type='text']").addClass("form-control");
            $("input[type='date']").addClass("form-control");
            $("input[type='number']").addClass("form-control");
            $("input[type='email']").addClass("form-control");
            $("input[type='tel']").addClass("form-control");
            $("select").addClass("form-select form-control");
            $("textarea").addClass("form-control");
            $("table").addClass("table table-hover");
        });
    </script>
    <!-- Campana global de notificaciones (Fase C) -->
    <script type="text/javascript">
        // La campana vive en cabeza.php y funciona en todos los modulos.
        // Llama al backend de inicio con la ruta absoluta (web_root),
        // porque page_root cambia segun el modulo visible.
        (function() {
            // Evitar doble instalacion si pie.php se incluye dos veces
            // Ojo: no usar voleyCampanaLista como bandera porque ese id ya existe
            // en cabeza.php y el navegador lo convierte en variable global
            if (window.voleyCampanaInstalada) {
                return;
            }
            window.voleyCampanaInstalada = true;

            // Token guardado al iniciar sesion (misma llave que TOKEN_GLOBAL)
            function voleyCampanaToken() {
                var guardado = localStorage.getItem('stp_k_l_t');
                if (!guardado) {
                    return '';
                }
                return guardado;
            }

            // Saneamiento basico contra XSS
            function voleyCampanaEsc(texto_crudo) {
                if (!texto_crudo && texto_crudo !== 0) {
                    return '';
                }
                return texto_crudo.toString()
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // POST estandar hacia las acciones de notificaciones del modulo inicio
            function voleyCampanaAjax(nombre_accion, datos_envio, funcion_retorno) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', web_root + 'inicio/' + nombre_accion, true);
                xhr.setRequestHeader('Authorization', voleyCampanaToken());
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            funcion_retorno(JSON.parse(xhr.responseText));
                        } catch (error_parseo) {
                            funcion_retorno({ error: true });
                        }
                    } else {
                        funcion_retorno({ error: true });
                    }
                };
                xhr.onerror = function() {
                    funcion_retorno({ error: true });
                };
                var parametros = new URLSearchParams();
                var claves = Object.keys(datos_envio);
                for (var i = 0; i < claves.length; i++) {
                    parametros.append(claves[i], datos_envio[claves[i]]);
                }
                xhr.send(parametros.toString());
            }

            // Pintar el numero de no leidas en la insignia de la campana
            function voleyCampanaPintarConteo(total) {
                var insignia = document.getElementById('voleyCampanaConteo');
                var pie = document.getElementById('voleyCampanaPie');
                if (!insignia || !pie) {
                    return;
                }
                if (total > 0) {
                    insignia.textContent = total;
                    insignia.classList.remove('d-none');
                    if (total === 1) {
                        pie.textContent = 'Tienes 1 aviso sin leer';
                    } else {
                        pie.textContent = 'Tienes ' + total + ' avisos sin leer';
                    }
                } else {
                    insignia.classList.add('d-none');
                    pie.textContent = 'Sin notificaciones';
                }
            }

            // Actualizar el conteo (tambien la llaman otros modulos al responder)
            window.voleyCampanaActualizar = function() {
                if (!document.getElementById('voleyCampanaBoton')) {
                    return;
                }
                if (voleyCampanaToken() === '') {
                    return;
                }
                voleyCampanaAjax('contar_notificaciones', {}, function(respuesta) {
                    if (respuesta.error || !respuesta.data) {
                        return;
                    }
                    voleyCampanaPintarConteo(parseInt(respuesta.data.total, 10) || 0);
                });
            };

            // Armar un renglon de aviso para la lista
            function voleyCampanaCrearItem(aviso) {
                var id_aviso = parseInt(aviso.id, 10) || 0;
                var titulo = voleyCampanaEsc(aviso.titulo);
                var mensaje = voleyCampanaEsc(aviso.mensaje);
                var fecha = voleyCampanaEsc(aviso.creada_en);
                var leida = parseInt(aviso.leida, 10) === 1;
                var evento_id = parseInt(aviso.evento_id, 10) || 0;

                // Destino: inicio, y al ancla de autorizaciones si es convocatoria
                var destino = web_root + voleyCampanaEsc(aviso.url || 'inicio');
                if (evento_id > 0) {
                    destino = destino + '#bloqueAutorizaciones';
                }

                // Los no leidos van resaltados
                var clase_item = 'list-group-item list-group-item-action voley-campana-item';
                var clase_titulo = 'fw-normal';
                if (!leida) {
                    clase_item = clase_item + ' bg-light';
                    clase_titulo = 'fw-semibold';
                }

                return `<a href="${destino}" data-aviso="${id_aviso}" class="${clase_item}">
                <div class="${clase_titulo} small">${titulo}</div>
                <div class="text-muted small">${mensaje}</div>
                <div class="text-muted" style="font-size: 11px;">${fecha}</div>
            </a>`;
            }

            // Cargar la lista de avisos al abrir la campana
            function voleyCampanaCargarLista() {
                var lista = document.getElementById('voleyCampanaLista');
                var boton_todas = document.getElementById('voleyCampanaMarcarTodas');
                if (!lista) {
                    return;
                }
                lista.innerHTML = '<div class="text-center p-3 text-muted small">Cargando...</div>';
                voleyCampanaAjax('listar_notificaciones', {}, function(respuesta) {
                    if (respuesta.error || !respuesta.data) {
                        lista.innerHTML = '<div class="text-center p-3 text-muted small">No se pudieron cargar los avisos.</div>';
                        return;
                    }
                    var avisos = respuesta.data;
                    if (avisos.length === 0) {
                        lista.innerHTML = '<div class="text-center p-3 text-muted small">Sin notificaciones.</div>';
                        if (boton_todas) {
                            boton_todas.classList.add('d-none');
                        }
                        return;
                    }
                    var html_final = '';
                    for (var i = 0; i < avisos.length; i++) {
                        html_final = html_final + voleyCampanaCrearItem(avisos[i]);
                    }
                    lista.innerHTML = html_final;
                    if (boton_todas) {
                        boton_todas.classList.remove('d-none');
                    }
                });
            }

            // Marcar un aviso como leido y luego navegar a su destino
            function voleyCampanaAbrirAviso(elemento, destino) {
                var id_aviso = parseInt(elemento.getAttribute('data-aviso'), 10) || 0;
                if (id_aviso <= 0) {
                    window.location.href = destino;
                    return;
                }
                voleyCampanaAjax('marcar_notificacion', { id: id_aviso }, function() {
                    window.voleyCampanaActualizar();
                    window.location.href = destino;
                });
            }

            // Atar eventos una sola vez al cargar cada pagina
            function voleyCampanaIniciar() {
                var boton = document.getElementById('voleyCampanaBoton');
                if (!boton) {
                    return;
                }
                if (voleyCampanaToken() === '') {
                    return;
                }

                // Abrir: cargar la lista fresca
                boton.addEventListener('click', function() {
                    voleyCampanaCargarLista();
                });

                // Clic en un aviso: marcar leido y navegar
                var lista = document.getElementById('voleyCampanaLista');
                if (lista) {
                    lista.addEventListener('click', function(evento) {
                        var elemento = null;
                        if (evento.target.closest) {
                            elemento = evento.target.closest('.voley-campana-item');
                        }
                        if (!elemento) {
                            return;
                        }
                        evento.preventDefault();
                        voleyCampanaAbrirAviso(elemento, elemento.getAttribute('href'));
                    });
                }

                // Marcar todas como leidas
                var boton_todas = document.getElementById('voleyCampanaMarcarTodas');
                if (boton_todas) {
                    boton_todas.addEventListener('click', function() {
                        voleyCampanaAjax('marcar_notificacion', { id: 0 }, function() {
                            voleyCampanaCargarLista();
                            window.voleyCampanaActualizar();
                        });
                    });
                }

                // Conteo inicial y refresco cada minuto
                window.voleyCampanaActualizar();
                setInterval(window.voleyCampanaActualizar, 60000);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', voleyCampanaIniciar);
            } else {
                voleyCampanaIniciar();
            }
        })();
    </script>
</body>
</html>
