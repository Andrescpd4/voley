# AGENTS.MD — Sistema Voley+

## 1. Arquitectura del Sistema

### Stack
- **PHP** 8.x, **MySQL** / MariaDB
- **jQuery** 3.4.1 + **Bootstrap 5** (theme **Velzon**)
- Assets locales en `plantilla/assets/libs/` (Leaflet, SweetAlert2, Select2, Dropzone, ApexCharts, Remixicons, Feather icons)
- **Composer** con: `illuminate/database`, `firebase/php-jwt`, `vlucas/phpdotenv`, `minishlink/web-push`

### Ubicación
- Raíz web: `C:\xampp\htdocs\voley\`
- URL local: `http://localhost/voley/`
- Hosting futuro: `https://dominio/voley/` (`.htaccess` con `RewriteBase /voley/`)

### Enrutamiento (idéntico a logistics)
```
.htaccess  →  index.php  →  admin_menu + admin_accion + admin_tipo_accion
```
1. `.htaccess` reescribe toda URL no-archivo a `index.php`
2. `index.php` parsea `$_SERVER['REQUEST_URI']` → `$_PARAMS[0]` (menu slug), `$_PARAMS[1]` (accion)
3. Busca en `admin_menu JOIN admin_accion JOIN admin_tipo_accion` y carga el `archivo` indicado

### Flujo de Página (tipo_accion = 'pagina')
```
index.php  →  pagina.php  →  ingreso.php  →  cabeza.php + formulario.php + pie.php
```

### Flujo JSON (tipo_accion = 'json')
```
index.php  →  descarga.php  →  obtener_ruta_menu()  →  acciones.php
```

---

## 2. Componentes REUTILIZABLES de logistics

> Estos archivos de `C:\xampp\htdocs\logistics\app\` se copian tal cual al proyecto Voley+:

### 2.1 Núcleo del Framework
| Archivo | Propósito |
|---------|-----------|
| `index.php` | Front Controller: parsea URL, despacha a pagina.php o descarga.php |
| `.htaccess` | Reescritura SEO + seguridad (bloquear exploits) |
| `pagina.php` | Decide si mostrar ingreso (logueado) o login (invitado) |
| `ingreso.php` | Incluye cabeza.php + formulario.php + pie.php |
| `ingresar.php` | Formulario de login público |
| `descarga.php` | Punto de entrada JSON: define ACCION, incluye acciones.php del módulo |
| `externos.php` | Bootstrap: carga vendor, headers de seguridad, sesión, config |
| `configuracion.php` | Lee .env (DB_HOST, JWT_SECRET, etc.) |
| `sesion.php` | Helper de sesión |
| `navegador.php` | Compatibilidad navegadores |

### 2.2 PHP Core (`php/`)
| Archivo | Propósito |
|---------|-----------|
| `db_conect.php` | Clase que extiende `Illuminate\Database\Capsule\Manager` (Eloquent) |
| `clase_base.php` | Clase `Base` con `$db` (para formularios libres) |
| `formulario_basico.php` | Clase para CRUDs simples (listar, agregar, modificar, eliminar, asignar) |
| `funciones_general.php` | Helpers: `obtener_ruta_menu()`, `llenar_combo()`, `validar_token()`, AES encrypt/decrypt, bitácoras, etc. |
| `validation.php` | Clase `Validation` para reglas de formularios |
| `funciones_select.php` | Helpers para Select2 |

### 2.3 Vistas
| Archivo | Propósito |
|---------|-----------|
| `cabeza.php` | `<head>`, topbar, sidebar, menú dinámico, constantes JS (`WEB_ROOT`, `PAGE_ROOT`, `TOKEN_GLOBAL`) |
| `pie.php` | Footer, carga de JS, oculta botones según permisos (`accion-{action}`) |
| `menu.php` | Genera el menú lateral recursivo desde `admin_menu` |
| `script_lia.php` / `style_lia.php` | Estilos y scripts personalizados globales |

### 2.4 Assets del Tema Velzon (`plantilla/`)
- `css/bootstrap.min.css`, `libs/bootstrap/js/bootstrap.bundle.min.js`
- `css/icons.min.css` (Remixicon + Boxicons)
- `js/sweet-alert/sweetalert.min.js` (alertas)
- `libs/select2/` (selects buscables)
- `libs/dropzone/dropzone-min.js` (subida de archivos drag&drop)
- `libs/apexcharts/apexcharts.min.js` (gráficos dashboard)
- `libs/feather-icons/feather.min.js` (iconos)
- `libs/toastify-js/` (notificaciones toast)

### 2.5 Dependencias Composer (`vendor/`)
```json
{
    "require": {
        "illuminate/database": "^11.23",
        "firebase/php-jwt": "^6.10",
        "vlucas/phpdotenv": "^5.6",
        "minishlink/web-push": "9.0",
        "gumlet/php-image-resize": "^2.0"
    }
}
```

### 2.6 Módulos de logistics que se pueden ADAPTAR
| Módulo logistics | Uso en Voley+ |
|------------------|---------------|
| `admin/usuarios/` | **Roles y Usuarios**: admin_usuario, admin_rol, admin_permiso |
| `admin/permisos_rol/` | Asignar menús/acciones a roles |
| `administracion/sg_sst/` | **Documentación**: estructura de carpetas, subida ZIP, historial |
| `administracion/gestion_permisos/` | **Autorizaciones**: flujo solicitud/aprobación (adaptar) |
| `administracion/anuncios/` | **Comunicados**: CRUD con imágenes y estados |
| `administracion/gestion_examenes/` | **Asistencia**: patrón de marcado masivo |
| `inicio/` | **Dashboard**: patrón set_token, favoritos, anuncios |
| `compartidos/` | Funciones JS compartidas (`gcAjax`, `gcEsc`, `gcDebounce`) |

---

## 3. Estructura de Módulos en Voley+

Cada módulo vive en `modulos/<categoria>/<nombre>/`:

```
modulos/<categoria>/<nombre>/
├── acciones.php       ← Backend: class Formulario extends Base { ... }
├── formulario.php     ← Orquestador: CSS + JS compartido + modales + includes de tabs
└── tabs/              ← Vistas parciales (HTML + JS autocontenido)
    ├── <tab1>.php
    └── <tab2>.php
