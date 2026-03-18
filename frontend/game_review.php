<?php 
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}
$rating=0;
$review="";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gameReview'])) {
	
	$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
	$request = array();
	$request['type'] = 'add_review';
	$request['session_key'] = $_SESSION['session_key'];
	$request['rating'] = $_POST['rating'];
	$request['comment'] = $_POST['comment'];
	

	$response = $client->send_request($request);
	if (isset($response['returnCode']) && $response['returnCode'] == '1') {
		$_SESSION['library_mess'] = $response['message'];
		$_SESSION['library_mess_type'] = "success";
	}
	else {
	$_SESSION['library_mess'] = $response['message'] ??"Failed to add Game";
                $_SESSION['library_mess_type'] = "danger";
	}
	header("Location: game_profile.php?id=" . $_POST['game_id']);
	exit();
}
else {
	header("Location: search_results.php");
	exit()
}
?>