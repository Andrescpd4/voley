<?php
// test_login_completo.php - Simular exactamente lo que hace el navegador
require_once 'externos.php';

echo "=== SIMULACION COMPLETA DEL FLUJO LOGIN ===" . PHP_EOL . PHP_EOL;

// Paso 1: Frontend pide token inicial
echo "Paso 1: Emitir token inicial" . PHP_EOL;
$hash = strtotime(date('Y-m-d H:m:s')) * 27;
$token_hash = md5($hash);
$token = cifrar_texto(json_encode(['id_user' => 0, 'token_hash' => $token_hash]));
$db->insert('admin_token', ['token' => $token, 'caduca' => date('Y-m-d'), 'id_user' => 0]);
echo "Token emitido: " . substr($token, 0, 50) . "..." . PHP_EOL;

// Paso 2: Frontend cifra POST (CryptoJS cifra nombre del campo Y valor)
echo PHP_EOL . "Paso 2: Cifrar POST (como CryptoJS)" . PHP_EOL;
$clave_cifrada = AES_encrypt('admin123', $token);
$usuario_cifrado = AES_encrypt('admin', $token);
// También cifrar los nombres de campos (como hace encriptar_form)
$campo_usuario_cifrado = AES_encrypt('usuario', $token);
$campo_clave_cifrado = AES_encrypt('clave', $token);
echo "Campo 'usuario' cifrado: " . substr($campo_usuario_cifrado, 0, 50) . "..." . PHP_EOL;
echo "Campo 'clave' cifrado: " . substr($campo_clave_cifrado, 0, 50) . "..." . PHP_EOL;

// Paso 3: Backend valida token
echo PHP_EOL . "Paso 3: Validar token" . PHP_EOL;
$rw = $db->select_row("SELECT * FROM admin_token WHERE token = '" . $db->escape_string($token) . "' AND estado = 1 AND caduca >= '" . date('Y-m-d') . "'");
echo "Token valido: " . ($rw ? 'SI' : 'NO') . PHP_EOL;

if ($rw) {
    $db->update('admin_token', ['estado' => 2], ['id' => $rw['id']]);
    
    // Paso 4: Descifrar POST (como hace desencriptar_post)
    echo PHP_EOL . "Paso 4: Descifrar POST" . PHP_EOL;
    $post_cifrado = [
        $campo_usuario_cifrado => $usuario_cifrado,
        $campo_clave_cifrado => $clave_cifrada
    ];
    $post_descifrado = desencriptar_post($post_cifrado, $token);
    echo "POST descifrado:" . PHP_EOL;
    foreach ($post_descifrado as $k => $v) {
        echo "  '$k' => '$v'" . PHP_EOL;
    }
    
    // Paso 5: Buscar usuario
    echo PHP_EOL . "Paso 5: Buscar usuario" . PHP_EOL;
    $usuario = strtoupper(trim($post_descifrado['usuario'] ?? ''));
    echo "Usuario (strtoupper): $usuario" . PHP_EOL;
    $persona = $db->select_row("SELECT * FROM persona WHERE user = '" . $db->escape_string($usuario) . "'");
    echo "Persona encontrada: " . ($persona ? $persona['nombre1'] : 'NO') . PHP_EOL;
    
    if ($persona) {
        echo "Clave coincide: " . ($persona['clave'] == clave($post_descifrado['clave']) ? 'SI' : 'NO') . PHP_EOL;
    }
}
