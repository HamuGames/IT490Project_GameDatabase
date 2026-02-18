<?php


if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	echo json_encode($msg);
	exit(0);
}
$request = $_POST;
$response = "unsupported request type, politely BYE";
switch ($request["type"])
{
	case "login":
		$response = "login, YAY!!!!  we can do that";
	break;
}
echo json_encode($response);
exit(0);

?>
