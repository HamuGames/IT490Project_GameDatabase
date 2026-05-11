<?php
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (isset($_POST['query']) && !empty(trim($_POST['query']))) {

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "search_games";
$request['query'] = trim($_POST['query']);

$response = $client->send_request($request);

if (isset($response['returnCode']) && $response['returnCode'] == '1') {
	$_SESSION['search_results'] = $response['data'];
	unset($_SESSION['search_error']);
}
else {
$_SESSION['search_error'] = $response['message'] ?? "Didn't find any games";
unset($_SESSION['search_results']);
}
header("Location: search_results.php");
exit();
} 
else {
	header("Location: HomePage.php");
	exit();
}

?>

