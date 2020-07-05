<?php
include '../helpers.php';
include 'helpers.php';

$request = json_decode(file_get_contents('php://input'), true);
$username = $request['username'];
$clientSecret = getSecret('credentials', $username);
if (!$clientSecret) {
    http_response_code(404);
    echo 'User ' . $username . ' is not registered';
    die();
}
$clientTgsSessionKey = base64_encode(openssl_random_pseudo_bytes(32));

$tgt = array(
    'username' => $username,
    'IP' => $_SERVER['REMOTE_ADDR'],
    'validTo' => date("Y-m-d", strtotime("+ 1 day")),
    'clientTgsSessionKey' => $clientTgsSessionKey
);
$tgsSecret = getSecret('credentials', 'TGS');
$data = array(
    'A' => encrypt($clientTgsSessionKey, $clientSecret),
    'B' => encrypt(json_encode($tgt), $tgsSecret)
);

$payload = json_encode($data);

header('Content-Type: application/json');
echo $payload;
