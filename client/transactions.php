<?php
include '../helpers.php';

function authenticate($username, $secret)
{
    $payload = json_encode(array('username' => $username));

    $url = 'localhost/KDC/AS.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);

    return $result;
}

function authorizeService($messageB, $serviceID, $username, $clientTgsSessionKey)
{
    $payload = json_encode(array(
        'C' => array(
            'B' => $messageB,
            'serviceID' => $serviceID
        ),
        'D' => encrypt(json_encode(array(
            'username' => $username,
            'timestamp' => date("Y-m-d")
        )), $clientTgsSessionKey)
    ));

    $url = 'localhost/KDC/TGS.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch));
    curl_close($ch);

    return $result;
}

function requestService($messageE, $timestamp, $username, $clientServerSessionKey)
{
    $payload = json_encode(array(
        'E' => $messageE,
        'G' => encrypt(json_encode(array(
            'username' => $username,
            'timestamp' => $timestamp
        )), $clientServerSessionKey)
    ));

    $url = 'localhost/server/service.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch));
    curl_close($ch);

    return $result;
}

$username = $_POST['username'];
$pwd = $_POST['psw'];

$secret = hash('sha256', $pwd . $username);

$authenticationResult = authenticate($username, $secret);
$clientTgsSessionKey = decrypt($authenticationResult['A'], $secret);

$serviceAuthorizationResult = authorizeService(
    $authenticationResult['B'],
    'DemoService',
    $username,
    $clientTgsSessionKey
);

$clientServerSessionKey = decrypt($serviceAuthorizationResult->F, $clientTgsSessionKey);
$timestamp = strtotime('now');
$requestServiceResult = requestService(
    $serviceAuthorizationResult->E,
    $timestamp,
    $username,
    $clientServerSessionKey
);

if ($timestamp == decrypt($requestServiceResult->H, $clientServerSessionKey)) {
    echo decrypt($requestServiceResult->services, $clientServerSessionKey);
    echo "<br/>Service was successfully provided";
}
