<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="toolbar">
                        <button id="btn_agregar" onclick="location.href='<?php echo WEB_ROOT ?>asistente/formulario-crud'" class="btn btn-square btn-outline-success">
                            <i class="fa fa-plus"></i> Crear Nuevo CRUD
                        </button>
                    </div>
                    <table id="table_crud"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var $table = $('#table_crud');
    $table.bootstrapTable({
        url: page_root + 'listar',
        pagination: true,
        sidePagination: 'server',
        height: 600,
        columns: [
            { field: '_NUM_', title: '#' },
            { field: 'nombre', title: 'Nombre' },
            { field: 'ruta', title: 'Ruta' },
            { field: 'accion', title: 'Accion' },
            { field: 'orden', title: 'Orden' }
        ]
    });
});
</script>
