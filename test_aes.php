<?php
// test_aes.php - Verificar cifrado/descifrado AES paso a paso
require_once 'externos.php';

echo "=== PRUEBA AES DIRECTA ===" . PHP_EOL . PHP_EOL;

$password = "clave_test";
$datos = "hola mundo";

echo "Password: $password" . PHP_EOL;
echo "Datos: $datos" . PHP_EOL . PHP_EOL;

// Cifrar
$cifrado = AES_encrypt($datos, $password);
echo "Cifrado (base64): " . substr($cifrado, 0, 80) . "..." . PHP_EOL;

// Descifrado
$descifrado = AES_decrypt($cifrado, $password);
echo "Descifrado: " . ($descifrado ?: "FALLÓ") . PHP_EOL;

if ($descifrado === false) {
    echo "ERROR: openssl_decrypt devolvió false" . PHP_EOL;
} elseif ($descifrado === $datos) {
    echo "EXITO: El descifrado coincide" . PHP_EOL;
} else {
    echo "FALLO: No coincide" . PHP_EOL;
    echo "Esperado: $datos" . PHP_EOL;
    echo "Obtenido: $descifrado" . PHP_EOL;
}
