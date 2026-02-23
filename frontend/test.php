<?php
// A pure test to see if the Backend answers
require_once('../backend/rabbitMQLib.inc'); // Adjust this path if needed!

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "login";
$request['username'] = "test"; // Using the username from your screenshot
$request['password'] = "test";

echo "Sending to RabbitMQ...\n";
$response = $client->send_request($request);

echo "Backend Responded:\n";
var_dump($response);
?>
