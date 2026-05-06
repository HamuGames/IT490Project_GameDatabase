<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: index.php");
        exit(0);
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
	$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
	$request = array();
	$request['type'] = "removeGame";
	$request['session_key'] = $_SESSION['session_key'];
	$request['game_id'] = $_GET['id'];

	$response = $client->send_request($request);

	//here is just a message to let user know if game was added to their lib. 

	if (isset($response['returnCode']) && $response['returnCode'] == '1') {
		$_SESSION['library_mess'] = $response['message'];
		$_SESSION['library_mess_type'] = "success";
	}
	else {
	$_SESSION['library_mess'] = $response['message'] ??"Failed to remove Game";
                $_SESSION['library_mess_type'] = "danger";
	}
	header("Location: myLibrary.php");
	exit();
}
else {
	header("Location: myLibrary.php");
	exit();
}
?>
