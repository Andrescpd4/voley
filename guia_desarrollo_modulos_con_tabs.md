📋 Guía de Desarrollo de Módulos con Tabs
Aquí está un instructivo completo y paso a paso para desarrollar un nuevo módulo que utilice el sistema de pestañas (formulario.php + tabs/*.php), basado en el análisis del módulo inv-epp y gestion_clientes/alpina/lavado_cubetas.
🎯 Requisitos previos
1. Definir el propósito del módulo
Decide qué hará el módulo:
- Módulo CRUD: Listar, agregar, modificar, eliminar (ej: "Areas de servicio")
- Módulo libre: Lógica de negocio personalizada (ej: "Historial de cambios")
2. Elegir una categoría (de modulos/<categoria>/<nombre>/)
# Ejemplos válidos:
modulos/clientes/repartidores/
modulos/descuentos/promociones/
modulos/reportes/ventas_diarias/
modulos/seguimiento/entregas/
🏗️ Estructura de directorios
modulos/<categoria>/<nombre>/
├── acciones.php              ← Backend: clase Formulario extends Base (patrón libre o CRUD)
├── formulario.php            ← Orquestador: Nav de pestañas + includes
└── tabs/
    ├── tab1.php            ← Vista parcial: Tab "Nombre Tab 1"
    ├── tab2.php             ← Vista parcial: Tab "Nombre Tab 2" 
    └── tab3.php             ← Vista parcial: Tab "Nombre Tab 3"
📝 Paso a paso de desarrollo
Paso 1: Crear el módulo
# Crear el directorio
mkdir -p modulos/<categoria>/<nombre>/tabs

# Iniciar el esqueleto básico
touch modulos/<categoria>/<nombre>/acciones.php
touch modulos/<categoria>/<nombre>/formulario.php
mkdir -p modulos/<categoria>/<nombre>/tabs
Paso 2: Configurar acciones.php
PATRÓN LIBRE (recomendado para módulos grandes):
// Archivo: modulos/<categoria>/<nombre>/acciones.php
<?php
// ============================================================
// <NOMBRE_MÓDULO> — Backend del módulo
// ============================================================
// Ejemplo: "gestor_pedido"
// El framework llega aquí así:
//   index.php -> descarga.php -> define('ACCION', '...') -> este archivo
//
// ACCION puede ser cualquiera de las registradas en admin_accion:
//   cada método tiene un equivalente en un trait
// ============================================================

require_once __DIR__ . '/../../../php/clase_base.php';
require_once __DIR__ . '/clases/<nom_helper>_helpers.php';
// otros helpers si es necesario

class Formulario extends Base
{
    use <nom_helper>_helpers;      // utilidades comunes
    // Ejemplo: use sgs_helpers;
    // Agrega más traits según sea necesario:
    // use <nom_helper>_estructura;  // si tienes navegación por sedes/años/meses
    // use <nom_helper>_carpetas;    // si tienes gestión de carpetas
    // use <nom_helper>_documentos;  // si tienes gestión de archivos
}

// El despachador ejecuta el método cuyo nombre llega en ACCION
$accion = ACCION;
$f = new Formulario();
$f->$accion();
?>
PATRÓN CRUD (módulos pequeños):
<?php
// Archivo: modulos/<categoria>/<nombre>/acciones.php
<?php
class <NombreClass> extends formulario_basico {
    function validar() { /* reglas + validate */ }
    function getSQL() { return "SELECT ..."; }
}
$accion = ACCION;
$f = new <NombreClass>("tabla", "id", true);
$f->$accion();  // listar, agregar, modificar, eliminar, etc.
?>
Paso 3: Configurar formulario.php
El orquestador - plantilla NAV + Includes:
<!-- Archivo: modulos/<categoria>/<nombre>/formulario.php -->
<?php
/**
 * MÓDULO: <Nombre del módulo en español>
 * Orquestador - Nav de pestañas
 * Incluye archivo de helper de navegación en la parte superior
 */
