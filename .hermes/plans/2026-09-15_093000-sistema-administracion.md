# Plan: Sistema de Administración Voley+

## Objetivo
Replicar el sistema completo de administración de logistics (gestión de usuarios, roles, permisos, asistente de módulos) en Voley+, con el flujo correcto de registro de menús → acciones → permisos por rol.

## Contexto Actual

**Estado de Voley+:**
- Framework base: index.php, .htaccess, cabeza.php, pie.php, menu.php — ✅ Funcionando
- Login/Logout: ✅ Funcionando
- Módulos creados por subagentes (usuarios, roles, permisos-rol) — ❌ No funcionan (404)
- Faltan acciones en `admin_accion` para los módulos nuevos
- No existe el asistente de creación de módulos
- No existe módulo de gestión de menús

**Tablas de BD existentes:**
- `admin_menu`, `admin_accion`, `admin_tipo_accion`
- `admin_rol`, `admin_usuario`
- `admin_permiso_menu`, `admin_permiso_accion`
- `persona` (con user/clave)

**Menús registrados en admin_menu:**
```
iniciar-sesion (1)
inicio (2)
sesion (3, oculto)
admin (10, padre)
usuarios (11, hijo de admin)
roles (12, hijo de admin)
permisos-rol (13, hijo de admin)
```

**Acciones registradas (27 total) pero FALTAN:**
- roles: agregar, modificar, eliminar
- permisos-rol: cargar, guardar
- asistente: ver, crear, crear_crud, etc.

## Paso a Paso

### Paso 1: Registrar acciones faltantes en BD
**Archivo:** `C:\xampp\htdocs\voley\sql\actualizar_acciones.sql`

```sql
-- ============================================================
-- ACCIONES FALTANTES PARA MÓDULOS DE ADMINISTRACIÓN
-- ============================================================

-- Roles (CRUD completo)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso) VALUES
('roles', 'agregar', 'json', 'acciones.php', 'S'),
('roles', 'modificar', 'json', 'acciones.php', 'S'),
('roles', 'eliminar', 'json', 'acciones.php', 'S'),
('roles', 'asignar', 'json', 'acciones.php', 'N');

-- Permisos por rol (cargar/guardar)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso) VALUES
('permisos-rol', 'cargar', 'json', 'acciones.php', 'S'),
('permisos-rol', 'guardar', 'json', 'acciones.php', 'S');

-- Asistente (creador de módulos)
INSERT IGNORE INTO admin_menu (menu, padre, nombre, ruta, accion, orden, visible, acceso, icono) VALUES
('asistente', 'admin', 'Asistente', '#', 'ver', 14, 'S', '7', 'ri-magic-line'),
('asistente-crud', 'asistente', 'Crear CRUD', 'modulos/asistente/formulario_crud', 'ver', 15, 'S', '7', 'ri-add-line'),
('asistente-menu', 'asistente', 'Gestionar Menu', 'modulos/asistente/gestion_menu', 'ver', 16, 'S', '7', 'ri-menu-line');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso) VALUES
('asistente', 'ver', 'pagina', 'formulario.php', 'N'),
('asistente-crud', 'ver', 'pagina', 'formulario.php', 'N'),
('asistente-crud', 'cargarInfoFormulario', 'json', 'acciones.php', 'S'),
('asistente-crud', 'verificar', 'json', 'acciones.php', 'S'),
('asistente-crud', 'crear', 'json', 'acciones.php', 'S'),
('asistente-menu', 'ver', 'pagina', 'formulario.php', 'N'),
('asistente-menu', 'listar', 'json', 'acciones.php', 'S'),
('asistente-menu', 'agregar', 'json', 'acciones.php', 'S'),
('asistente-menu', 'modificar', 'json', 'acciones.php', 'S'),
('asistente-menu', 'eliminar', 'json', 'acciones.php', 'S');

-- Permisos del rol admin (id=1) a las nuevas acciones
INSERT IGNORE INTO admin_permiso_menu (rol, menu) VALUES
(1, 'asistente'), (1, 'asistente-crud'), (1, 'asistente-menu');

INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu IN ('roles', 'permisos-rol', 'asistente', 'asistente-crud', 'asistente-menu');
```

**Verificación:**
```bash
mysql -u root voley_plus < sql/actualizar_acciones.sql
mysql -u root voley_plus -e "SELECT menu, accion, archivo FROM admin_accion WHERE menu IN ('roles','permisos-rol','asistente','asistente-crud','asistente-menu') ORDER BY menu, accion;"
```

---

