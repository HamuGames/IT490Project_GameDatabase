#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('config.php');
require_once('igdbHarvester.php');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}

function doLogin($username,$password)
{
	global $db_host, $db_user, $db_pass, $db_name;

	$mydb = new mysqli($db_host, $db_user, $db_pass, $db_name);	

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

function doLogout($sessionKey) {

	global $db_host, $db_user, $db_pass, $db_name;

        $mydb = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mydb->connect_error){
            return array("status" => false, "message" => "Connection to Database failed"); }

	$stmt = $mydb->prepare("DELETE FROM sessions WHERE session_id = ?");

	if (!$stmt) {
	return array("status" => false, "message" => "ERROR: " . $mydb->error);
	}
	$stmt->bind_param("s", $sessionKey);
	$stmt->execute();
	$stmt->close();
	$mydb->close();
	return array("status" => true, "message" => "Session terminated in Database");
}

function doRegister($fName,$lName,$email,$username,$password)
{
global $db_host, $db_user, $db_pass, $db_name;

        $mydb = new mysqli($db_host, $db_user, $db_pass, $db_name);

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
	global $pdo, $client_id, $client_secret;
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
    case "logout":
	    return doLogout($request['session_key']);
    case "validate_session":
	    return doValidate($request['sessionId']);
    case "search_games":
	    $searchTerm = $request['query'];
	    $stmt = $pdo->prepare("SELECT gameId as id, title as name, summary, cover_url as cover_image, rating FROM games WHERE title LIKE ?");
	    $stmt->execute(["%$searchTerm%"]);
	    $local_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	    if (!empty($local_results)) {
	    return array("returnCode" => '1', 'message' => "Loaded from local DB", 'data' => $local_results);
	    }

	    $token = getIGDBToken($client_id, $client_secret);
if (!$token) {
        return array("returnCode" => '0', 'message' => "API Auth Failed");
    }

    $api_results = harvestGameData($searchTerm, $pdo, $client_id, $token);

    // 4. Return the freshly harvested data
    if (!empty($api_results)) {
        return array("returnCode" => '1', 'message' => "Harvested from IGDB", 'data' => $api_results);
    } else {
        return array("returnCode" => '0', 'message' => "Game not found anywhere");
    }
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

