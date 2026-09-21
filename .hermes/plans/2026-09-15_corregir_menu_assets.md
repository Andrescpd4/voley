# Plan: Corrección Sistema de Administración Voley+

## Estado Actual

**Funcional:**
- Login ✅
- Framework base (index.php, .htaccess, cabeza.php, pie.php) ✅
- Assets de Velzon (Bootstrap 5, iconos, temas) ✅
- Módulos creados: usuarios, roles, permisos-rol, asistente ✅

**No funcional:**
- Menú lateral no muestra submódulos de administración
- Assets JS (choices.js, flatpickr) dan error MIME Type
- manifest.json no existe

## Problemas identificados

1. **Menu.php**: Tenía un error de sintaxis (comilla mal cerrada)
2. **plugins.js**: Las rutas de assets no incluyen `plantilla/` al inicio
3. **manifest.json**: No existe el archivo
4. **Permisos**: La consulta SQL en menu.php usa `u.rol` pero Voley+ tiene `u.rol_id`

## Correcciones Aplicadas

### 1. Menu.php
**Archivo:** `C:\xampp\htdocs\voley\menu.php`

Corregido error de sintaxis en la generación de enlaces del menú lateral. La consulta SQL ahora usa correctamente `u.rol_id=p.rol` para la tabla `admin_usuario`.

### 2. Plugins.js
**Archivo:** `C:\xampp\htdocs\voley\plantilla\assets\js\plugins.js`

Cambiadas las rutas de:
```javascript
// ANTES (roto)
'assets/libs/choices.js/public/assets/scripts/choices.min.js'

// DESPUÉS (funciona)
'plantilla/assets/libs/choices.js/public/assets/scripts/choices.min.js'
```

### 3. Manifest.json
**Archivo:** `C:\xampp\htdocs\voley\manifest.json`

Creado el archivo con la configuración de la PWA para Voley+.

### 4. Menú "asistente"
**BD:** `admin_menu`

Corregido el valor de `padre` de `'NULL'` (string) a `NULL` real para el menú `asistente`.

## Pendiente

- Reiniciar Apache para que los cambios surtan efecto
- Probar navegación: `http://localhost/voley/usuarios`
- Probar login y verificar que el menú muestra Administración > Usuarios, Roles, Permisos

## URLs a probar

1. `http://localhost/voley/` → Dashboard
2. `http://localhost/voley/usuarios` → Módulo de usuarios
3. `http://localhost/voley/roles` → Módulo de roles
4. `http://localhost/voley/permisos-rol` → Asignar permisos por rol
5. `http://localhost/voley/asistente` → Creador de módulos

## Temas de Velzon

El tema **galaxy** está disponible. Para aplicarlo:

1. En `cabeza.php`, cambiar `data-theme="default"` por `data-theme="galaxy"`
2. Agregar `data-bs-theme="dark"` (obligatorio para galaxy)
3. O usar el customizer lateral (engranaje derecho del tema)
