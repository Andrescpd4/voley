# Plan de Refactor: Módulo Afiliación - Voley+

## Objetivo
Implementar flujo completo de afiliación con 3 tabs principales:
1. **Registro** (Acudiente - Rol 3): Formulario completo para registrar deportista + documentos
2. **Mis Solicitudes** (Acudiente - Rol 3): Lista/historial simple de sus peticiones
3. **Administración** (Admin - Roles 1, 4): Gestión completa con revisión de estados

---

## Estructura de Tabs

| Tab | Rol | Descripción |
|-----|-----|-------------|
| **1. Registro** | Acudiente (3) | Formulario completo: Crear persona (deportista) + vínculo acudiente + subir documentos |
| **2. Mis Solicitudes** | Acudiente (3) | Lista/historial simple: Ver estado actual de sus peticiones (pendiente, aprobado, etc.) |
| **3. Administración** | Admin (1, 4) | Gestión completa: DataTable + filtros + revisar/editar estado + observaciones |

---

## Flujo Formulario Registro (Tab 1)

### Secciones (fieldsets colapsables)
1. **Datos Deportista**: Nombres, apellidos, tipo_doc (TI/CC), num_doc, fecha_nac, categoria, EPS, RH, alergias
2. **Contacto Deportista**: Celular, correo, dirección
3. **Datos Acudiente** (prellenado de sesión, editable): Nombres, apellidos, celular, correo, dirección, **parentesco** (radio: padre/madre/tutor/otro)
4. **Contacto Emergencia**: Nombre, teléfono, relación
5. **Información Club**: Observaciones, checkboxes autorizaciones (tratamiento datos, imagen, actividades, emergencia)
6. **Documentación**: **Modal** con lista de `tipo_documento` (TI/CC, EPS, Médico, Foto, Acudiente) → cada uno con botón "Subir" + vista previa

### Botones
- `Guardar Borrador` → estado `borrador` (valida mínimos: nombres, apellidos)
- `Enviar a Revisión` → estado `pendiente_revision` (valida todo + ≥1 documento)

---

## Tab "Mis Solicitudes" (Tab 2) - Simple

```html
<!-- DataTable simple sin server-side complejidad -->
<table id="misSolicitudesTabla">
  <thead>
    <tr style="background: #405189; color: white;">
      <th>#</th><th>Deportista</th><th>Estado</th><th>Fecha</th><th>Acciones</th>
    </tr>
  </thead>
</table>
```

- Columnas: #, Nombre deportista, Badge estado, Fecha solicitud, Acciones (Ver detalle)
- Modal "Ver": Ficha readonly con todos los datos + documentos subidos

---

## Tab "Administración" (Tab 3) - Ya Iniciada

**Mantener estilo lavado_cubetas ya implementado:**
- Accordion filtros (Estado, Búsqueda, Fechas)
- DataTable server-side `new DataTable()`
- Header `#405189`
- Columnas con botones: Ver, Cambiar Estado, Eliminar
- Modales: Ver (ficha completa), Cambiar Estado (select + observaciones)
- **SIN botón "Nueva Solicitud"** (confirmado)

---

## Backend - Acciones Necesarias (acciones.php)

| ID | Acción | Tab | Descripción |
|----|--------|-----|-------------|
| 55 | `listar_gestion` | Admin | DataTable server-side con filtros |
| 56 | `asignar_gestion` | Admin | Detalle ficha completa (modal Ver) |
| 57 | `modificar_gestion` | Admin | Cambiar estado + observaciones |
| 58 | `eliminar_gestion` | Admin | Soft delete |
| 59 | `listar_mis_solicitudes` | Acudiente | Lista paginada simple |
| 60 | `obtener_mis_solicitud` | Acudiente | Detalle para modal Ver |
| 61 | `crear_registro_acudiente` | Acudiente | **Persona + Deportista + Acudiente + Usuario + Docs** (borrador/envío) |
| 62 | `actualizar_registro_acudiente` | Acudiente | Editar borrador |
| 63 | `subir_documento_acudiente` | Acudiente | Subir 1 doc (tipo_documento_id) |
| 64 | `eliminar_documento_acudiente` | Acudiente | Borrar doc propio |
| 65 | `listar_categorias` | Compartido | Select categorías activas |
| 66 | `listar_tipos_documento` | Compartido | Select tipos doc (activos, obligatorios) |
| 67 | `listar_parentescos` | Compartido | Enum parentesco para radio buttons |
| 68 | `verificar_documento_unico` | Compartido | Validar documento no duplicado |

