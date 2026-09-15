<?php
// test_login.php - Prueba el flujo completo de login
require_once 'externos.php';

echo "=== SIMULACION FLUJO LOGIN ===" . PHP_EOL . PHP_EOL;

// Paso 1: Frontend pide token inicial
echo "Paso 1: Emitir token inicial" . PHP_EOL;
$hash = strtotime(date('Y-m-d H:m:s')) * 27;
$token_hash = md5($hash);
$token = cifrar_texto(json_encode(['id_user' => 0, 'token_hash' => $token_hash]));
$db->insert('admin_token', ['token' => $token, 'caduca' => date('Y-m-d'), 'id_user' => 0]);
echo "Token emitido: " . substr($token, 0, 50) . "..." . PHP_EOL;

// Paso 2: Frontend cifra POST con ese token
echo PHP_EOL . "Paso 2: Cifrar POST" . PHP_EOL;
$clave_cifrada = AES_encrypt('admin123', $token);
$usuario_cifrado = AES_encrypt('admin', $token);
echo "Clave cifrada: " . substr($clave_cifrada, 0, 50) . "..." . PHP_EOL;

// Paso 3: Backend valida token
echo PHP_EOL . "Paso 3: Validar token" . PHP_EOL;
$rw = $db->select_row("SELECT * FROM admin_token WHERE token = '" . $db->escape_string($token) . "' AND estado = 1 AND caduca >= '" . date('Y-m-d') . "'");
echo "Token valido: " . ($rw ? 'SI' : 'NO') . PHP_EOL;

if ($rw) {
    $db->update('admin_token', ['estado' => 2], ['id' => $rw['id']]);
    
    // Paso 4: Descifrar POST
    echo PHP_EOL . "Paso 4: Descifrar POST" . PHP_EOL;
    $post_cifrado = ['clave' => $clave_cifrada, 'usuario' => $usuario_cifrado];
    $post_descifrado = desencriptar_post($post_cifrado, $token);
    echo "Usuario descifrado: " . ($post_descifrado['usuario'] ?? 'FALLÓ') . PHP_EOL;
    echo "Clave descifrada: " . ($post_descifrado['clave'] ?? 'FALLÓ') . PHP_EOL;
    
    // Paso 5: Buscar usuario
    echo PHP_EOL . "Paso 5: Buscar usuario" . PHP_EOL;
    $usuario = strtoupper(trim($post_descifrado['usuario']));
    $persona = $db->select_row("SELECT * FROM persona WHERE user = '" . $db->escape_string($usuario) . "'");
    echo "Persona encontrada: " . ($persona ? $persona['nombre1'] : 'NO') . PHP_EOL;
    
    if ($persona) {
        echo "Clave en BD: " . $persona['clave'] . PHP_EOL;
        echo "Hash calculado: " . clave($post_descifrado['clave']) . PHP_EOL;
        echo "Clave coincide: " . ($persona['clave'] == clave($post_descifrado['clave']) ? 'SI' : 'NO') . PHP_EOL;
    }
} else {
    echo "ERROR: Token no valido" . PHP_EOL;
}
