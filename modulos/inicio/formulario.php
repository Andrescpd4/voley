<!-- ============================================================
 DASHBOARD — Panel principal de Voley+

 Estructura:
   - Cards con estadisticas principales
   - Graficos (ApexCharts)
   - Tablas de resumen
============================================================ -->

<style>
    .card-stat {
        border-left: 4px solid #405189;
        transition: transform 0.2s;
    }
    .card-stat:hover {
        transform: translateY(-3px);
    }
    .card-stat .icon-stat {
        font-size: 28px;
        color: #405189;
    }
    .card-stat .value-stat {
        font-size: 24px;
        font-weight: 700;
        color: #405189;
    }
    .card-stat .label-stat {
        font-size: 12px;
        color: #878a99;
        text-transform: uppercase;
    }
    .card-stat.success { border-left-color: #0ab39c; }
    .card-stat.success .icon-stat,
    .card-stat.success .value-stat { color: #0ab39c; }
    .card-stat.warning { border-left-color: #f7b84b; }
    .card-stat.warning .icon-stat,
    .card-stat.warning .value-stat { color: #f7b84b; }
    .card-stat.danger { border-left-color: #f06548; }
    .card-stat.danger .icon-stat,
    .card-stat.danger .value-stat { color: #f06548; }
</style>

<div class="row">
    <!-- Card: Deportistas Activas -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-team-line icon-stat"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="value-stat" id="stat-deportistas">0</div>
                        <div class="label-stat">Deportistas Activas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Nuevas Solicitudes -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-file-add-line icon-stat"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="value-stat" id="stat-solicitudes">0</div>
                        <div class="label-stat">Nuevas Solicitudes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Documentación Pendiente -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-file-list-3-line icon-stat"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="value-stat" id="stat-documentos">0</div>
                        <div class="label-stat">Docs Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Autorizaciones Pendientes -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-shield-check-line icon-stat"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="value-stat" id="stat-autorizaciones">0</div>
                        <div class="label-stat">Autorizaciones</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mensaje de bienvenida -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Bienvenido a Voley+</h5>
                <p class="text-muted mb-0">
                    Sistema de gestión integral del club. Utilice el menú lateral para navegar entre los módulos:
                </p>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary-subtle text-primary p-2">
                                    <i class="ri-user-add-line"></i>
                                </span>
                            </div>
                            <div>
                                <h6>Afiliación</h6>
                                <p class="text-muted small mb-0">Registro de deportistas, documentos y autorizaciones.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-success-subtle text-success p-2">
                                    <i class="ri-calendar-check-line"></i>
                                </span>
                            </div>
                            <div>
                                <h6>Asistencia</h6>
                                <p class="text-muted small mb-0">Control de asistencia por clase y categoría.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-warning-subtle text-warning p-2">
                                    <i class="ri-trophy-line"></i>
                                </span>
                            </div>
                            <div>
                                <h6>Eventos</h6>
                                <p class="text-muted small mb-0">Torneos, salidas y convocatorias.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // ============================================================
    // RENOVAR JWT Y CARGAR DATOS DEL DASHBOARD
    // ============================================================

    // Renovar JWT al cargar la pagina
    function gdpRenovarToken() {
        $.ajax({
            url: web_root + 'inicio/set_token',
            type: 'POST',
            headers: { 'Authorization': TOKEN_GLOBAL },
            dataType: 'json',
            success: function(r) {
                if (r.error === false) {
                    TOKEN_GLOBAL = r.data;
                    localStorage.setItem('stp_k_l_t', r.token || r.data);
                }
            }
        });
    }

    // Cargar datos del dashboard
    function gdpCargarDashboard() {
        $.ajax({
            url: web_root + 'inicio/dashboard',
            type: 'POST',
            headers: { 'Authorization': TOKEN_GLOBAL },
            dataType: 'json',
            success: function(r) {
                if (r.error === false) {
                    var datos = r.data;
                    document.getElementById('stat-deportistas').textContent = datos.total_deportistas;
                    document.getElementById('stat-solicitudes').textContent = datos.nuevas_solicitudes;
                    document.getElementById('stat-documentos').textContent = datos.documentacion_pendiente;
                    document.getElementById('stat-autorizaciones').textContent = datos.autorizaciones_pendientes;
                }
            }
        });
    }

    // Ejecutar al cargar
    $(document).ready(function() {
        gdpRenovarToken();
        gdpCargarDashboard();
    });
</script>