---

## Permisos por Rol

| Acción | Rol 1 (Admin) | Rol 4 (SuperAdmin) | Rol 3 (Acudiente) |
|--------|---------------|-------------------|-------------------|
| 55-58 (Admin) | ✅ | ✅ | ❌ |
| 59-64 (Acudiente) | ✅ | ✅ | ✅ |
| 65-68 (Compartidas) | ✅ | ✅ | ✅ |

---

## Generación Automática de Credenciales (Nuevo Requerimiento)

```php
// Al crear persona (deportista) en crear_registro_acudiente():
function _generar_credenciales_deportista($nombre1, $apellido1, $identificacion) {
    // Username: primera letra nombre1 + apellido1 + últimos 4 dígitos doc (lowercase)
    $username = strtolower(
        mb_substr($nombre1, 0, 1) . 
        $apellido1 . 
        substr($identificacion, -4)
    );
    // Ej: "Brayan Andres Montañez Niño / 1072649849" → "bmontañez9849"
    
    // Password: número de documento
    $password = $identificacion;
    
    return ['username' => $username, 'password' => $password];
}

// Insert en tabla usuario:
$credenciales = $this->_generar_credenciales_deportista($nombre1, $apellido1, $identificacion);
$hash = password_hash($credenciales['password'], PASSWORD_DEFAULT);
$this->db->insert('usuario', [
    'persona_id' => $persona_id,
    'rol_id' => 3, // Acudiente/Deportista
    'login' => $credenciales['username'],
    'password_hash' => $hash,
    'activo' => 1
]);
```

---

## Base de Datos

### Vistas Actualizar (`sql_views_afiliacion.sql`)
```sql
-- v_afiliacion: Expandir para incluir TODOS los campos del formulario
-- deportista: tipo_documento, identificacion, fecha_nacimiento, genero, celular, correo, direccion, rh, alergias, contacto_emergencia_nombre, contacto_emergencia_telefono
-- acudiente: tipo_documento, identificacion, celular, correo, direccion, parentesco
-- documento: join multiple para listar docs por tipo
```

### Acciones + Permisos (`sql_afiliacion_tabs.sql`)
```sql
-- Limpiar acciones 48-54 antiguas (listar, asignar, agregar, modificar, eliminar, listarAcudientes, listarDeportistas)
-- Insertar nuevas 55-68 (ver tabla arriba)
-- Permisos:
--   Rol 1,4: todas las acciones admin (55-59) + compartidas
--   Rol 3: acciones acudiente (60-68) + compartidas
--   Menu 'afiliacion' ya tiene permiso rol 3 (línea 927)
```

---

## Estructura Final de Archivos

```
modulos/afiliacion/
├── acciones.php                          ← Dispatcher principal
├── formulario.php                        ← Orquestador 3 tabs
├── tabs/
│   ├── utilerias_js.php                  ← JS compartido (HOISTED)
│   ├── registro.php                      ← Tab 1: Formulario completo acudiente
│   ├── mis_solicitudes.php               ← Tab 2: Lista simple + modal ver
│   └── gestion.php                       ← Tab 3: Admin (ajustar existente)
├── clases/
│   ├── afiliacion_helpers.php            ← Helpers base (ampliar)
│   ├── afiliacion_acudiente.php          ← NUEVO: Lógica Tab 1 + Tab 2
│   ├── afiliacion_admin.php              ← NUEVO: Lógica Tab 3
│   └── afiliacion_documentos.php         ← NUEVO: Gestión documentos
└── database/
    ├── sql_views_afiliacion.sql          ← Actualizar vistas
    └── sql_afiliacion_tabs.sql           ← Limpiar + nuevas acciones + permisos
```

---

## Modal Documentos (Diseño Móvil-Friendly)