?>

// ================ NAVEGACIÓN GLOBAL (TODO EL ARCHIVO) ================
?> 
<!-- Librería DataTables (necesaria para las tablas de todos los tabs) -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.1/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.1/js/buttons.dataTables.js"></script>

<!-- CSS de SweetAlert2 (para notificaciones modales) -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- JS de SweetAlert2 (tiempo de carga y alerta) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Scripts HOISTED - deben ir ANTES de los includes (function declarations) -->
<script type="text/javascript">
// ===== UTILERÍAS GLOBALES DEL SISTEMA =====

// gcAjax - para peticiones AJAX con token
function gcAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: respuesta.msg });
                } else if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parsing response:', e);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar respuesta' });
            }
        } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error en el servidor' });
        }
    };
    
    xhr.onerror = function() {
        console.error('Network error');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    };
    
    var params = new URLSearchParams();
    for (var key in datos) {
        params.append(key, datos[key]);
    }
    xhr.send(params.toString());
}

// Utilidades de escape
function gcEsc(valor) {
    if (!valor) return '';
    return valor.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Debounce para búsqueda en tiempo real
function gcDebounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}
</script>

<!-- Modales compartidos (son incluidos por los tabs si es necesario) -->
<div class="modal fade" id="gcModalShared" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Título del Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="gcModalBody">
                Contenido del modal aquí...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de utilerías específicos del módulo (incluido por HOISTING) -->
<?php include_once 'tabs/utilerias_js.php'; ?>

<!-- ========== FIN DE SCRIPTS HOISTED =========== -->

<!-- ========== NAV DE PESTAÑAS =========== -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs border-tab border-0 mb-0 nav-secondary" id="topline-tab" role="tablist">
                        <!-- Los enlaces de tabs son generados dinámicamente por el backend -->
                    </ul>
                    <div class="tab-content" id="topline-tabContent">
                        <!-- Los contenidos de las pestañas son incluidos dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS de configuración de DataTables (compartido) -->
<script type="text/javascript">
// ===== CONFIGURACIÓN GLOBAL DE DATATABLES =====
jQuery(document).ready(function($) {
    // Configuración global para todos los DataTables del módulo
    $.fn.dataTable.ext.errMode = 'throw';
});
</script>
?>
💡 CLAVE: Los scripts de utilerías se definen antes de los includes de los tabs (function declarations son hoisted). Los includes van entre cabeza.php y pie.php del sistema.
Paso 4: Configurar los tabs individuales
Patrón general para cada archivo de tab:
<!-- Archivo: modulos/<categoria>/<nombre>/tabs/tab1.php -->
<?php
// ===== VARIABLES OBLIGATORIAS =====
$page_title = "Título del Tab";
$page_icon = "ri-download-line";  // Clase de icono Remixicon

// ===== UTILERÍAS =====
$gcAjax = function($accion, $datos, $callback) {
    // Implementación o importar de formulario.php si es el mismo módulo
};
?>

<!-- CONTENIDO DEL TAB (HTML + JS embebido o incluido) -->
<div class="card">
    <div class="card-header">
        <h5>
            <i class="ri-download-line me-1"></i>
            Título del Tab
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_tab1" class="table dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Columna 1</th>
                        <th>Columna 2</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- LOS DATOS SON LLENADOS VIA AJAX, NO HTML ESTÁTICO -->
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Cargando datos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
// ===== JAVASCRIPT ESPECÍFICO DEL TAB =====
// Inicialización del DataTable para este tab específico
jQuery(document).ready(function($) {
    // Configuración para ESTE tab específico, NO global
    $('#table_tab1').DataTable({
        ajax: {
            url: page_root + 'listar?tab=1',  // Acción del backend específica para este tab
            data: function(d) {
                // Parámetros de filtro específicos del tab
                d.param1 = $('#filtro1').val();
                d.param2 = $('#filtro2').val();
            }
        },
        columns: [
            { data: 'columna1' },
            { data: 'columna2' },
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-primary">Editar</button> ' +
                           '<button class="btn btn-sm btn-danger">Eliminar</button>';
                }
            }
        ],
        language: { url: 'js/datatable/spanish.json' },
        pageLength: 50,
        processing: true,
        serverSide: true,
        // Configuración ESPECÍFICA para ESTE tab, no global
        initComplete: function() {
            // En este punto, el tab está completamente inicializado
            console.log('Tab 1 inicializado correctamente');
        }
    });
});
</script>
?>
Ejemplo completo para una pestaña de tabla (basado en inv-epp):
<!-- Archivo: modulos/<categoria>/<nombre>/tabs/tab_descargas.php -->
<?php
$page_title = "Descargas";
$page_icon = "ri-download-line";
?>

