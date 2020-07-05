<?php
include '../helpers.php';
include 'helpers.php';

$request = json_decode(file_get_contents('php://input'), true);

$tgsSecret = getSecret('credentials', 'TGS');
$ticketGrantingTicket = json_decode(decrypt($request['C']['B'], $tgsSecret));
$clientTgsSessionKey = $ticketGrantingTicket->clientTgsSessionKey;

$messageD = json_decode(decrypt($request['D'], $clientTgsSessionKey));

if ($messageD->username === $ticketGrantingTicket->username) {
    $clientServerSessionKey = base64_encode(openssl_random_pseudo_bytes(32));

    $serviceSecret = getSecret('services', $request['C']['serviceID']);

    $clientServerTicket = array(
        'username' => $ticketGrantingTicket->username,
        'IP' => $ticketGrantingTicket->IP,
        'validTo' => date("Y-m-d", strtotime("+ 1 day")),
        'clientServerSessionKey' => $clientServerSessionKey
    );

    $data = array(
        'E' => encrypt(json_encode($clientServerTicket), $serviceSecret),
        'F' => encrypt($clientServerSessionKey, $clientTgsSessionKey)
    );

    $payload = json_encode($data);

    header('Content-Type: application/json');
    echo $payload;
} else {
    http_response_code(403);
    echo 'Access denied!';
    die();
}
