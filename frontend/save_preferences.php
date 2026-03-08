<?php
session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

require_once('../backend/rabbitMQLib.inc');

if (!isset($_POST) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit(0);
}

$request = $_POST;
$request['type'] = 'save_preferences';
$request['username'] = $_SESSION['username'];
$request['session_key'] = $_SESSION['session_key'];

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

// Clear the new registration flag
unset($_SESSION['new_registration']);

echo json_encode($response);
exit(0);
?>