<!-- Contenedor principal del filtro -->
<div class="accordion" id="accordionFiltros">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingFiltros">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros">
                <i class="ri-filter-3-line me-2"></i> Filtros de Búsqueda
            </button>
        </h2>
        <div id="collapseFiltros" class="accordion-collapse collapse show" data-bs-parent="#accordionFiltros">
            <div class="accordion-body">
                <form id="formFiltros">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Cliente:</label>
                            <select class="form-control" id="filtro_cliente">
                                <option value="">Todos los clientes</option>
                                <?php
                                // Consulta real del backend:
                                $clientes = $db->select_all("SELECT id, nombre FROM clientes WHERE visible=1 ORDER BY nombre");
                                foreach ($clientes as $c) {
                                    echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Inicio:</label>
                            <input type="date" class="form-control" id="filtro_fecha_inicio">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Fin:</label>
                            <input type="date" class="form-control" id="filtro_fecha_fin">
                        </div>
                        <div class="col-md-3">
                            <label>Planta:</label>
                            <select class="form-control" id="filtro_planta">
                                <option value="">Todas las plantas</option>
                                <?php
                                $plantas = $db->select_all("SELECT id, nombre FROM plantas WHERE visible=1 ORDER BY nombre");
                                foreach ($plantas as $p) {
                                    echo "<option value='{$p['id']}'>{$p['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" onclick="aplicarFiltros()">
                        <i class="ri-search-line me-1"></i> Aplicar Filtros
                    </button>
                    <button type="button" class="btn btn-secondary mt-3 ms-2" onclick="limpiarFiltros()">
                        <i class="ri-refresh-line me-1"></i> Limpiar Filtros
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla principal -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>
            <i class="ri-table me-1"></i> Listado de Registros
        </h5>
        <button class="btn btn-success btn-sm" onclick="nuevoRegistro()">
            <i class="ri-add-line me-1"></i> Nuevo Registro
        </button>
    </div>
    <div class="card-body">
        <table id="table_descargas" class="table dt-responsive nowrap w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Planta</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="ri-loader-4-line spin me-1"></i> Cargando datos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modales específicos del tab -->
<div class="modal fade" id="modalFormulario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo">Nuevo/Editar Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- El contenido del formulario será cargado via AJAX -->
                <div class="text-center">
                    <i class="ri-loader-4-line spin fs-2"></i>
                    <p>Cargando formulario...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// ===== JAVASCRIPT DEL TAB =====

// Estado global del tab
const tabState = {
    table: null,
    filtros: {},
    currentRegistroId: null
};

// Función principal para aplicar filtros y recargar la tabla
function aplicarFiltros() {
    tabState.filtros = {
        cliente: $('#filtro_cliente').val(),
        fecha_inicio: $('#filtro_fecha_inicio').val(),
        fecha_fin: $('#filtro_fecha_fin').val(),
        planta: $('#filtro_planta').val()
    };
    
    if (tabState.table) {
        tabState.table.ajax.reload();
    }
}

// Función para limpiar todos los filtros
function limpiarFiltros() {
    $('#formFiltros')[0].reset();
    tabState.filtros = {};
    if (tabState.table) {
        tabState.table.ajax.reload();
    }
}

// Función para abrir el modal de nuevo registro
function nuevoRegistro() {
    $('#modalTitulo').text('Nuevo Registro');
    tabState.currentRegistroId = null;
    
    // Cargar el formulario vacío
    $.ajax({
        url: page_root + 'formulario',
        type: 'GET',
        data: { registro: 'nuevo' },
        success: function(response) {
            $('#modalFormulario .modal-body').html(response);
            $('#modalFormulario').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el formulario'
            });
        }
    });
}