```

### Patrón Backend (`acciones.php`)
```php
<?php
require_once __DIR__ . '/../../../php/clase_base.php';

class Formulario extends Base
{
    function mi_metodo()
    {
        $this->validar_token();
        // ... DB logic ...
        echo json_encode(['error'=>false, 'msg'=>'ok', 'data'=>...]);
    }
}
$accion = ACCION;
$f = new Formulario();
$f->$accion();
```

### Patrón CRUD Backend (`acciones.php`)
```php
<?php
require_once("php/formulario_basico.php");
class MiModulo extends formulario_basico {
    function validar() {
        $v = new Validation($_POST);
        $v->addRule("campo", "required", true, "Título");
        $result = $v->validate();
        if ($result['messages'] != "") {
            echo json_encode(['error'=>true, 'msg'=>$result['messages'], 'bad_fields'=>$result['bad_fields']]);
            exit(0);
        }
        return true;
    }
    function getSQL() {
        return "SELECT * FROM tabla WHERE visible=1";
    }
}
$accion = ACCION;
$f = new MiModulo("tabla", "id", true);
$f->$accion();  // listar, agregar, modificar, eliminar
```

### Comunicación Backend ↔ Frontend
```
gcAjax('listarDeportistas', { categoria: 'junior' }, callback)
  ↓ POST a page_root + 'listarDeportistas'
  ↓ .htaccess → index.php → descarga.php
  ↓ define('ACCION', 'listarDeportistas')
  ↓ include acciones.php → $f->listarDeportistas()
  ↓ echo json_encode(['error'=>false, 'msg'=>'OK', 'data'=>[...]])
  ↓ callback recibe JSON y renderiza la tabla
