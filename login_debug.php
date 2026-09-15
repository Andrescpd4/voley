<?php
// ============================================================
// LOGIN_DEBUG.PHP — Archivo independiente para probar el login
// sin pasar por el framework. Muestra cada paso del proceso.
// ============================================================

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DEBUG DE LOGIN</h1>";
echo "<pre>";

// 1. Cargar dependencias de Composer
echo "=== 1. CARGAR COMPOSER ===\n";
require 'vendor/autoload.php';
echo "✓ Composer cargado\n\n";

// 2. Cargar .env
echo "=== 2. CARGAR .ENV ===\n";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "\n";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NO DEFINIDO') . "\n";
echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NO DEFINIDO') . "\n";
echo "JWT_SECRET: " . (isset($_ENV['JWT_SECRET']) ? 'DEFINIDO' : 'NO DEFINIDO') . "\n\n";

// 3. Cargar configuración y conectar BD
echo "=== 3. CARGAR CONFIGURACION ===\n";
require_once("configuracion.php");
require_once("php/db_conect.php");
require_once("php/funciones_general.php");

echo "Intentando conectar a BD...\n";
$db = new db_conect();
$db->pconnect(
    $cfg['db_host'],
    $cfg['db_user'],
    $cfg['db_password'],
    $cfg['db_database_name'],
    $cfg['db_port'],
    $cfg['db_driver'],
    $cfg['charset'],
    $cfg['collation']
);

if ($db->connect_error()) {
    echo "✗ ERROR DE CONEXION: " . $db->connect_error() . "\n";
    exit;
}
echo "✓ BD conectada correctamente\n\n";

// 4. Verificar tablas
echo "=== 4. VERIFICAR TABLAS ===\n";
$persona = $db->select_row("SELECT * FROM persona WHERE id = 1");
if ($persona) {
    echo "✓ Persona encontrada: " . $persona['nombre1'] . " " . $persona['apellido1'] . "\n";
    echo "  Usuario (user): " . $persona['user'] . "\n";
    echo "  Clave hash: " . $persona['clave'] . "\n";
} else {
    echo "✗ No se encontro persona con id=1\n";
}

$usuario = $db->select_row("SELECT * FROM admin_usuario WHERE persona_id = 1");
if ($usuario) {
    echo "✓ Usuario admin encontrado, rol: " . $usuario['rol_id'] . "\n";
} else {
    echo "✗ No se encontro usuario admin\n";
}

$rol = $db->select_row("SELECT * FROM admin_rol WHERE id = 1");
if ($rol) {
    echo "✓ Rol encontrado: " . $rol['nombre'] . "\n";
}
echo "\n";

// 5. Verificar hash de contraseña
echo "=== 5. VERIFICAR HASH DE CONTRASEÑA ===\n";
$hash_calculado = clave('admin123');
echo "Hash de 'admin123': " . $hash_calculado . "\n";
echo "Hash en BD: " . ($persona['clave'] ?? 'N/A') . "\n";
echo "Coinciden: " . (($persona['clave'] ?? '') == $hash_calculado ? 'SI' : 'NO') . "\n\n";

// 6. Probar cifrado AES
echo "=== 6. PROBAR CIFRADO AES ===\n";
$password_test = "test_token";
$datos_test = "hola mundo";
$cifrado = AES_encrypt($datos_test, $password_test);
$descifrado = AES_decrypt($cifrado, $password_test);
echo "Original: $datos_test\n";
echo "Cifrado: " . substr($cifrado, 0, 50) . "...\n";
echo "Descifrado: " . ($descifrado ?: "FALLÓ") . "\n";
echo "AES funciona: " . ($descifrado === $datos_test ? 'SI' : 'NO') . "\n\n";

// 7. Probar flujo completo de login
echo "=== 7. PROBAR FLUJO COMPLETO DE LOGIN ===\n";

