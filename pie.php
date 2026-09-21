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
                            <script>document.write(new Date().getFullYear())</script> &copy; Voley+
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
</body>
</html>
