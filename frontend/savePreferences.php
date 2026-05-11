<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

    $request = array();
    $request['type'] = "save_preferences";
    $request['session_key'] = $_SESSION['session_key'];

    $request['platforms'] = $_POST['platforms'] ?? [];
    $request['genres'] = $_POST['genres'] ?? [];

    $response = $client->send_request($request);

    if (isset($response['returnCode']) && $response['returnCode'] == '1') {
        $_SESSION['pref_mess'] = "Preferences saved!";
        $_SESSION['pref_mess_type'] = "success";
	if (isset($_SESSION['new_registration']) && $_SESSION['new_registration'] === true) {
		unset($_SESSION['new_registration']); 
		header("Location: HomePage.php");
		exit();
	}
    
    } else {
        $_SESSION['pref_mess'] = $response['message'] ?? "Failed to save preferences.";
        $_SESSION['pref_mess_type'] = "danger";
    }

header("Location: preferences.php");
    exit();
}
else {
	header("Location: preferences.php");
	exit();
}
?>