```

**Regla de oro:** Backend (`acciones.php`) nunca ve HTML. Frontend (`formulario.php` + `tabs/*.php`) nunca ve SQL.

---

## 4. Sistema de Permisos

### Tablas
| Tabla | Propósito |
|-------|-----------|
| `admin_menu` | Ítems del menú (slug, nombre, ruta, accion, icono) |
| `admin_accion` | Acciones por menú (ver, listar, agregar, etc.) |
| `admin_tipo_accion` | Mapea tipo → archivo: `pagina`→`pagina.php`, `json`→`descarga.php` |
| `admin_permiso_menu` | Roles que pueden ver un menú |
| `admin_permiso_accion` | Roles que pueden ejecutar una acción |
| `admin_rol` | Catálogo de roles |
| `admin_usuario` | Usuario → rol |

### Roles Voley+
| Rol ID | Nombre | Alcance |
|--------|--------|---------|
| 1 | Administrador Voley+ | Acceso total |
| 2 | Entrenador | Solo sus categorías, asistencia, eventos |
| 3 | Acudiente/Padre | Solo sus hijos asociados |
| 4 | Super Admin | Eliminar contenido, logs, auditoría |

### Niveles de acceso (`admin_menu.acceso`)
- `'1'` → público (todos)
- `'3'` → logueados
- `'7'` → por roles (requiere `admin_permiso_menu`)

### Ocultamiento automático (pie.php)
```html
<button class="accion-agregar">   ← visible solo si rol tiene permiso 'agregar'
<button class="accion-eliminar">  ← visible solo si rol tiene permiso 'eliminar'
```

---

## 5. Base de Datos — Tablas Principales Voley+

### 5.1 Usuarios y Personas
```sql
-- Persona física (deportista, acudiente, entrenador, admin)
CREATE TABLE persona (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('CC','TI','RC','CE','OTRO'),
    identificacion VARCHAR(20) UNIQUE,
    nombre1 VARCHAR(50), nombre2 VARCHAR(50),
    apellido1 VARCHAR(50), apellido2 VARCHAR(50),
    fecha_nacimiento DATE,
    celular VARCHAR(15), correo VARCHAR(100),
    direccion TEXT, foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuarios del sistema (uno por persona)
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    rol_id INT NOT NULL,
    login VARCHAR(50) UNIQUE,
    clave VARCHAR(255),  -- bcrypt/hash
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (persona_id) REFERENCES persona(id),
    FOREIGN KEY (rol_id) REFERENCES admin_rol(id)
);
```

### 5.2 Deportista y Acudientes
```sql
CREATE TABLE deportista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    categoria_id INT,          -- junior, infantil, etc.
    eps VARCHAR(100),
    alergias TEXT,
    contacto_emergencia_nombre VARCHAR(100),
    contacto_emergencia_telefono VARCHAR(15),
    estado ENUM('borrador','pendiente_revision','requiere_info','aprobado','no_aprobado','activo','inactivo') DEFAULT 'borrador',
    fecha_afiliacion DATE,
    FOREIGN KEY (persona_id) REFERENCES persona(id)
);

-- Relación Deportista ↔ Acudiente (un acudiente puede tener varios hijos)
CREATE TABLE deportista_acudiente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deportista_id INT NOT NULL,
    acudiente_id INT NOT NULL,    -- persona_id del acudiente
    parentesco ENUM('padre','madre','tio','abuelo','otro'),
    es_principal TINYINT(1) DEFAULT 0,
    FOREIGN KEY (deportista_id) REFERENCES deportista(id),
    FOREIGN KEY (acudiente_id) REFERENCES persona(id)
);
```

### 5.3 Documentación
```sql
CREATE TABLE tipo_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),         -- "Fotocopia CC", "Certificado EPS", etc.
    obligatorio TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
);

CREATE TABLE documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deportista_id INT NOT NULL,
    tipo_documento_id INT NOT NULL,
    archivo VARCHAR(255),        -- ruta en storage
    estado ENUM('pendiente','en_revision','aprobado','rechazado') DEFAULT 'pendiente',
    observaciones TEXT,
    subido_por INT,              -- persona_id
    fecha_subida DATETIME,
    revisado_por INT,
    fecha_revision DATETIME,
    FOREIGN KEY (deportista_id) REFERENCES deportista(id)
);
```

### 5.4 Autorizaciones
```sql
CREATE TABLE tipo_autorizacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200),
    contenido TEXT,              -- texto del documento
    version VARCHAR(20),
    activo TINYINT(1) DEFAULT 1
);

CREATE TABLE autorizacion_firmada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deportista_id INT NOT NULL,
    acudiente_id INT NOT NULL,    -- persona_id
    tipo_autorizacion_id INT NOT NULL,
    fecha_firma DATETIME,
    firma_electronica TEXT,      -- evidencia (IP, hash, etc.)
    aceptada TINYINT(1) DEFAULT 1,
    version_firmada VARCHAR(20),
    FOREIGN KEY (deportista_id) REFERENCES deportista(id),
    FOREIGN KEY (acudiente_id) REFERENCES persona(id),
    FOREIGN KEY (tipo_autorizacion_id) REFERENCES tipo_autorizacion(id)
);

-- Autorizaciones para eventos/salidas
CREATE TABLE autorizacion_evento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    deportista_id INT NOT NULL,
    acudiente_id INT NOT NULL,
    autoriza TINYINT(1),         -- 1=autoriza, 0=no autoriza
    fecha_respuesta DATETIME,
    firma_electronica TEXT
);
```

### 5.5 Asistencia
```sql
CREATE TABLE clase (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    categoria_id INT,
    entrenador_id INT,           -- persona_id
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME
);

CREATE TABLE asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clase_id INT NOT NULL,
    deportista_id INT NOT NULL,
    estado ENUM('asistio','no_asistio','excusa','tarde') NOT NULL,
    observaciones TEXT,
    fecha_marcacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clase_id) REFERENCES clase(id),
    FOREIGN KEY (deportista_id) REFERENCES deportista(id)
);
```

### 5.6 Eventos/Torneos
```sql
CREATE TABLE evento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200),
    tipo ENUM('torneo','entrenamiento','salida','reunion','otro'),
    fecha DATE,
    lugar VARCHAR(200),
    horario TEXT,
    recomendaciones TEXT,
    requiere_autorizacion TINYINT(1) DEFAULT 0,
    creado_por INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE evento_deportista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    deportista_id INT NOT NULL,
    convocado TINYINT(1) DEFAULT 1,
    FOREIGN KEY (evento_id) REFERENCES evento(id),
    FOREIGN KEY (deportista_id) REFERENCES deportista(id)
);
```

### 5.7 Comunicados
```sql
CREATE TABLE comunicado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200),
    contenido TEXT,
    destinatario_tipo ENUM('todos','categoria','grupo','individual'),
    destinatario_id INT,          -- categoría_id o grupo_id o persona_id
    creado_por INT,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    confirmacion_lectura TINYINT(1) DEFAULT 0
);

CREATE TABLE comunicado_lectura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comunicado_id INT NOT NULL,
    persona_id INT NOT NULL,
    fecha_lectura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comunicado_id) REFERENCES comunicado(id),
    FOREIGN KEY (persona_id) REFERENCES persona(id)
);
```

---

## 6. Desarrollo por Prioridad

### Prioridad 1: Usuarios y Roles
**Reutilizar:** `admin/usuarios/` + `admin/roles/` + `admin/permisos_rol/`

Módulo: `modulos/admin/usuarios/`
- CRUD de usuarios (adaptar de logistics)
- Asignar rol (Acudiente, Entrenador, Admin)
- Permisos por rol (qué menús/acciones ve cada rol)
- Login con JWT (ya implementado en `inicio/set_token`)

**Tablas nuevas:** `persona`, `usuario` (adaptar `admin_usuario`)

### Prioridad 2: Afiliación y Ficha del Deportista
**Nuevo módulo:** `modulos/afiliacion/`

Módulo: `modulos/afiliacion/`
- `formulario.php`: wizard de afiliación (tabs: Datos Deportista → Acudientes → Documentos → Autorizaciones)
- `tabs/datos_deportista.php`: formulario ficha completa
- `tabs/acudientes.php`: asociar padres/acudientes
- `tabs/documentos_adjuntos.php`: subir docs requeridos
- `tabs/estado_proceso.php`: barra de progreso + pendientes
- **Estados:** borrador → pendiente_revision → requiere_info → aprobado → activo
- **Observaciones administrativas** + devolver a corrección

**Backend:**
- `acciones.php`: guardarParcial(), enviarRevision(), aprobar(), devolver(), rechazar()

**Tablas:** `deportista`, `deportista_acudiente`, `tipo_documento`, `documento`

### Prioridad 3: Documentación
**Adaptar:** `administracion/sg_sst/` (estructura carpetas) + `gestion_documentos/` (flujos)

Módulo: `modulos/documentacion/`
- Configurar tipos de documentos obligatorios
- Carga por acudiente (drag&drop con Dropzone)
- Flujo de revisión: pendiente → en_revision → aprobado/rechazado
- Observaciones por documento
- Vista rápida: deportistas con docs completos vs pendientes

### Prioridad 4: Autorizaciones y Firma Digital
**Adaptar:** `gestion_permisos/` (flujo aprobación)

Módulo: `modulos/autorizaciones/`
- CRUD parametrizable de tipos de autorización (admin crea nuevos)
- Contenido HTML editable, versionado
- Acudiente lee → acepta → firma (evidencia: IP, fecha, hash, user-agent)
- Trazabilidad completa (quién, cuándo, qué versión)
- Solicitar nueva firma si cambia versión
- Dashboard: firmas pendientes vs completadas

### Prioridad 5: Aprobación Administrativa
Módulo: `modulos/afiliacion/admin/`
- Lista de solicitudes pendientes con filtros
- Ver ficha completa del deportista
- Aprobar/Rechazar/Devolver con observaciones
- Al aprobar: genera ficha activa automáticamente (copia datos del borrador)

### Prioridad 6: Control de Asistencia
**Adaptar:** `gestion_examenes/` (marcado masivo)

Módulo: `modulos/asistencia/`
- Entrenador crea/selecciona clase
- Lista de deportistas de la categoría
- Marcar: asistió / no asistió / excusa / llegada tarde
- Histórico por deportista y por periodo
- Dashboard: % asistencia

### Prioridad 7: Eventos y Permisos de Salida
Módulo: `modulos/eventos/`
- CRUD de eventos/torneos (nombre, fecha, lugar, horario, recomendaciones)
- Convocar deportistas (individual, por categoría, todas)
- Si requiere autorización: acudiente recibe notificación
- Acudiente autoriza/no autoriza + firma electrónica
- Dashboard: quiénes autorizaron y quiénes faltan

### Prioridad 8: Comunicados
**Adaptar:** `administracion/anuncios/` (CRUD con imágenes)

Módulo: `modulos/comunicados/`
- Publicar comunicado (título, contenido, destinatario)
- Destinatarios: todos, categoría, grupo, individual
- Confirmación de lectura (opcional)
- Historial de comunicados por destinatario
- Integración con notificaciones push (web-push ya instalado)

### Prioridad 9: Panel Administrativo (Dashboard)
Módulo: `modulos/inicio/`
- Cards: deportistas activos, nuevas solicitudes, docs pendientes, autorizaciones pendientes
- Gráfico asistencia mensual (ApexCharts)
- Próximos eventos
- Actividad reciente (bitácora)
- Accesos rápidos (favoritos)

---

## 7. Regla de Desarrollo: Código Fácil de Leer (OBLIGATORIA)

> Todo código nuevo sigue esta regla. El usuario es principiante.

### JavaScript

1. **Plantillas con backticks** para armar HTML. Prohibido pegar con comillas y `+`
2. **Una función pequeña por pieza de HTML**: `crearFilaSoporte(soporte)` devuelve template
3. **Bucles `for` normales** (`for (var i = 0; i < lista.length; i++)`). Prohibido `forEach`
4. **`if/else` en vez de ternarios** (`x ? y : z` prohibido)
5. **Prohibidos operadores modernos**: `??`, `?.`, spread, destructuring, arrow functions
6. **Comentarios en español** antes de cada bloque
7. **Nombres en español y descriptivos**: `nombre_completo`, `total_soportes`
8. **Se permite conservar**: configuración DataTables, `ACCION`, `validar_token`, `page_root`

### PHP

1. **`if/else` en vez de ternarios y `??`**
2. **Funciones cortas** con comentarios numerados por paso
3. **Cuidado con `select_one()`**: si no hay fila devuelve array() vacío, comprobar con `is_string()`
4. **Nombres claros en español**: `$cargo_colaborador`, `$fecha_aprobacion`
5. **Comentario de bloque por sección**

### Seguridad SQL — Regla de Oro
NUNCA concatenar variables en SQL. Usar:
- **Helpers con `?`**: `_consultar("SELECT * FROM t WHERE id = ?", array($id))`
- **`intval()`** para IDs
- **`$db->insert()` / `$db->update()`** para INSERT/UPDATE simples

---

## 8. Módulos Planificados (Estructura de Carpetas)

```
voley/
├── .htaccess
├── index.php
├── pagina.php
├── ingreso.php
├── ingresar.php
├── descarga.php
├── cabeza.php
├── pie.php
├── menu.php
├── configuracion.php
├── externos.php
├── sesion.php
├── plantilla/           ← Tema Velzon (copiar de logistics)
├── php/
│   ├── db_conect.php
│   ├── clase_base.php
│   ├── formulario_basico.php
│   ├── funciones_general.php
│   ├── validation.php
│   └── funciones_select.php
├── modulos/
│   ├── admin/
│   │   ├── usuarios/        ← Prioridad 1
│   │   ├── roles/           ← Prioridad 1
│   │   └── permisos_rol/    ← Prioridad 1
│   ├── afiliacion/
│   │   ├── datos_deportista/  ← Prioridad 2
│   │   ├── documentos_adjuntos/ ← Prioridad 3
│   │   └── admin_aprobacion/  ← Prioridad 5
│   ├── autorizaciones/      ← Prioridad 4
│   ├── asistencia/          ← Prioridad 6
│   ├── eventos/             ← Prioridad 7
│   ├── comunicados/         ← Prioridad 8
│   └── inicio/              ← Prioridad 9 (Dashboard)
├── storage/               ← Archivos subidos (documentos, fotos)
├── vendor/                ← Composer
└── .env                   ← Config secreta (DB, JWT, etc.)
```

---

## 9. Verificación al Terminar Código Nuevo

- `php -l` sin errores.
- `node --check` del bloque JS.
- **Escáner de emojis = 0** (solo íconos Remixicon/Feather del tema).
- Explicar al usuario cada parte en lenguaje simple.

---

## 10. Notas para el Desarrollador

### Cómo arrancar el proyecto Voley+
1. Copiar carpeta `plantilla/` completa de logistics a voley
2. Copiar archivos raíz: `index.php`, `.htaccess`, `pagina.php`, `ingreso.php`, `ingresar.php`, `descarga.php`, `cabeza.php`, `pie.php`, `menu.php`, `configuracion.php`, `externos.php`, `sesion.php`, `script_lia.php`, `style_lia.php`
3. Copiar carpeta `php/` completa de logistics
4. Crear `.env` con credenciales DB + JWT_SECRET
5. Ejecutar `composer install`
6. Crear tablas con scripts SQL (ver sección 5)
7. Registrar menús en `admin_menu`, `admin_accion`, `admin_tipo_accion`
8. Empezar por Prioridad 1 (Usuarios y Roles)

### Convenciones de Nomenclatura
- **PHP clases**: `PascalCase` → `Formulario`, `Admin_usuario`, `DeportistaController`
- **PHP métodos/funciones**: `camelCase` → `listarDeportistas()`, `guardarParcial()`
- **JS funciones**: `camelCase` prefijo módulo → `gdpListarDeportistas()`, `gdpGuardarFirma()`
- **JS variables**: `snake_case` → `lista_deportistas`, `total_documentos`
- **PHP variables**: `snake_case` → `$nombre_completo`, `$fecha_aprobacion`
- **Tablas BD**: `snake_case` → `deportista`, `tipo_documento`, `autorizacion_firmada`
- **Columnas BD**: `snake_case` → `persona_id`, `fecha_nacimiento`, `created_at`

### Assets Disponibles (Velzon)
| Asset | Uso principal |
|-------|---------------|
| Bootstrap 5 | Layout, grid, modales, tabs, cards |
| SweetAlert2 | Alertas de confirmación, éxito, error |
| Select2 | Selects con búsqueda (categorías, deportistas) |
| Dropzone | Subida de documentos (drag&drop múltiple) |
| ApexCharts | Gráficos dashboard (asistencia, estadísticas) |
| Remixicon | Íconos del tema (ri-user-line, ri-file-line, etc.) |
| ToastifyJS | Notificaciones toast emergentes |

### API Endpoints para Desarrollo Futuro
```php
// Push notifications (minishlink/web-push ya instalado)
// Guardar suscripción del navegador
// Enviar notificación: nuevo comunicado, documento rechazado, autorización pendiente
```
