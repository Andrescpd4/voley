<?php
// ============================================================
// STYLE_LIA.PHP — Estilos globales personalizados
// Se incluye en cabeza.php
// ============================================================
?>
<!-- Estilos personalizados globales -->
<style>
    /* Clases para ocultar botones por permisos (pie.php las usa) */
    .accion-agregar, .accion-modificar, .accion-eliminar, .accion-ver, .accion-listar,
    .accion-imprimir, .accion-exportar, .accion-asignar, .accion-cambiar,
    .accion-restaurar, .accion-cargar, .accion-guardar, .accion-enviar,
    .accion-aprobar, .accion-rechazar, .accion-devolver, .accion-finalizar {
        display: inline-block !important;
    }
    
    /* Loading overlay personalizado */
    #loading {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
    }
    #loading img {
        width: 48px;
        height: 48px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Mejoras para tablas */
    .table-hover tbody tr:hover {
        background-color: rgba(64, 81, 137, 0.04);
    }
    
    /* Badge estados */
    .badge-estado-activo { background-color: #28a745; }
    .badge-estado-inactivo { background-color: #dc3545; }
    .badge-estado-pendiente { background-color: #ffc107; color: #212529; }
    .badge-estado-aprobado { background-color: #17a2b8; }
    .badge-estado-rechazado { background-color: #dc3545; }
    .badge-estado-borrador { background-color: #6c757d; }
    
    /* Sidebar active */
    .nav-link.menu-link.active,
    .nav-item .nav-link.active {
        background-color: rgba(64, 81, 137, 0.1);
        color: #405189;
        font-weight: 500;
    }
    
    /* Card stats */
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    
    /* Formulario wizard steps */
    .wizard-steps .nav-link {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin: 0 0.25rem;
        transition: all 0.2s;
    }
    .wizard-steps .nav-link.active {
        background: #405189;
        color: white;
    }
    
    /* Dropzone personalizado */
    .dropzone {
        min-height: 200px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        transition: border-color 0.2s, background 0.2s;
    }
    .dropzone.dz-drag-hover {
        border-color: #405189;
        background: rgba(64, 81, 137, 0.05);
    }
    
    /* Select2 en modales */
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding-top: 4px;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
    }
    
    /* Scrollbar personalizado */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }
    
    /* Responsive cards */
    @media (max-width: 767.98px) {
        .card-body { padding: 1rem; }
        .table-responsive { font-size: 0.875rem; }
    }
</style>