// Función para editar un registro existente
function editarRegistro(id) {
    $('#modalTitulo').text('Editar Registro');
    tabState.currentRegistroId = id;
    
    // Cargar el formulario con datos existentes
    $.ajax({
        url: page_root + 'formulario',
        type: 'GET',
        data: { registro: id },
        success: function(response) {
            $('#modalFormulario .modal-body').html(response);
            $('#modalFormulario').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el registro'
            });
        }
    });
}

// Función para eliminar un registro
function eliminarRegistro(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada AJAX al backend
            gcAjax('eliminar', { id: id }, function(callback) {
                if (!callback.error) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Registro eliminado correctamente'
                    });
                    // Recargar la tabla
                    if (tabState.table) {
                        tabState.table.ajax.reload();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: callback.msg
                    });
                }
            });
        }
    });
}

// Inicialización del DataTable para este tab
jQuery(document).ready(function($) {
    // Función para armar acciones en la tabla (editar, ver, etc.)
    function gcpArmarAcciones(registro) {
        return '<div class="d-flex justify-content-center gap-2">' +
            '<button class="btn btn-sm btn-primary" onclick="editarRegistro(' + registro.id + ')" title="Editar">' +
            '<i class="ri-edit-line"></i> </button>' +
            '<button class="btn btn-sm btn-info" onclick="verRegistro(' + registro.id + ')" title="Ver">' +
            '<i class="ri-eye-line"></i> </button>' +
            '<button class="btn btn-sm btn-danger" onclick="eliminarRegistro(' + registro.id + ')" title="Eliminar">' +
            '<i class="ri-delete-bin-line"></i> </button>' +
            '</div>';
    }
    
    // Inicialización del DataTable
    tabState.table = $('#table_descargas').DataTable({
        ajax: {
            url: page_root + 'listar',
            data: function(d) {
                // Combinar los filtros del estado con los que vienen del DataTable
                d = { ...d, ...tabState.filtros };
            }
        },
        columns: [
            { data: 'id' },
            { data: 'cliente_nombre' },
            { data: 'planta_nombre' },
            { data: 'fecha' },
            { data: 'usuario_nombre' },
            { 
                data: 'estado',
                render: function(data) {
                    const badges = {
                        'ACTIVO': '<span class="badge bg-success">Activo</span>',
                        'INACTIVO': '<span class="badge bg-secondary">Inactivo</span>',
                        'PENDIENTE': '<span class="badge bg-warning text-dark">Pendiente</span>',
                        'COMPLETADO': '<span class="badge bg-primary">Completado</span>'
                    };
                    return badges[data] || '<span class="badge bg-light text-dark">' + data + '</span>';
                }
            },
            {
                data: null,
                render: function(data) {
                    return gcpArmarAcciones(data);
                }
            }
        ],
        language: { url: 'js/datatable/spanish.json' },
        pageLength: 50,
        processing: true,
        serverSide: true,
        dom: '<"top">rt<"bottom"lp>',
        initComplete: function() {
            console.log('Tabla de descargas inicializada correctamente');
        }
    });
});
</script>
?>
Paso 5: Actualizar acciones.php para cada tab
<!-- Archivo: modulos/<categoria>/<nombre>/acciones.php (ejemplo con 3 tabs) -->
<?php
class Formulario extends Base {
    // ===== UTILIDADES COMUNES =====
    use mymodule_helpers;  // utilidades como _consultar(), _obtener_fila(), etc.
    