```html
<!-- Botón en formulario abre modal -->
<button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalDocumentos">
  <i class="ri-file-list-line me-1"></i> Gestionar Documentos Requeridos
</button>

<!-- Modal -->
<div class="modal fade" id="modalDocumentos" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Documentos Requeridos</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Lista dinámica cargada via AJAX listar_tipos_documento -->
        <div class="list-group" id="listaDocumentosRequeridos">
          <!-- Por cada tipo_documento: -->
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1">Documento de Identidad (TI/CC)</h6>
              <small class="text-muted">Obligatorio · PDF · Máx 10MB</small>
              <span class="badge bg-success ms-2" id="doc-status-1">Subido</span>
            </div>
            <div class="btn-group">
              <button class="btn btn-sm btn-primary" onclick="subirDocumento(1)"><i class="ri-upload-cloud-line"></i></button>
              <button class="btn btn-sm btn-outline-info" onclick="verDocumento(1)"><i class="ri-eye-line"></i></button>
              <button class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento(1)"><i class="ri-delete-bin-line"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
```

```javascript
// Cargar lista al abrir modal
$('#modalDocumentos').on('show.bs.modal', function() {
    afiliacionAjax('listar_tipos_documento', {}, function(r) {
        var html = '';
        for (var i = 0; i < r.data.length; i++) {
            var doc = r.data[i];
            var badge = doc.subido ? 'bg-success' : (doc.obligatorio ? 'bg-warning' : 'bg-secondary');
            var badgeText = doc.subido ? 'Subido' : (doc.obligatorio ? 'Obligatorio' : 'Opcional');
            html += '<div class="list-group-item d-flex justify-content-between align-items-center">';
            html += '  <div><h6 class="mb-1">' + afiliacionEsc(doc.nombre) + '</h6>';
            html += '  <small class="text-muted">' + (doc.obligatorio ? 'Obligatorio' : 'Opcional') + ' · PDF · Máx 10MB</small></div>';
            html += '  <div><span class="badge ' + badge + ' me-2" id="doc-badge-' + doc.id + '">' + badgeText + '</span>';
            if (doc.subido) {
                html += '  <button class="btn btn-sm btn-outline-info" onclick="verDocumento(' + doc.id + ')"><i class="ri-eye-line"></i></button>';
                html += '  <button class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento(' + doc.id + ')"><i class="ri-delete-bin-line"></i></button>';
            } else {
                html += '  <button class="btn btn-sm btn-primary" onclick="subirDocumento(' + doc.id + ')"><i class="ri-upload-cloud-line"></i> Subir</button>';
            }
            html += '  </div></div>';
        }
        document.getElementById('listaDocumentosRequeridos').innerHTML = html;
    });
});
```

---

## Tab 2 - Mis Solicitudes (Simple)

```javascript
// DataTable simple (no server-side complejo)
misSolicitudesTabla = new DataTable('#misSolicitudesTabla', {
    ajax: { url: page_root + 'listar_mis_solicitudes', type: 'POST', headers: {'Authorization': TOKEN_GLOBAL} },
    columns: [
        { data: '_NUM_' },
        { data: 'deportista_nombre' },
        { data: 'estado', render: badgeEstado },
        { data: 'fecha_solicitud' },
        { data: null, render: function(d) { return '<button class="btn btn-sm btn-soft-info" onclick="verSolicitud('+d.id+')"><i class="ri-eye-line"></i> Ver</button>'; }, className: 'text-center' }
    ],
    language: { url: 'js/datatable/spanish.json' },
    pageLength: 20,
    order: [[3, 'desc']],
    dom: 'rtip' // Sin filtros complejos
});
```

---

## Tab 3 - Administración (Ajustes Mínimos)

- ✅ Ya tiene estilo lavado_cubetas
- ✅ Accordion filtros
- ✅ DataTable server-side con botones en columnas
- ✅ Modales Ver / Cambiar Estado
- **QUITAR**: Botón "Nueva Solicitud" (línea 19-21 en gestion.php actual)
- **CONFIRMAR**: Header `#405189`, accordion `accordion-light-primary txt-primary`

---

## Colores Voley+ (Confirmados)

