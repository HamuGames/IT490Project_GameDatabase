<?php

session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECIATED & ~E_NOTICE & ~E_WARNING);

require_once('../backend/rabbitMQLib.inc');

if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY LEAVE";
	echo json_encode($msg);
	exit(0);
}

$request = $_POST;
$response = "unsupported request type, politely BYE");

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

switch ($request["type"])
{
case "login":
	$response = $client->send_request($request);
	if (!isset($response)) {
		echo json_encode(["status" => false, "message" => "RabbitMQ return nothing"]);
		exit(0);
	}

	if (is_array($response)) {
		if (isset($_response['status']) && $response ['status'] === true)
		{
			$_SESSION['logged in'] = true;
			$_SESSION['username'] = $request['username'];
			$_SESSION['session_key'] = $response['session_key'];
		}
		echo json_encode($response);
	} else {
		echo response;
	}
	exit(0);
	break;
}

echo json_encode($response);
exit(0);
?>
