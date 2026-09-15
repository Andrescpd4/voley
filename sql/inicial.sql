-- ============================================================
-- VOLEY+ — Script de creacion de tablas base
-- ============================================================

-- Tabla de roles del sistema
CREATE TABLE IF NOT EXISTS admin_rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO admin_rol (id, nombre, descripcion) VALUES
(1, 'Administrador Voley+', 'Acceso total al sistema'),
(2, 'Entrenador', 'Gestion de asistencia y eventos'),
(3, 'Acudiente', 'Padre, madre o tutor del deportista'),
(4, 'Super Admin', 'Eliminar contenido, auditoria');

-- Tabla de personas (deportistas, acudientes, entrenadores, admins)
CREATE TABLE IF NOT EXISTS persona (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('CC','TI','RC','CE','OTRO') DEFAULT 'CC',
    identificacion VARCHAR(20) NOT NULL,
    nombre1 VARCHAR(50),
    nombre2 VARCHAR(50),
    apellido1 VARCHAR(50),
    apellido2 VARCHAR(50),
    fecha_nacimiento DATE,
    celular VARCHAR(15),
    correo VARCHAR(100),
    direccion TEXT,
    foto VARCHAR(255),
    plantas VARCHAR(50) DEFAULT '',
    user VARCHAR(50),
    clave VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_identificacion (identificacion)
) ENGINE=InnoDB;

-- Tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS admin_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    _usuario VARCHAR(50),
    _fecha DATETIME,
    FOREIGN KEY (persona_id) REFERENCES persona(id),
    FOREIGN KEY (rol_id) REFERENCES admin_rol(id)
) ENGINE=InnoDB;

-- Tabla de sesiones activas
CREATE TABLE IF NOT EXISTS admin_sesion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    usuario VARCHAR(50),
    user_agent TEXT,
    refer TEXT,
    ip VARCHAR(45),
    inicio DATETIME,
    fin DATETIME,
    salida VARCHAR(1) DEFAULT 'N'
) ENGINE=InnoDB;

-- Tabla de sesiones denegadas (log de intentos fallidos)
CREATE TABLE IF NOT EXISTS admin_sesion_denegada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    usuario VARCHAR(50),
    user_agent TEXT,
    refer TEXT,
    ip VARCHAR(45),
    fecha DATETIME,
    tipo INT
) ENGINE=InnoDB;

-- Tabla de tokens JWT (para autenticacion en AJAX)
CREATE TABLE IF NOT EXISTS admin_token (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token TEXT NOT NULL,
    caduca DATE NOT NULL,
    id_user INT NOT NULL,
    estado TINYINT(1) DEFAULT 1 COMMENT '1=activo, 2=usado'
) ENGINE=InnoDB;

-- Tabla de modulos del sistema
CREATE TABLE IF NOT EXISTS admin_tipo_accion (
    codigo VARCHAR(20) PRIMARY KEY,
    archivo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(100)
) ENGINE=InnoDB;

INSERT INTO admin_tipo_accion (codigo, archivo, descripcion) VALUES
('pagina', 'pagina.php', 'Carga pagina HTML completa'),
('json', 'descarga.php', 'Retorna JSON para AJAX');

-- Tabla de menus del sistema
CREATE TABLE IF NOT EXISTS admin_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu VARCHAR(50) NOT NULL UNIQUE,
    padre VARCHAR(50) DEFAULT NULL,
    nombre VARCHAR(100) NOT NULL,
    ruta VARCHAR(50) DEFAULT '',
    accion VARCHAR(20) DEFAULT 'ver',
    orden INT DEFAULT 0,
    visible VARCHAR(1) DEFAULT 'S',
    acceso VARCHAR(2) DEFAULT '3',
    icono VARCHAR(50) DEFAULT '',
    target VARCHAR(20) DEFAULT '_self',
    descripcion TEXT
) ENGINE=InnoDB;

INSERT INTO admin_menu (menu, padre, nombre, ruta, accion, orden, visible, acceso, icono) VALUES
('iniciar-sesion', NULL, 'Iniciar sesion', 'modulos/sesion', 'ver', 1, 'S', '1', 'ri-login-box-line'),
('inicio', NULL, 'Inicio', 'modulos/inicio', 'ver', 2, 'S', '3', 'ri-home-4-line'),
('sesion', NULL, 'Sesion', 'modulos/sesion', 'ver', 3, 'N', '1', ''),
('admin', NULL, 'Administracion', '#', 'ver', 10, 'S', '7', 'ri-settings-4-line'),
('usuarios', 'admin', 'Usuarios', 'modulos/admin/usuarios', 'ver', 11, 'S', '7', 'ri-user-line'),
('roles', 'admin', 'Roles', 'modulos/admin/roles', 'ver', 12, 'S', '7', 'ri-shield-user-line'),
('permisos-rol', 'admin', 'Permisos por rol', 'modulos/admin/permisos-rol', 'ver', 13, 'S', '7', 'ri-lock-password-line');