| Uso | Color |
|-----|-------|
| Primary (headers, tabs active, btn-primary, accordion) | `#405189` |
| Success (Aprobado/Activo/Subido) | `#0ab39c` |
| Info (Pendiente/En revisión) | `#405189` |
| Warning (Requiere info/Borrador) | `#f7b84b` |
| Danger (Rechazado/No aprobado) | `#f06548` |
| Secondary (Inactivo) | `#6c757d` |

---

## Orden de Implementación

| Fase | Archivos | Descripción |
|------|----------|-------------|
| **1. BD** | `sql_views_afiliacion.sql`, `sql_afiliacion_tabs.sql` | Vistas expandidas + acciones/permisos limpios |
| **2. Clases** | `afiliacion_helpers.php`, `afiliacion_acudiente.php`, `afiliacion_admin.php`, `afiliacion_documentos.php` | Lógica separada por responsabilidad |
| **3. Backend** | `acciones.php` | Dispatcher usando 2 traits |
| **4. JS Shared** | `tabs/utilerias_js.php` | gcAjax, gcEsc, badgeEstado, barraProgreso, modalDocs |
| **5. Tabs** | `tabs/registro.php`, `tabs/mis_solicitudes.php`, `tabs/gestion.php` (ajuste) | 3 tabs completas |
| **6. Orquestador** | `formulario.php` | Nav 3 tabs + includes condicionales |
| **7. Verificación** | `php -l`, test manual | Validación completa |

---

## Validaciones Clave Backend

```php
// crear_registro_acudiente()
function crear_registro_acudiente() {
    // 1. Validar token + rol acudiente
    // 2. Validar campos según modo (borrador vs envío)
    //    - Borrador: solo nombre1, apellido1
    //    - Envío: todos los * + ≥1 documento
    // 3. Verificar documento único (persona.identificacion)
    // 4. Transacción:
    //    a) INSERT persona (deportista) → persona_id
    //    b) INSERT deportista (persona_id, categoria_id, eps, rh, alergias, contacto_emerg_*, estado='borrador'|'pendiente_revision')
    //    c) INSERT deportista_acudiente (deportista_id, acudiente_id=persona_acudiente, parentesco, es_principal=1)
    //    d) INSERT usuario (persona_id, rol_id=3, login=generado, password_hash=doc, activo=1)
    //    e) INSERT autorizacion_firmada (3 tipos) si checkboxes marcados
    // 5. Retornar ID + credenciales generadas
}
```

---

## Checklist Final de Entrega

- [ ] `php -l` sin errores en 11 archivos PHP
- [ ] SQL vistas + acciones ejecutan sin duplicados
- [ ] **Tab 1**: Formulario completa → Guardar Borrador (estado `borrador`) / Enviar Revisión (estado `pendiente_revision`); Modal docs gestiona 5 tipos; Credenciales auto-generadas; Validaciones cliente+servidor
- [ ] **Tab 2**: Lista muestra solicitudes del acudiente logueado; Badge estado colores voley+; Modal Ver ficha completa + docs
- [ ] **Tab 3**: DataTable carga; Filtros (estado, búsqueda, fechas); Modal Ver ficha completa; Modal Estado cambia + bitácora; Eliminar soft; Sin botón "Nueva"; Permisos `accion-*` ocultos
- [ ] Estilos: Nav `nav-secondary nav-border pt-0`; Accordion `accordion-light-primary txt-primary`; Headers `#405189`; Badges colores correctos
- [ ] Código: Sin emojis, comentarios español, `var`, `for`, backticks, if/else sin ternarios

---

## Referencia de Estilo: lavado_cubetas

Patrón a replicar en las 3 tabs:
- Nav tabs: `nav nav-tabs border-tab border-0 mb-0 nav-secondary`
- Active: `nav-link active nav-border pt-0 txt-secondary nav-secondary`
- Inactive: `nav-link nav-border txt-secondary nav-secondary`
- Accordion: `accordion-light-primary txt-primary` con iconos feather
- DataTable: `new DataTable('#id', { layout: { topStart: { buttons: [...] }}})` → **NO USAR**, botones externos
- Header tabla: `<tr style="background: #405189; color: white;">`
- Botones en columnas de tabla (Ver, Estado, Eliminar) → abren modales