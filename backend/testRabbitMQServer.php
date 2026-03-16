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
		return array("status" => false, "message" => "Connection to Database failed"); 
	}
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
	    //user preferences start    
		case "get_preferences":
			global $pdo;
			$sessionKey = $request['session_key'];

			$gUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
			$gUser->execute([$sessionKey]);
			$userR = $gUser->fetch(PDO::FETCH_ASSOC);

			if (!$userR) {
				return array("returnCode" => '0', 'message' => "Login Again");
			}
			$userId = $userR['userid'];
			$evryPlatform = $pdo->query("SELECT platformId as id, name FROM platforms ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
			$evryGenre = $pdo->query("SELECT genreId as id, name FROM genres ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

			$usrPlatsSt = $pdo->prepare("SELECT platform_id FROM user_platforms WHERE user_id = ?");		
			$usrPlatsSt->execute([$userId]);
			$usrPlats = $usrPlatsSt->fetchALL(PDO::FETCH_COLUMN);

			$usrGensSt = $pdo->prepare("SELECT genre_id FROM user_genres WHERE user_id = ?");
			$usrGensSt->execute([$userId]);
			$usrGens = $usrGensSt->fetchALL(PDO::FETCH_COLUMN);

			$data = [

				'all_platforms' => $evryPlatform,
				'all_genres' => $evryGenre,
				'user_platforms' => $usrPlats,
				'user_genres' => $usrGens

			];
			return array("returnCode" => '1', 'message' => "Preferences Loaded", 'data' => $data);	    
		case "save_preferences":
			global $pdo;
			$sessionKey = $request['session_key'];
			$platforms = $request['platforms'] ?? [];
			$genres = $request['genres'] ?? [];

			$gUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
			$gUser->execute([$sessionKey]);
			$userR = $gUser->fetch(PDO::FETCH_ASSOC);
			if (!$userR) return array("returnCode" => '0', "message" => "Session expired");
			$userId = $userR['userid'];

			$pdo->beginTransaction();
			try {
				//first clear all preferences
				$pdo->prepare("DELETE FROM user_platforms WHERE user_id = ?")->execute([$userId]);
				$pdo->prepare("DELETE FROM user_genres WHERE user_id = ?")->execute([$userId]);
				// ins. user plats.
				$platStmt = $pdo->prepare("INSERT INTO user_platforms (user_id, platform_id) VALUES (?, ?)");
				foreach ($platforms as $pId) {
					$platStmt->execute([$userId, $pId]);
				}
			// ins. user genres
					$genStmt = $pdo->prepare("INSERT INTO user_genres (user_id, genre_id) VALUES (?, ?)");
				foreach ($genres as $gId) {
					$genStmt->execute([$userId, $gId]);
				}
				$pdo->commit();
				return array("returnCode" => '1', "message" => "Preferences saved.");
			} catch (Exception $e) {
				$pdo->rollBack();
				return array("returnCode" => '0', "message" => "Database error: " . $e->getMessage());
			}

//user preferences end 
    
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
		
			if (!empty($api_results)) {
				$format = [];
				foreach ($api_results as $r) {
					$format[] = [
						'id' => $r['id'],
						'name' => $r['name'],
						'summary' => $r['summary'] ?? "",
						'cover_image' => $r['cover']['image_id'] ?? null,
						'rating' => $r['rating'] ??null
					];
				}
			return array("returnCode" => '1', 'message' => "Harvested from IGDB", 'data' => $format);
			} 
			else {
				return array("returnCode" => '0', 'message' => "Game not found anywhere");
			}
		case "get_game_details":
			global $pdo;
			$gameId = $request['game_id'];

			$stmt = $pdo->prepare("SELECT g.*, GROUP_CONCAT(p.name SEPARATOR ', ') as platform_list FROM games g LEFT JOIN game_platforms gp ON g.gameId = gp.game_id LEFT JOIN platforms p ON gp.platform_id = p.platformId WHERE g.gameId = ? GROUP BY g.gameId");
			$stmt->execute([$gameId]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($data) {
				return array("returnCode" => '1', 'message' => "Data harvesting successful", 'data' => $data);
			}
			else {
				return array("returnCode" => '0', 'message' => "Game not Found");
			}
		case "addToLibrary":
			global $pdo;
			$sessionKey = $request['session_key'];
			$gameId = $request['game_id'];
			$status = $request['status'];

			$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
			$getUser->execute([$sessionKey]);
			$userR = $getUser->fetch(PDO::FETCH_ASSOC);
			if (!$userR) {
				return array("returnCode" => '0', 'message' => "Login again please");
			}

			$userId = $userR['userid'];

			$check = $pdo->prepare("SELECT id FROM user_library WHERE user_id = ? AND game_id = ?");
			$check->execute([$userId, $gameId]);

			if ($check->rowCount() > 0) {
				$update = $pdo->prepare("UPDATE user_library SET status = ? WHERE user_id = ? AND game_id = ?");
				if ($update->execute([$status, $userId, $gameId])) {
					return array("returnCode" => '1', 'message' => "Library Updated!");
				}
			}
			else {
				$insert = $pdo->prepare("INSERT INTO user_library (user_id, game_id, status) VALUES (?, ?, ?)");
				if ($insert->execute([$userId, $gameId, $status])) {
					return array ("returnCode" => '1', 'message' => "Added to your library!");
				}
			}
			return array("returnCode" => '0', 'message' => "Error While updating library.(DB)");

		case "get_user_library":
			global $pdo;
			$sessionKey = $request['session_key'];

			$stmt = $pdo->prepare("SELECT g.gameId, g.title, g.cover_url, l.status FROM user_library l JOIN games g ON l.game_id = g.gameId JOIN sessions s ON l.user_id = s.userid WHERE s.session_id = ? ");
			$stmt->execute([$sessionKey]);
			$userGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($userGames) {
				return array("returnCode" => '1', 'message' => "Library pulled! ", 'data' => $userGames);
			}
			else {
				return array("returnCode" => '0', 'message' => "Library is Empty! ");
			}
			//start of homepage cases

		case "homepage_data":
			global $pdo;
			$sessionKey = $request['session_key'];
			$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
				$getUser->execute([$sessionKey]);
				$userR = $getUser->fetch(PDO::FETCH_ASSOC);
				if (!$userR) {
				return array("returnCode" => '0', 'message' => "Login again please");
				}
				$userId = $userR['userid'];
			//random games not in usr lib.
				$stRecs = $pdo->prepare("
			SELECT DISTINCT g.* FROM games g
			JOIN game_platforms gp ON g.gameId = gp.game_id
			JOIN user_platforms up ON gp.platform_id = up.platform_id
			JOIN game_genres gg ON g.gameId = gg.game_id
			JOIN user_genres ug ON gg.genre_id = ug.genre_id
			WHERE up.user_id = ? AND ug.user_id = ?
			AND g.gameId NOT IN (SELECT game_id FROM user_library WHERE user_id = ?)
			ORDER BY RAND() LIMIT 4 ");
				$stRecs->execute([$userId, $userId, $userId]);
			$recs = $stRecs->fetchAll(PDO::FETCH_ASSOC);
			//upcoming games
			$platStmt = $pdo->prepare("SELECT platform_id FROM user_platforms WHERE user_id = ?");
			$platStmt->execute([$userId]);
			$uPlats = $platStmt->fetchAll(PDO::FETCH_COLUMN);

			$genStmt = $pdo->prepare("SELECT genre_id FROM user_genres WHERE user_id = ?");
			$genStmt->execute([$userId]);
			$uGens = $genStmt->fetchAll(PDO::FETCH_COLUMN);

		case "review_data":
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
			
			if (!empty($api_results)) {
				$format = [];
				foreach ($api_results as $r) {
					$format[] = [
						'id' => $r['id'],
						'name' => $r['name'],
						'summary' => $r['summary'] ?? "",
						'cover_image' => $r['cover']['image_id'] ?? null,
						'rating' => $r['rating'] ??null
					];
				}
				return array("returnCode" => '1', 'message' => "Harvested from IGDB", 'data' => $format);
			}
			else {
				return array("returnCode" => '0', 'message' => "Game not found anywhere");
			}
			global $client_id, $client_secret;
			$token = getIGDBToken($client_id, $client_secret);

			$upcoming = harvestUpcomingGames($pdo, $client_id, $token, $uPlats, $uGens);
			$upc = [];
			foreach ($upcoming as $uc) {
				$upc[] = [
					'gameId' => $uc['id'],
					'title' => $uc['name'],
					'cover_url' => $uc['cover']['image_id'] ?? null
				];
			}

			//random games based on game from user library
			$stLibGame = $pdo->prepare("SELECT g.* FROM user_library ul
				JOIN games g ON ul.game_id = g.gameId
				WHERE ul.user_id = ? ORDER BY RAND() LIMIT 1");
			$stLibGame->execute([$userId]);
			$libGame = $stLibGame->fetch(PDO::FETCH_ASSOC);

			$related = [];
			if ($libGame) {
				$stRel = $pdo->prepare("
					SELECT DISTINCT g.* FROM games g
					JOIN game_genres gg ON g.gameId = gg.game_id
					WHERE gg.genre_id IN (SELECT genre_id FROM game_genres WHERE game_id = ?)
					AND g.gameId != ?
					LIMIT 4 ");

				$stRel->execute([$libGame['gameId'], $libGame['gameId']]);
				$related = $stRel->fetchAll(PDO::FETCH_ASSOC);
			}
			return array("returnCode" => '1', "data" => [
					"recommendations" => $recs,
					"upcoming" => $upc,
					"lib_game" => $libGame,
					"related" => $related
				]
			);
		//end of homepage cases
		//end of switch	
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed");

}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