    // ===== MÉTODOS DEL TAB =====
    function listar_tab1() {
        $this->validar_token();
        $this->validar();
        
        // 1. Leer parámetros de paginación y filtros del frontend
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 50;
        $search = isset($_POST['search']['value']) ? $this->db->escape_string($_POST['search']['value']) : '';
        
        // 2. Construir consulta segura con marcadores ?
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                id, cliente_nombre, planta_nombre, fecha, usuario_nombre, estado
            FROM tabla_principal
            WHERE 1=1
            AND (cliente_nombre LIKE ? OR planta_nombre LIKE ?)
            ORDER BY fecha DESC
            LIMIT ? OFFSET ?
        ";
        
        $params = [
            "%$search%",
            "%$search%",
            $length,
            $start
        ];
        
        $rows = $this->db->getConnection()->select($sql, $params);
        
        // 3. Contar total y filtrado
        $total = $this->db->select_one("SELECT FOUND_ROWS() as total");
        $filtrado = $this->db->select_one("SELECT COUNT(*) as total FROM tabla_principal WHERE cliente_nombre LIKE ? OR planta_nombre LIKE ?", 
                                         ["%$search%", "%$search%"]);
        
        // 4. Retornar JSON para DataTable
        $result = [
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            'recordsTotal' => $total['total'],
            'recordsFiltered' => $filtrado['total'],
            'data' => $rows
        ];
        
        $this->_success('', $result);
    }
    
    function agregar_tab1() {
        $this->validar_token();
        $this->validar();
        
        // Validaciones del formulario
        if (empty($_POST['cliente_id'])) {
            $this->_error('Debe seleccionar un cliente');
        }
        if (empty($_POST['planta_id'])) {
            $this->_error('Debe seleccionar una planta');
        }
        if (empty($_POST['fecha'])) {
            $this->_error('La fecha es obligatoria');
        }
        
        // Insertar en la base de datos
        $data = [
            'cliente_id' => intval($_POST['cliente_id']),
            'planta_id' => intval($_POST['planta_id']),
            'fecha' => $this->db->escape_string($_POST['fecha']),
            'usuario_id' => $_SESSION['persona_id'],
            'estado' => 'ACTIVO',
            'observacion' => $this->db->escape_string($_POST['observacion'] ?? '')
        ];
        
        $id = $this->db->insert('tabla_principal', $data);
        
        if ($id) {
            $this->_historiar('crear_registro_tab1', $id, 0, 'Registro creado en tab1: ' . $id);
            $this->_success('Registro agregado correctamente', ['id' => $id]);
        } else {
            $this->_error('No se pudo agregar el registro');
        }
    }
    
    // ... más métodos: modificar_tab1, eliminar_tab1, obtener_tab1, etc.
}
?>
Paso 6: Configurar formulario.php para enlaces de tabs
<!-- Archivo: modulos/<categoria>/<nombre>/formulario.php (versión simplificada) -->
<?php
/**
 * MÓDULO: <Nombre del módulo en español>
 * Orquestador - Tabs Nav + Includes
 */

if ($_SESSION['usuario_rol'] == 4) {
    $es_super_admin = true;
} else {
    $es_super_admin = false;
}
?>
<!-- Scripts HOISTED (antes de includes) -->
<script type="text/javascript">
// ===== UTILERÍAS GLOBALES =====
const TOKEN_GLOBAL = '<?php echo $TOKEN_GLOBAL ?? ''; ?>';
const page_root = '<?php echo $PAGE_ROOT ?? ''; ?>';

function gcAjax(accion, datos, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', page_root + accion, true);
    xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var respuesta = JSON.parse(xhr.responseText);
                if (respuesta.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: respuesta.msg });
                } else if (callback) {
                    callback(respuesta);
                }
            } catch(e) {
                console.error('Error parsing response:', e);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar respuesta' });
            }
        } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error en el servidor' });
        }
    };
    
    xhr.onerror = function() {
        console.error('Network error');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    };
    
    var params = new URLSearchParams();
    for (var key in datos) {
        if (datos[key] !== null && datos[key] !== undefined) {
            params.append(key, datos[key]);
        }
    }
    xhr.send(params.toString());
}
</script>