### Paso 2: Verificar y corregir módulo de Usuarios
**Archivo:** `C:\xampp\htdocs\voley\modulos\admin\usuarios\acciones.php`

Verificar que:
1. La clase extiende `clase_base` (no `formulario_basico` — no lo necesita)
2. Implementa `listar()`, `agregar()`, `modificar()`, `eliminar()`, `asignar()`, `listarPersonasSinUsuario()`, `listarRoles()`
3. Usa `validar_token()` al inicio de cada método
4. Responde JSON con `{error: false/true, msg: '...', data: [...]}`

**Verificación:**
```bash
# Desde el navegador (logueado):
# 1. Ir a http://localhost/voley/admin/usuarios
# 2. La tabla debe cargar automáticamente vía AJAX a page_root + 'listar'
```

**Correcciones probables:**
- El formulario.php debe cargar jQuery ANTES de los scripts del módulo
- Las rutas AJAX deben usar `page_root + 'accion'`

---

### Paso 3: Verificar y corregir módulo de Roles
**Archivo:** `C:\xampp\htdocs\voley\modulos\admin\roles\acciones.php`

Verificar que:
1. La clase `Rol` extiende `formulario_basico`
2. El constructor llama `parent::__construct('admin_rol', 'id', true)`
3. Implementa `validar()` y `getSQL()`

**Archivo:** `C:\xampp\htdocs\voley\modulos\admin\roles\formulario.php`

Verificar que:
1. Tiene la tabla con columnas: ID, Nombre, Usuarios, Acciones
2. Botón "Nuevo Rol" con clase `accion-agregar`
3. Modal para crear/editar

---

### Paso 4: Verificar y corregir módulo de Permisos por Rol
**Archivo:** `C:\xampp\htdocs\voley\modulos\admin\permisos-rol\acciones.php`

Verificar que:
1. Clase `PermisosRol` extiende `clase_base`
2. Método `cargar()`: recibe `$_POST['rol']`, devuelve permisos actuales en JSON
3. Método `guardar()`: recibe `$_POST['rol']`, `$_POST['menus']` (array), `$_POST['acciones']` (array)

**Archivo:** `C:\xampp\htdocs\voley\modulos\admin\permisos-rol\formulario.php`

Verificar que:
1. Select de roles (onChange → cargar())
2. Árbol de menús con checkboxes recursivos
3. Botón Guardar que envía todo por AJAX

---

### Paso 5: Crear módulo de Gestión de Menús (asistente/gestion_menu)
**Archivo:** `C:\xampp\htdocs\voley\modulos\asistente\gestion_menu\acciones.php`

```php
<?php
require_once("php/formulario_basico.php");

class Admin_menu extends formulario_basico {
    function validar() {
        $v = new Validation($_POST);
        $v->addRules('menu', 'Menu', array('required' => true, 'maxLength' => 100));
        $v->addRules('nombre', 'Nombre', array('required' => true, 'maxLength' => 50));
        $v->addRules('ruta', 'Ruta', array('maxLength' => 100));
        $v->addRules('accion', 'Accion', array('maxLength' => 60));
        $v->addRules('orden', 'Orden', array('required' => true, 'maxLength' => 6));
        $v->addRules('visible', 'Visible', array('required' => true, 'maxLength' => 1));
        $v->addRules('icono', 'Icono', array('maxLength' => 200));

        $result = $v->validate();
        if ($result['messages'] == "") {
            return true;
        } else {
            echo json_encode(['error' => true, 'msg' => $result['messages'], 'bad_fields' => $result['bad_fields']]);
            exit(0);
        }
    }

    function getSQL() {
        $s = "";
        if (isset($_GET["menu"]) && $_GET["menu"] != "" && $_GET["menu"] != "NULL") {
            $s .= " AND menu LIKE '%" . str_replace(" ", "%", $_GET['menu']) . "%' ";
        }
        if (isset($_GET["padre"]) && $_GET["padre"] != "" && $_GET["padre"] != "NULL") {
            $s .= " AND padre = '$_GET[padre]'";
        }
        return "SELECT * FROM admin_menu WHERE 1=1 $s ORDER BY padre, orden";
    }
}

$_POST['id'] = urlsafe_b64decode($_POST['id'] ?? '');
$_GET['id'] = urlsafe_b64decode($_GET['id'] ?? '');

$accion = ACCION;
$f = new Admin_menu("admin_menu", "id", true);
$f->$accion();
```

**Archivo:** `C:\xampp\htdocs\voley\modulos\asistente\gestion_menu\formulario.php`
- Tabla con: ID, Menu, Nombre, Padre, Ruta, Icono, Acciones
- Filtros: buscar por menu, filtrar por padre
- Modal CRUD

