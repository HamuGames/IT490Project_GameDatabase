#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    $mydb = new mysqli("127.0.0.1", "Hamu", "11301250", "IT490DB");

    if ($mydb->connect_error){
	    return array("status" => false, "message" => "Connection to Database failed"); }

    $stmt = $mydb->prepare("SELECT password FROM users WHERE username = ?");	
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$stored_hash = $row['password'];
	if (password_verify($password, $stored_hash)) {
	return array("status" => true, "message" => "LETS GOOOOOO");
	}
	else{
	return array("status" => false, "message" => "Wrong Password");
	}}
	else{
	return array("status" => false, "message" => "No User found");
	}
	$stmt->close();
	$mydb->close();
}

function doRegister($fName,$lName,$email,$username,$password)
{
	$mydb = new mysqli("127.0.0.1", "Hamu", "11301250", "IT490DB");

    if ($mydb->connect_error){
            return array("status" => false, "message" => "Connection to Database failed"); }

	$stmt = $mydb->prepare("SELECT username FROM users WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		$stmt->close();
		$mydb->close();
		return array("status" => false, "message" => "Username is taken");
	}
	$stmt->close();

	$hash = password_hash($password, PASSWORD_DEFAULT);

	$createUser = $mydb->prepare("INSERT INTO users (firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?)");
	$createUser->bind_param("sssss", $fName, $lName, $email, $username, $hash);

	if ($createUser->execute()) {
		$createUser->close();
		$mydb->close();
		return array("status" => true, "message" => "You have Registered successfully!");
	}
	else {
		$createUser->close();
		$mydb->close();
		return array("status" => false, "message" => "Failed to Register");
	}
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "register":
	    $test_hash = password_hash($request['password'], PASSWORD_DEFAULT);
	    echo "Hashed: " .  $test_hash . PHP_EOL;
	    return doRegister($request['fName'],$request['lName'],$request['email'],$request['username'],$request['password']);
    case "validate_session":
	    return doValidate($request['sessionId']); 
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

