<?php
session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
//error_reporting(E_ALL);

require_once('../backend/rabbitMQLib.inc');

if (!isset($_POST))
{
        $msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
        echo json_encode($msg);
        exit(0);
}
$request = $_POST;
$response = "unsupported request type, politely BYE";

$client = new rabbitMQClient("../backend/testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = $_POST['type'];
$request['username'] = $_POST['username'];

if ($_POST['type'] === "sendCode") {
	$request['method'] = $_POST['Method'];
}
if ($_POST['type'] === "verifyCode") {
	$request['code'] = $_POST['Code'};
}
$response = $client->send_request($request);

if ($_POST['type'] === "verifyCode" && isset($response['status']) && $response['status'] === true {
	$_SESSION['logged_in'] = true;
	unset($_SESSION['authentication']);
}

echo json_encode($response);
exit(0);
?>






