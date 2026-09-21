<!-- Indice del Asistente - Links a cada sub-modulo -->
<style>
    .asis-card {
        border: 1px solid var(--vz-border-color);
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        height: 100%;
    }
    .asis-card:hover {
        border-color: var(--vz-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .asis-icon {
        font-size: 32px;
        color: var(--vz-primary);
        margin-bottom: 12px;
    }
    .asis-titulo {
        font-weight: 600;
        color: var(--vz-body-color);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><i class="ri-robot-line me-2"></i>Asistente de Desarrollo</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/admin-front" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-palette-line"></i></div>
                    <div class="asis-titulo">Actualizar visual front</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/formulario-crud" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-database-2-line"></i></div>
                    <div class="asis-titulo">Crear formulario CRUD</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/gestion-menu" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-menu-2-line"></i></div>
                    <div class="asis-titulo">Gestion de Menu</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/gestion-acciones" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-settings-3-line"></i></div>
                    <div class="asis-titulo">Gestion Servicios</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/visual-front" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-eye-line"></i></div>
                    <div class="asis-titulo">Gestion visuales front</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/formulario-libre" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-file-text-line"></i></div>
                    <div class="asis-titulo">Crear formulario libre</div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <a href="<?php echo WEB_ROOT ?>asistente/roles" class="text-decoration-none">
                <div class="asis-card">
                    <div class="asis-icon"><i class="ri-shield-user-line"></i></div>
                    <div class="asis-titulo">Administracion</div>
                </div>
            </a>
        </div>
    </div>
</div>
