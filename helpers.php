<?php
function encrypt($message, $key)
{
    $method = 'aes-256-ctr';
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encryptedMessage = openssl_encrypt($message, $method, $key, 0, $iv);
    return base64_encode($iv . $encryptedMessage);
}

function decrypt($encryptedMessage, $key)
{
    $encryptedMessage = base64_decode($encryptedMessage);
    $method = 'aes-256-ctr';
    $ivLength = openssl_cipher_iv_length($method);
    $iv = mb_substr($encryptedMessage, 0, $ivLength, '8bit');
    $ciphertext = mb_substr($encryptedMessage, $ivLength, null, '8bit');

    $message = openssl_decrypt(
        $ciphertext,
        $method,
        $key,
        0,
        $iv
    );

    return $message;
}
