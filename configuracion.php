<?php
// ============================================================
// CONFIGURACION.PHP — Lee variables del .env y define constantes
// ============================================================

// Conexion a BD
$cfg['db_host'] = $_ENV['DB_HOST'] ?? 'localhost';
$cfg['db_user'] = $_ENV['DB_USERNAME'] ?? 'root';
$cfg['db_password'] = $_ENV['DB_PASSWORD'] ?? '';
$cfg['db_database_name'] = $_ENV['DB_DATABASE'] ?? 'voley_plus';
$cfg['db_port'] = $_ENV['DB_PORT'] ?? '3306';
$cfg['db_driver'] = $_ENV['DB_CONNECTION'] ?? 'mysql';
$cfg['charset'] = $_ENV['charset'] ?? 'utf8';
$cfg['collation'] = $_ENV['collation'] ?? 'utf8_unicode_ci';

// Ajustes PHP
ini_set('memory_limit', '10000M');
ini_set('max_input_vars', '10000');
ini_set('max_execution_time', '60000');
ini_set('max_input_time', '-1');

// Reporte de errores
error_reporting(E_ERROR | E_PARSE);

// Menu de inicio segun estado de sesion
if (isset($_SESSION['nombre_usuario'])) {
    $cfg['menu_inicio'] = 'inicio';
} else {
    $cfg['menu_inicio'] = 'iniciar-sesion';
}

// Constantes globales
define("EMPRESA", $_ENV['NOMBRE_SISTEMA'] ?? 'Voley+');
define("NOMBRE_SISTEMA", $_ENV['NOMBRE_SISTEMA'] ?? 'Voley+');
define("SIGLA_SISTEMA", $_ENV['SIGLA_SISTEMA'] ?? 'VPLY');

// Rutas
define("RUTA_ARCHIVO", $_ENV['RUTA_ARCHIVO'] ?? '/storage/');
define("RUTA_TMP", $_ENV['RUTA_TMP'] ?? '/tmp/');