---

### Paso 6: Crear asistente de creación de módulos CRUD
**Archivo:** `C:\xampp\htdocs\voley\modulos\asistente\formulario_crud\acciones.php`

Debe implementar:
- `cargarInfoFormulario()`: Recibe tabla, devuelve columnas con checkboxes
- `verificar()`: Valida que no existan duplicados
- `crear()`: Genera archivos formulario.php y acciones.php del nuevo módulo

**Archivo:** `C:\xampp\htdocs\voley\modulos\asistente\formulario_crud\formulario.php`
- Formulario: seleccionar tabla, base de datos
- Tabla de columnas con checkboxes (incluir, titulo, grid, filtro)
- Botón "Crear Módulo" que llama a acciones.php

---

### Paso 7: Crear módulo padre del Asistente
**Archivo:** `C:\xampp\htdocs\voley\modulos\asistente\formulario.php`

Layout con tabs:
- "Crear CRUD" → include formulario_crud
- "Gestionar Menús" → include gestion_menu

---

### Paso 8: Registrar permisos para el rol administrador
Después de crear todos los módulos, ejecutar:

```sql
INSERT IGNORE INTO admin_permiso_menu (rol, menu)
SELECT 1, menu FROM admin_menu WHERE acceso = '7';

INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE requiere_permiso = 'S';
```

---

### Paso 9: Pruebas de integración

**Flujo completo:**
1. Login como admin → Dashboard
2. Click en "Administracion" en sidebar → abre dropdown
3. Click en "Usuarios" → tabla carga con AJAX
4. Click en "Roles" → lista de roles
5. Click en "Permisos por rol" → árbol de checkboxes
6. Click en "Asistente" → opciones para crear módulos

**Verificación por módulo:**

| Módulo | URL | Acción AJAX | Respuesta esperada |
|--------|-----|-------------|-------------------|
| Usuarios | /admin/usuarios | listar | JSON con array de usuarios |
| Usuarios | /admin/usuarios | agregar | JSON error:false |
| Roles | /admin/roles | listar | JSON con array de roles |
| Roles | /admin/roles | agregar | JSON error:false |
| Permisos | /admin/permisos-rol | cargar | JSON con permisos actuales |
| Permisos | /admin/permisos-rol | guardar | JSON error:false |
| Gestion Menu | /asistente/gestion_menu | listar | JSON con menús |

---

## Riesgos y consideraciones

1. **formulario_basico.php**: Ya fue creado por subagentes, verificar que funcione correctamente con `$db->insert()` y `$db->update()` de Eloquent.

2. **AES decrypt**: Verificar que `desencriptar_post()` funcione con el token correcto (mismo formato CryptoJS ↔ PHP).

3. **Session cookies**: El sistema usa cookies con nombres aleatorios, verificar que el navegador las acepte.

4. **Timezone**: PHP usa Europe/Berlin por defecto, los tokens de sesión se guardan con esa fecha. Si hay problemas, ajustar `date_default_timezone_set('America/Bogota')` en externos.php.

5. **Nombres de archivos**: Usar `_` (guión bajo) en nombres de archivos PHP, no `-` (guión) porque PHP no puede incluir archivos con guión en el nombre.

---

## Archivos a crear/modificar

**Nuevos:**
- `sql/actualizar_acciones.sql`
- `modulos/asistente/formulario.php`
- `modulos/asistente/gestion_menu/acciones.php`
- `modulos/asistente/gestion_menu/formulario.php`
- `modulos/asistente/formulario_crud/acciones.php`
- `modulos/asistente/formulario_crud/formulario.php`

**Modificar:**
- `modulos/admin/usuarios/acciones.php` (corregir si es necesario)
- `modulos/admin/roles/acciones.php` (corregir si es necesario)
- `modulos/admin/permisos-rol/acciones.php` (corregir si es necesario)
- `modulos/admin/usuarios/formulario.php` (corregir si es necesario)
- `modulos/admin/roles/formulario.php` (corregir si es necesario)
- `modulos/admin/permisos-rol/formulario.php` (corregir si es necesario)

---

## Orden de implementación

1. Paso 1: Registrar acciones en BD (SQL)
2. Paso 2-4: Verificar/corrergir módulos existentes (usuarios, roles, permisos)
3. Paso 5-6: Crear módulos del asistente (gestion_menu, formulario_crud)
4. Paso 7: Crear módulo padre del asistente
5. Paso 8: Registrar permisos
6. Paso 9: Pruebas de integración
