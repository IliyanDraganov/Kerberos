<?php
include '../helpers.php';

$request = json_decode(file_get_contents('php://input'), true);

$secret = 'ServiceSecretKey';
$clientServerTicket = json_decode(decrypt($request['E'], $secret));
$clientServerSessionKey = $clientServerTicket->clientServerSessionKey;

$authenticator = json_decode(decrypt($request['G'], $clientServerSessionKey));

if ($authenticator->username === $clientServerTicket->username) {
    $data = array(
        'H' => encrypt($authenticator->timestamp, $clientServerSessionKey),
        'services' => encrypt(json_encode(array(
            '29.06.20 : -200$', '11.06.20 : +900$', '18.06.20 : -300$'
        )), $clientServerSessionKey)
    );

    $payload = json_encode($data);

    header('Content-Type: application/json');
    echo $payload;
} else {
    http_response_code(403);
    echo 'Access denied!';
    die();
}