<!-- ========== NAV DE PESTAÑAS =========== -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs border-tab border-0 mb-0 nav-secondary" id="topline-tab" role="tablist">
                        <!-- Los tabs son generados dinámicamente por el backend -->
                        <li class="nav-item">
                            <a class="nav-link active nav-border pt-0 txt-secondary" 
                               id="topline-top-user-tab" 
                               data-bs-toggle="tab" 
                               href="#topline-top-user" 
                               role="tab" 
                               aria-controls="topline-top-user" 
                               aria-selected="true">
                                <i class="ri-download-line"></i> Tab 1
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-border txt-secondary" 
                               id="topline-top-description-tab" 
                               data-bs-toggle="tab" 
                               href="#topline-top-description" 
                               role="tab" 
                               aria-controls="topline-top-description" 
                               aria-selected="false">
                                <i class="ri-bar-chart-line"></i> Tab 2
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-border txt-secondary" 
                               id="topline-top-informes-tab" 
                               data-bs-toggle="tab" 
                               href="#topline-top-informes" 
                               role="tab" 
                               aria-controls="topline-top-informes" 
                               aria-selected="false">
                                <i class="ri-file-text-line"></i> Tab 3
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="topline-tabContent">
                        <div class="tab-pane fade show active" id="topline-top-user" role="tabpanel">
                            <div class="card-body px-0 pb-0">
                                <?php include_once 'tabs/tab1.php' ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="topline-top-description" role="tabpanel">
                            <div class="card-body px-0 pb-0">
                                <?php include_once 'tabs/tab2.php' ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="topline-top-informes" role="tabpanel">
                            <div class="card-body px-0 pb-0">
                                <?php include_once 'tabs/tab3.php' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
?>
Paso 7: Actualizar el helper de navegación
En clases/<nom_helper>_helpers.php, agregar un método para generar los enlaces de los tabs:
// helpers.php
function armar_nav_tabs() {
    $tabs = [
        ['slug' => 'tab1', 'nombre' => 'Tab 1', 'icono' => 'ri-download-line', 'activo' => true],
        ['slug' => 'tab2', 'nombre' => 'Tab 2', 'icono' => 'ri-bar-chart-line', 'activo' => false],
        ['slug' => 'tab3', 'nombre' => 'Tab 3', 'icono' => 'ri-file-text-line', 'activo' => false]
    ];
    
    $html = '<ul class="nav nav-tabs border-tab border-0 mb-0 nav-secondary" id="topline-tab" role="tablist">';
    foreach ($tabs as $tab) {
        $activeClass = $tab['activo'] ? 'active' : '';
        $ariaSelected = $tab['activo'] ? 'true' : 'false';
        
        $html .= '<li class="nav-item">';
        $html .= '<a class="nav-link nav-border txt-secondary ' . $activeClass . '" ';
        $html .= 'id="topline-' . $tab['slug'] . '-tab" ';
        $html .= 'data-bs-toggle="tab" href="#topline-' . $tab['slug'] . '" ';
        $html .= 'role="tab" aria-controls="topline-' . $tab['slug'] . '" ';
        $html .= 'aria-selected="' . $ariaSelected . '">';
        $html .= '<i class="' . $tab['icono'] . '"></i> ' . $tab['nombre'] . '</a>';
        $html .= '</li>';
    }
    $html .= '</ul>';
    
    return $html;
}
🚀 Paso 8: Configuración de enrutamiento
En configuracion.php (si existe):
<?php
// Enrutamiento del módulo - debe coincidir con el slug del menú
$module_config = [
    '<categoria>/<nombre>' => [
        'nombre' => '<Nombre del módulo>',
        'descripcion' => '<Breve descripción del módulo>',
        'icono' => '<ri-*-line>',
        'orden' => 100, // orden en el menú
        'acceso' => '7' // 1=publico, 3=logueados, 7=por rol, 8=prohibido
    ]
];
?>
En index.php (no modificar - ya está implementado por el sistema):
El sistema detecta automáticamente cualquier módulo bajo modulos/ y enruta basado en admin_menu:
// El index.php existente ya maneja esto automáticamente:
// 1. Index.php parsea REQUEST_URI → $_PARAMS[0] = menu_slug, $_PARAMS[1] = accion
// 2. Luego busca en admin_menu JOIN admin_accion JOIN admin_tipo_accion
// 3. Carga archivo (acciones.php o pagina.php) según admin_tipo_accion
// 4. El módulo puede tener su propio index.php dentro de modulos/<categoria>/<nombre>/ si es necesario
🔑 Reglas de seguridad clave
1. Regla de oro anti-inyección SQL:
// ❌ MALO - concatenación directa
$sql = "SELECT * FROM tabla WHERE id = " . $id;

