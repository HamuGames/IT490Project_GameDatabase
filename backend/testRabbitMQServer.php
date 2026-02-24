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
    //SESSION

    //SESSION
    $stmt = $mydb->prepare("SELECT id, password FROM users WHERE username = ?");	
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
	//$row = $result->fetch_assoc();
	//$stored_hash = $row['password'];
	$stmt->bind_result($userid, $stored_hash);
	$stmt->fetch();
	$stmt->close();

	if (password_verify($password, $stored_hash)) {

		$sessionKey = bin2hex(random_bytes(16));
		$session=$mydb->prepare(" INSERT INTO sessions (userid, session_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE session_id = ?, created_at = CURRENT_TIMESTAMP");
$session->bind_param("iss", $userid, $sessionKey, $sessionKey);
if (!$session->execute()){
return array("status" => false, "message" => "Session Error: " . $mydb->error);
}
$session->close();
$mydb->close();
	return array("status" => true, "message" => "LETS GOOOOOO", "session_key" => $sessionKey);
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

	$stmt = $mydb->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
	$stmt->bind_param("ss", $username, $email);
	$stmt->execute();
//	$stmt->store_result();
$stmt->bind_result($tempUser, $tempEmail);

//if ($stmt->num_rows > 0) {
if ($stmt->fetch()) {
		$stmt->close();
		$mydb->close();
		//return array("status" => false, "message" => "Username is taken, or email already in use");
		if (strtolower($tempUser) === strtolower($username)) {
		return array("status" => false, "message" => "Username is already taken");
		}
		else {
		return array("status" => false, "message" => "Email already in use. Please Login");
		}
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

