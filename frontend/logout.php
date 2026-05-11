<?php
session_start();
require_once('../backend/rabbitMQLib.inc');

if (isset($_SESSION['session_key'])) {
	$client = new rabbitMQClient("../backend/testRabbitMQ.ini","testServer");

	$request = array();
	$request['type'] = "logout";
	$request['session_key'] = $_SESSION['session_key'];

	$client->send_request($request);
}
session_unset();
session_destroy();

header("Location: index.php");
exit(0);
?>
