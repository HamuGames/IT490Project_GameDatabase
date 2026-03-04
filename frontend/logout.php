<?php
session_start();
require_once('../backend/rabbitMQLib.inc');

if (isset($_SESSION['session_key'])) {
	try {
		$client = new rabbitMQClient("../backend/testRabbitMQ.ini","testServer");
		$request = array();
		$request['type'] = "logout";
		$request['session_key'] = $_SESSION['session_key'];
		$client->send_request($request);
	} catch (Exception $e) {
		error_log("Logout Failed: " . $e->getMessage());
	}
}
$_SESSION = array();
session_unset();
session_destroy();

if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"],$params["domain"],
		$params["secure"], $params["httponly"]
	);
}

header("Location: register.php");
exit(0);
?>
