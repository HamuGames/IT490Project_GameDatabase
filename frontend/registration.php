<?php
session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

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

switch ($request["type"])
{
	case "register":
		$response = $client->send_request($request);
		if (isset($response['status']) && $response['status'] === true){
		$_SESSION['logged_in'] = true;
            $_SESSION['new_registration'] = true; 
		$_SESSION['username'] = $request['username'];
		if (isset($response['session_key'])) {
                $_SESSION['session_key'] = $response['session_key'];
		}
		$response['redirect'] = "onboarding.php";
		}
		break;
}
echo json_encode($response);
exit(0);

?>