-- Tabla de acciones por menu
CREATE TABLE IF NOT EXISTS admin_accion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    tipo_accion VARCHAR(20) DEFAULT 'pagina',
    archivo VARCHAR(50) DEFAULT 'acciones.php',
    requiere_permiso VARCHAR(1) DEFAULT 'N',
    FOREIGN KEY (menu) REFERENCES admin_menu(menu)
) ENGINE=InnoDB;

INSERT INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso) VALUES
('iniciar-sesion', 'ver', 'pagina', 'formulario.php', 'N'),
('iniciar-sesion', 'iniciar', 'json', 'acciones.php', 'N'),
('iniciar-sesion', 'restaurar', 'json', 'acciones.php', 'N'),
('iniciar-sesion', 'set_login', 'json', 'acciones.php', 'N'),
('inicio', 'ver', 'pagina', 'formulario.php', 'N'),
('inicio', 'set_token', 'json', 'acciones.php', 'N'),
('inicio', 'dashboard', 'json', 'acciones.php', 'N'),
('sesion', 'ver', 'pagina', 'iniciar_sesion.php', 'N'),
('sesion', 'iniciar', 'json', 'acciones.php', 'N'),
('sesion', 'cerrar_sesion', 'pagina', 'cerrar_sesion.php', 'N'),
('sesion', 'restaurar', 'json', 'acciones.php', 'N'),
('sesion', 'set_login', 'json', 'acciones.php', 'N'),
('usuarios', 'ver', 'pagina', 'formulario.php', 'N'),
('usuarios', 'listar', 'json', 'acciones.php', 'S'),
('usuarios', 'agregar', 'json', 'acciones.php', 'S'),
('usuarios', 'modificar', 'json', 'acciones.php', 'S'),
('usuarios', 'eliminar', 'json', 'acciones.php', 'S'),
('roles', 'ver', 'pagina', 'formulario.php', 'N'),
('roles', 'listar', 'json', 'acciones.php', 'S'),
('permisos-rol', 'ver', 'pagina', 'formulario.php', 'N'),
('permisos-rol', 'listar', 'json', 'acciones.php', 'S'),
('permisos-rol', 'guardar', 'json', 'acciones.php', 'S');

-- Tabla de permisos de menus por rol
CREATE TABLE IF NOT EXISTS admin_permiso_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol INT NOT NULL,
    menu VARCHAR(50) NOT NULL,
    FOREIGN KEY (rol) REFERENCES admin_rol(id),
    FOREIGN KEY (menu) REFERENCES admin_menu(menu)
) ENGINE=InnoDB;

-- Tabla de permisos de acciones por rol
CREATE TABLE IF NOT EXISTS admin_permiso_accion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol INT NOT NULL,
    accion INT NOT NULL,
    FOREIGN KEY (rol) REFERENCES admin_rol(id),
    FOREIGN KEY (accion) REFERENCES admin_accion(id)
) ENGINE=InnoDB;

-- Tabla de logs del sistema
CREATE TABLE IF NOT EXISTS admin_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_persona INT,
    archivo VARCHAR(100),
    tipo INT,
    mensaje TEXT,
    menu VARCHAR(50),
    accion VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de bitacora del sistema
CREATE TABLE IF NOT EXISTS admin_bitacoras_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo_bitacora INT,
    datos_anteriores TEXT,
    datos_insertados TEXT,
    observacion TEXT,
    menu VARCHAR(50),
    id_usuario INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    menu VARCHAR(50),
    tipo VARCHAR(20),
    texto TEXT,
    url_destino VARCHAR(255),
    leido TINYINT(1) DEFAULT 0,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de dias festivos
CREATE TABLE IF NOT EXISTS festivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    descripcion VARCHAR(100)
) ENGINE=InnoDB;

-- Tabla de meses
CREATE TABLE IF NOT EXISTS mes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

INSERT INTO mes (id, nombre) VALUES
(1, 'Enero'), (2, 'Febrero'), (3, 'Marzo'), (4, 'Abril'),
(5, 'Mayo'), (6, 'Junio'), (7, 'Julio'), (8, 'Agosto'),
(9, 'Septiembre'), (10, 'Octubre'), (11, 'Noviembre'), (12, 'Diciembre');

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Usuario administrador por defecto (clave: admin123)
INSERT INTO persona (id, tipo_documento, identificacion, nombre1, apellido1, user, clave) VALUES
(1, 'CC', '1234567890', 'Administrador', 'Sistema', 'admin', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92');

INSERT INTO admin_usuario (persona_id, rol_id, activo) VALUES
(1, 1, 1);

-- Permisos para rol administrador
INSERT INTO admin_permiso_menu (rol, menu) VALUES
(1, 'inicio'), (1, 'usuarios'), (1, 'roles'), (1, 'permisos-rol');