// 7a. Emitir token
$hash = strtotime(date('Y-m-d H:m:s')) * 27;
$token_hash = md5($hash);
$token = cifrar_texto(json_encode(['id_user' => 0, 'token_hash' => $token_hash]));
echo "Token emitido: " . substr($token, 0, 50) . "...\n";

$insert = $db->insert('admin_token', [
    'token' => $token,
    'caduca' => date('Y-m-d'),
    'id_user' => 0
]);
echo "Token insertado en BD (ID): $insert\n";

// 7b. Cifrar POST como lo hace CryptoJS
$campo_usuario_cifrado = AES_encrypt('usuario', $token);
$campo_clave_cifrado = AES_encrypt('clave', $token);
$valor_usuario_cifrado = AES_encrypt('admin', $token);
$valor_clave_cifrado = AES_encrypt('admin123', $token);

echo "POST cifrado preparado\n";

// 7c. Validar token (como hace validar_token_login)
$token_bd = $db->select_row("SELECT * FROM admin_token WHERE token = '" . $db->escape_string($token) . "' AND estado = 1");
echo "Token encontrado en BD: " . ($token_bd ? 'SI (ID: ' . $token_bd['id'] . ')' : 'NO') . "\n";

if ($token_bd) {
    echo "Token estado: " . $token_bd['estado'] . "\n";
    echo "Token caduca: " . $token_bd['caduca'] . "\n";
    echo "Fecha actual: " . date('Y-m-d') . "\n";
    echo "Caduca >= hoy: " . ($token_bd['caduca'] >= date('Y-m-d') ? 'SI' : 'NO') . "\n";
    
    // Marcar como usado
    $db->update('admin_token', ['estado' => 2], ['id' => $token_bd['id']]);
    
    // 7d. Descifrar POST
    $post_cifrado = [
        $campo_usuario_cifrado => $valor_usuario_cifrado,
        $campo_clave_cifrado => $valor_clave_cifrado
    ];
    $post_descifrado = desencriptar_post($post_cifrado, $token);
    
    echo "\nPOST descifrado:\n";
    print_r($post_descifrado);
    
    // 7e. Buscar usuario
    $usuario = strtoupper(trim($post_descifrado['usuario'] ?? ''));
    echo "\nUsuario a buscar (strtoupper): $usuario\n";
    $persona = $db->select_row("SELECT * FROM persona WHERE user = '" . $db->escape_string($usuario) . "'");
    echo "Persona encontrada: " . ($persona ? $persona['nombre1'] : 'NO') . "\n";
    
    if ($persona) {
        // 7f. Verificar contraseña
        $clave_descifrada = $post_descifrado['clave'] ?? '';
        $hash_bd = $persona['clave'];
        $hash_calculado = clave($clave_descifrada);
        
        echo "\nClave descifrada: $clave_descifrada\n";
        echo "Hash calculado: $hash_calculado\n";
        echo "Hash en BD: $hash_bd\n";
        echo "Contraseña correcta: " . ($hash_bd == $hash_calculado ? 'SI' : 'NO') . "\n";
    }
} else {
    echo "\nERROR: Token no encontrado. Ultimos tokens en BD:\n";
    $tokens = $db->select_all("SELECT id, token, estado, caduca FROM admin_token ORDER BY id DESC LIMIT 5");
    foreach ($tokens as $t) {
        echo "  ID: {$t['id']}, Estado: {$t['estado']}, Caduca: {$t['caduca']}\n";
    }
}

echo "\n=== 8. PROBAR JWT ===\n";
use Firebase\JWT\JWT;
$key = $_ENV['JWT_SECRET'] ?? 'clave_secreta';
$payload = ['id_user' => 1, 'test' => true];
$jwt = JWT::encode($payload, $key, 'HS256');
echo "JWT generado: " . substr($jwt, 0, 50) . "...\n";
$decoded = JWT::decode($jwt, new Firebase\JWT\Key($key, 'HS256'));
echo "JWT decodificado: " . print_r($decoded, true) . "\n";

echo "\n=== FIN DEL DEBUG ===\n";
echo "</pre>";