// ✅ BUENO - marcadores de posición
$sql = "SELECT * FROM tabla WHERE id = ?";
$rows = $this->db->getConnection()->select($sql, [$id]);
2. Validación de IDs:
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
3. Seguridad de sesiones:
// Siempre verificar que el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    $this->_error('Debe iniciar sesión');
}
🎨 Estilo de desarrollo y convenciones
1. Nomenclatura de archivos:
- acciones.php - backend, métodos coinciden con acciones de admin_accion
- formulario.php - frontend orquestador
- tabs/*.php - cada pestaña
2. Nomenclatura de variables:
// En español y descriptiva
$fecha_inicio = ...;        // NO $date1
$nombre_cliente = ...;      // NO $cust_name
$planta_seleccionada = ...; // NO $selPlant
3. Estructura de comentarios:
// 1. Breve descripción de lo que hace
// 2. Por qué es necesario
// 3. Referencias si es necesario
🧪 Flujo de trabajo de pruebas
Pruebas unitarias:
1. PHP -l para errores de sintaxis: php -l modulos/<categoria>/<nombre>/acciones.php
2. DataTable init - verificar que el DataTable se inicialice correctamente en cada tab
3. CORS/AJAX - verificar que las peticiones gcAjax funcionen
4. Permisos - probar con diferentes roles de usuario (1, 3, 4, 8)
Pruebas funcionales:
1. Tab 1: Probar listar, agregar, modificar, eliminar
2. Tab 2: Probar visualizaciones/gráficos
3. Tab 3: Probar exportación de reportes
4. Navegación: Probar cambio entre tabs
5. Filtros: Probar funcionalidad de filtros
📊 Lista de verificación de implementación
Backend (acciones.php):
- Validar token para todos los métodos
- Implementar todos los endpoints requeridos
- Probar inserciones, actualizaciones, eliminaciones
- Verificar límites de permisos por rol
Frontend (formulario.php + tabs):
- Nav de pestañas renderizado correctamente
- DataTable inicializado en cada tab
- Utilidades globales cargadas (gcAjax, gcEsc, etc.)
- Handlers de eventos functionales
- Modales y UI responsivos
Archivos adicionales:
- clases/<nom_helper>_helpers.php - utilidades
- clases/<nom_helper>_estructura.php - navegación (opcional)
- clases/<nom_helper>_carpetas.php - gestión de carpetas (opcional)
- clases/<nom_helper>_documentos.php - gestión de archivos (opcional)
🎯 Resumen final
Desarrollo de módulos con tabs:
1. ✅ Estructura de directorios (acciones.php, formulario.php, tabs/)
2. ✅ Backend con patrón libre o CRUD
3. ✅ Frontend con Nav de pestañas + includes por tab
4. ✅ Scripts de utilerías HOISTED (gcAjax, gcEsc, etc.)
5. ✅ Cada tab con DataTable + filtros funcionales
6. ✅ Código limpio siguiendo reglas del módulo AGENTS.md §10
¡Feliz desarrollo! 🚀