<?php
// test_cryptojs.php - Simular exactamente lo que hace CryptoJS.AES.encrypt()
require_once 'externos.php';

echo "=== SIMULACION EXACTA DE CryptoJS.AES.encrypt() ===" . PHP_EOL . PHP_EOL;

$password = "clave_test";
$plaintext = "hola mundo";

echo "Password: $password" . PHP_EOL;
echo "Plaintext: $plaintext" . PHP_EOL . PHP_EOL;

// CryptoJS.AES.encrypt(plaintext, password) hace:
// 1. Generar salt aleatorio de 8 bytes
$salt = openssl_random_pseudo_bytes(8);
echo "Salt (hex): " . bin2hex($salt) . PHP_EOL;

// 2. Derivar key (32 bytes) e IV (16 bytes) usando EVP_BytesToKey con MD5, 1 iteración
//    EVP_BytesToKey: D_i = MD5(D_{i-1} + password + salt), D_0 = ""
$data00 = $password . $salt;
$D1 = md5($data00, true);  // 16 bytes
$D2 = md5($D1 . $data00, true);  // 16 bytes
$D3 = md5($D2 . $data00, true);  // 16 bytes

$derived = $D1 . $D2 . $D3;  // 48 bytes
$key = substr($derived, 0, 32);
$iv = substr($derived, 32, 16);

echo "Key (hex): " . bin2hex($key) . PHP_EOL;
echo "IV (hex): " . bin2hex($iv) . PHP_EOL . PHP_EOL;

// 3. Cifrar con AES-256-CBC
$ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
echo "Ciphertext (base64): " . base64_encode($ciphertext) . PHP_EOL;

// 4. Formato final: "Salted__" + salt + ciphertext (base64)
$final = base64_encode('Salted__' . $salt . $ciphertext);
echo "Formato CryptoJS (base64): " . substr($final, 0, 80) . "..." . PHP_EOL . PHP_EOL;

// 5. Ahora probar descifrar con mi función
echo "=== DESCIFRADO CON AES_decrypt() ===" . PHP_EOL;
$descifrado = AES_decrypt($final, $password);
echo "Descifrado: " . ($descifrado ?: "FALLÓ") . PHP_EOL;

if ($descifrado === $plaintext) {
    echo "EXITO" . PHP_EOL;
} else {
    echo "FALLO" . PHP_EOL;
}
