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
	$request['session_key'] = $_SESSION['session_key'];
	$request['gameRating'] = $_SESSION['gameRating'];

	$response = $client->send_request($request);
}

?>