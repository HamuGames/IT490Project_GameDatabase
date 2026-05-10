#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('config.php');
require_once('igdbHarvester.php');

    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


function login($username,$password)
{
	global $db_host, $db_user, $db_pass, $db_name;

	$mydb = new mysqli($db_host, $db_user, $db_pass, $db_name);	

    if ($mydb->connect_error){
	    return array("status" => false, "message" => "Connection to Database failed"); }
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

function logout($sessionKey) {

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

function register($fName,$lName,$email,$phone,$username,$password)
{
global $db_host, $db_user, $db_pass, $db_name;

        $mydb = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($mydb->connect_error){
            return array("status" => false, "message" => "Connection to Database failed"); }

	$stmt = $mydb->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
	$stmt->bind_param("ss", $username, $email);
	$stmt->execute();
<<<<<<< HEAD
<<<<<<< HEAD
	//	$stmt->store_result();
	$stmt->bind_result($tempUser, $tempEmail);

	//if ($stmt->num_rows > 0) {
	if ($stmt->fetch()) {
=======
$stmt->bind_result($tempUser, $tempEmail);

if ($stmt->fetch()) {
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa
=======
$stmt->bind_result($tempUser, $tempEmail);

if ($stmt->fetch()) {
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
		$stmt->close();
		$mydb->close();
		if (strtolower($tempUser) === strtolower($username)) {
		return array("status" => false, "message" => "Username is already taken");
		}
		else {
		return array("status" => false, "message" => "Email already in use. Please Login");
		}
}
	$stmt->close();

	$hash = password_hash($password, PASSWORD_DEFAULT);

	$createUser = $mydb->prepare("INSERT INTO users (firstname, lastname, email, phone, username, password) VALUES (?, ?, ?, ?, ?, ?)");
	$createUser->bind_param("ssssss", $fName, $lName, $email, $phone, $username, $hash);

	if ($createUser->execute()) {
		$userid = $mydb->insert_id;
		$createUser->close();

		$sessionKey = bin2hex(random_bytes(16));
$session = $mydb->prepare("INSERT INTO sessions (userid, session_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE session_id = ?, created_at = CURRENT_TIMESTAMP");
		$session->bind_param("iss", $userid, $sessionKey, $sessionKey);

		if (!$session->execute()){
			$session->close();
			$mydb->close();
			return array("status" => false, "message" => "Session Error" . $mydb->error);
		}

		$mydb->close();
<<<<<<< HEAD
<<<<<<< HEAD
			return array("status" => true, "message" => "You have Registered successfully!");
		}
=======
		return array("status" => true, "message" => "You have Registered successfully!", "session_key" => $sessionKey);
	}
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa
=======
		return array("status" => true, "message" => "You have Registered successfully!", "session_key" => $sessionKey);
	}
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
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
<<<<<<< HEAD
<<<<<<< HEAD
	var_dump($request);
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
=======
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
  var_dump($request);
	if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
	}
	try {
  switch ($request['type'])
  {
    case "login":
      return login($request['username'],$request['password']);
    case "register":
	    $test_hash = password_hash($request['password'], PASSWORD_DEFAULT);
	    echo "Hashed: " .  $test_hash . PHP_EOL;
	    return register($request['fName'],$request['lName'],$request['email'],$request['phone'],$request['username'],$request['password']);
	    //user preferences start
	    
    case "get_preferences":
	    global $pdo;
	    $sessionKey = $request['session_key'];

	$gUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
    	$gUser->execute([$sessionKey]);
	$userR = $gUser->fetch(PDO::FETCH_ASSOC);

	if (!$userR) {
	return array("returnCode" => '0', 'message' => "Login Again");
<<<<<<< HEAD
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
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

<<<<<<< HEAD
<<<<<<< HEAD
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
=======
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
$pdo->beginTransaction();
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
    
	//2fa starts hereee

   case "sendCode":
	   $username = $request['username'];
	   $method = $request['method'];
	   global $telnyx_sender_id, $telnyx_api_key;
	   $stmt = $pdo->prepare("SELECT id, email, phone, firstname FROM users WHERE username = ?");
	   $stmt->execute([$username]);
	   $user = $stmt->fetch(PDO::FETCH_ASSOC);

	   if (!$user) {
	   	return array("status" => false, "message" => "User not found");
	   }
	   $userId = $user['id'];

	   $code = (string) random_int(100000, 999999);

	   $insert = $pdo-> prepare("
		INSERT INTO user2fa (id, code, exp) 
		VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
		ON DUPLICATE KEY UPDATE
		code = VALUES(code), exp = VALUES(exp)");

	if ($insert->execute([$userId, $code])) {
		$email = $user['email'];
		$phone = $user['phone'];
		$name = $user['firstname'];

		if ($method === 'sms') {
			$messageData = json_encode([
				'from' => $telnyx_sender_id,
				'to' => $phone,
				'text' => "Hey, $name, your login code is: " . $code . ". It will expire in 15 minutes."
			]);
			$ch = curl_init('https://api.telnyx.com/v2/messages');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   			curl_setopt($ch, CURLOPT_POST, true);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
    			curl_setopt($ch, CURLOPT_HTTPHEADER, [
        			'Authorization: Bearer ' . $telnyx_api_key,
        			'content-type: application/json',
        			'Accept: application/json'
			]);
			$apiResponse = curl_exec($ch);
			curl_close($ch);
		} else {
			$subject = "Your 2FA Login Code";
			$message = "Hey $name, your login code is: " . $code . ". It will expire in 15 minutes.";
			$headers = "From: it490.gamersdungeon@gmail.com";

			mail($email, $subject, $message, $headers);
		}
		return array("status" => true, "message" => "Code generated and sent.");
	} else {
		return array("status" => false, "message" => "Error generating code");
	}
	   break;

	  case "verifyCode":
		  $username = $request['username'];
		  $code = $request['code'];

		  $stmt = $pdo->prepare("SELECT id, email, phone, firstname FROM users WHERE username = ?");
		  $stmt->execute([$username]);
		  $user = $stmt->fetch(PDO::FETCH_ASSOC);

		  if (!$user) {
		  	return array("status" => false, "message" => "User not found");
		  }
		  $id = $user['id'];

		  $verify = $pdo->prepare("
			SELECT * FROM user2fa
			WHERE id = ? AND code = ? AND exp > NOW()
			");
		$verify->execute([$id, $code]);
		$match = $verify->fetch(PDO::FETCH_ASSOC);

		if ($match) {
			$delete = $pdo->prepare("
				DELETE FROM user2fa
				WHERE id = ?
				");
			$delete->execute([$id]);
			return array("status" => true, "message" => "Successful!");
		} else {
			return array("status" => false, "message" => "Invalid or Expired Code");
		}
		break;

	
	// 2fa ends hereee


    case "logout":
	    return logout($request['session_key']);
    case "validate_session":
	    return doValidate($request['sessionId']);
    case "search_games":
	    $searchTerm = $request['query'];
	    $stmt = $pdo->prepare("SELECT gameId as id, title as name, summary, cover_url as cover_image, rating FROM games WHERE title LIKE ?");
	    $stmt->execute(["%$searchTerm%"]);
	    $local_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
<<<<<<< HEAD
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9

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
    } else {
        return array("returnCode" => '0', 'message' => "Game not found anywhere");
    }
    case "get_game_details":
	    global $pdo;
	    $gameId = $request['game_id'];

<<<<<<< HEAD
<<<<<<< HEAD
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
=======
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9
	    $stmt = $pdo->prepare("SELECT g.*, GROUP_CONCAT(p.name SEPARATOR ', ') as platform_list FROM games g LEFT JOIN game_platforms gp ON g.gameId = gp.game_id LEFT JOIN platforms p ON gp.platform_id = p.platformId WHERE g.gameId = ? GROUP BY g.gameId");
	    $stmt->execute([$gameId]);
	    $data = $stmt->fetch(PDO::FETCH_ASSOC);
	    if ($data) {
		$link = $pdo->prepare("SELECT storeName, url FROM gameLinks WHERE gameId = ?");
		$link->execute([$gameId]);
		$data['storeLinks'] = $link->fetchAll(PDO::FETCH_ASSOC);
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
<<<<<<< HEAD
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa

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
		break;	
		case "add_review":
			global $pdo;
			$sessionKey = $request['session_key'];
			$gameId = $request['game_id'];
			$rating = $request['rating'];
			$comment = $request['comment'];

			$getUser = $pdo->prepare("SELECT g.userid FROM sessions WHERE session_id = ?");
			$getUser->execute([$sessionKey]);
			$userR = $getUser->fetch(PDO::FETCH_ASSOC);
			if (!$userR) {
				return array("returnCode" => '0', 'message' => "Login again please");
			}

			$userId = $userR['userid'];

			$check = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND game_id = ?");
			$check->execute([$userId, $gameId]);
			
			if ($check->rowCount() > 0) {
				$update = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE user_id = ? AND game_id = ?");
				if ($update->execute([$rating, $comment, $userId, $gameId])){
					return array("returnCode" => '1', 'message' => "Review updated!");
				}
			}	
			else {
				$insert = $pdo->prepare("INSERT INTO reviews (user_id, game_id, rating, comment) VALUES (?, ?, ?, ?)");
				if ($insert->execute([$userId, $gameId, $rating, $comment])) {
					return array ("returnCode" => '1', 'message' => "Review Added!");
				}
			}
			return array("returnCode" => '0', 'message' => "Error While adding review.(DB)");

		/*case "get_reviews":
			global $pdo;
			$stmt = $pdo->prepare("SELECT g.gameId, g.title, g.cover_url, l.status FROM user_library l JOIN games g ON l.game_id = g.gameId JOIN sessions s ON l.user_id = s.userid WHERE s.session_id = ? ");

			$sessionKey = $request['session_key'];
			$stmt = $pdo->prepare("SELECT comments FROM reviews l");
			$stmt->execute([$sessionKey]);
			$gameReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($userGames) {
				return array("returnCode" => '1', 'message' => "Reviews pulled! ", 'data' => $gameReviews);
			}
			else {
				return array("returnCode" => '0', 'message' => "No reviews! ");
			}*/
			//start of homepage cases
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
<<<<<<< HEAD
	return array("returnCode" => '0', 'message'=>"Server received request and processed");
=======
=======

	$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
	$getUser->execute([$sessionKey]);
	$userR = $getUser->fetch(PDO::FETCH_ASSOC);
	if (!$userR) {
	return array("returnCode" => '0', 'message' => "Login again please");
	}
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9

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
	break;
case "removeGame":
	global $pdo;
	$sessionKey = $request['session_key'];
	$gameId = $request['game_id'];

	$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
	$getUser->execute([$sessionKey]);
	$userR = $getUser->fetch(PDO::FETCH_ASSOC);
	if (!$userR) {
	return array("returnCode" => '0', 'message' => "Login again please");
	}

	$userId = $userR['userid'];

		$update = $pdo->prepare("DELETE from  user_library WHERE user_id = ? AND game_id = ?");
	if ($update->execute([ $userId, $gameId])) {
		return array("returnCode" => '1', 'message' => "Game Removed");
	}
	break;
case "get_user_library":
	global $pdo;
	$sessionKey = $request['session_key'];
	$targetUser = trim($request['target_user'] ?? '');

	$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
	$getUser->execute([$sessionKey]);
	$userR = $getUser->fetch(PDO::FETCH_ASSOC);
	if (!$userR) {
		return array("returnCode" => '0', 'message' => "Login again");
	}
	$myUserId = $userR['userid'];
	
	if ($targetUser !== '') {
	$stmt = $pdo->prepare("SELECT g.gameId, g.title, g.cover_url, l.status FROM user_library l JOIN games g ON l.game_id = g.gameId JOIN users u ON l.user_id = u.id WHERE LOWER(u.username) = LOWER(?) ");
	$stmt->execute([$targetUser]);
} else {
$stmt = $pdo->prepare("SELECT g.gameId, g.title, g.cover_url, l.status FROM user_library l JOIN games g ON l.game_id = g.gameId WHERE l.user_id = ? ");
        $stmt->execute([$myUserId]);
}

	$userGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ($userGames) {
		return array("returnCode" => '1', 'message' => "Library pulled! ", 'data' => $userGames);
	}
	else {
	return array("returnCode" => '0', 'message' => "Library is Empty! ");
	}

	//email noti here
case "add_friend":
	global $pdo;
	$sessionKey = $request['session_key'] ?? '';
	$friendUsername = trim($request['friend_username'] ?? '');

	if ($friendUsername === '') {
		return array("returnCode" => '0', 'message' => "Friend username is required");
	}

	try {
		$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
		$getUser->execute([$sessionKey]);
		$userR = $getUser->fetch(PDO::FETCH_ASSOC);
		if (!$userR) {
			return array("returnCode" => '0', 'message' => "Session expired. Please login again.");
		}
		$userId = (int)$userR['userid'];

		$friendStmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?)");
		$friendStmt->execute([$friendUsername]);
		$friendRow = $friendStmt->fetch(PDO::FETCH_ASSOC);

		if (!$friendRow) {
			return array("returnCode" => '0', 'message' => "User not found");
		}

		$friendId = (int)$friendRow['id'];
		if ($friendId === $userId) {
			return array("returnCode" => '0', 'message' => "You cannot add yourself as a friend");
		}

		$checkFriends = $pdo->prepare("SELECT id FROM user_friends WHERE user_id = ? AND friend_id = ?");
		$checkFriends->execute([$userId, $friendId]);
		if ($checkFriends->fetch()) {
			return array("returnCode" => '0', 'message' => "You are already sent a request!");
		}

		$insertFriend = $pdo->prepare("INSERT INTO user_friends (user_id, friend_id) VALUES (?, ?)");
		$insertFriend->execute([$userId, $friendId]);
//		$insertFriend->execute([$friendId, $userId]);

		$checkMut = $pdo->prepare("SELECT id FROM user_friends WHERE user_id = ? AND friend_id = ?");
		$checkMut->execute([$friendId, $userId]);
		if ($checkMut->fetch()) {
		return array("returnCode" => '1', 'message' => "You are now friends!");
		}
		return array("returnCode" => '1', 'message' => "Request Sent successfully");

	} catch (Exception $e) {
		return array("returnCode" => '0', 'message' => "Database error: " . $e->getMessage());
	}
	break;

case "get_friends_library":
	global $pdo;
	$sessionKey = $request['session_key'] ?? '';

	$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
	$getUser->execute([$sessionKey]);
	$userR = $getUser->fetch(PDO::FETCH_ASSOC);
	if (!$userR) {
		return array("returnCode" => '0', 'message' => "Session expired. Please login again.");
	}
	$userId = (int)$userR['userid'];

	$friendsStmt = $pdo->prepare("SELECT
		u.username,
		COALESCE(COUNT(DISTINCT ul.game_id), 0) AS game_count,
		COALESCE(MAX(CASE WHEN ul.status = 'playing' THEN g.title END), MAX(g.title), 'No games yet') AS favorite_game,
		GROUP_CONCAT(DISTINCT g.title ORDER BY g.title SEPARATOR '||') AS games_csv
	FROM user_friends uf1
	INNER JOIN user_friends uf2 ON uf1.user_id = uf2.friend_id AND uf1.friend_id = uf2.user_id
	JOIN users u ON u.id = uf1.friend_id
	LEFT JOIN user_library ul ON ul.user_id = u.id
	LEFT JOIN games g ON g.gameId = ul.game_id
	WHERE uf1.user_id = ?
	GROUP BY u.id, u.username
	ORDER BY u.username ASC");
	$friendsStmt->execute([$userId]);
	$rows = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);

	$data = [];
	foreach ($rows as $row) {
		$games = [];
		if (!empty($row['games_csv'])) {
			$games = array_slice(explode('||', $row['games_csv']), 0, 5);
		}

		$data[] = [
			'username' => $row['username'],
			'status' => 'Friend',
			'favorite_game' => $row['favorite_game'],
			'games' => $games,
			'count' => (int)$row['game_count']
		];
	}

	return array("returnCode" => '1', 'message' => "Friends loaded", 'data' => $data);
break;
case "search_users":
	global $pdo;
	$sessionKey = $request['session_key'] ?? '';
	$query = trim($request['query'] ?? '');

	if ($query === '') {
		return array("returnCode" => '1', 'message' => "No query", 'data' => []);
	}

	$getUser = $pdo->prepare("SELECT userid FROM sessions WHERE session_id = ?");
	$getUser->execute([$sessionKey]);
	$userR = $getUser->fetch(PDO::FETCH_ASSOC);
	if (!$userR) {
		return array("returnCode" => '0', 'message' => "Session expired. Please login again.");
	}
	$userId = (int)$userR['userid'];
	$searchStmt = $pdo->prepare("SELECT u.id, u.username
	FROM users u
	LEFT JOIN user_friends uf ON uf.user_id = ? AND uf.friend_id = u.id
	WHERE u.id != ?
	AND uf.id IS NULL
	AND LOWER(u.username) LIKE LOWER(?)
	ORDER BY u.username ASC
	LIMIT 15");
	$searchStmt->execute([$userId, $userId, "%$query%"]);
	$rows = $searchStmt->fetchAll(PDO::FETCH_ASSOC);

	return array("returnCode" => '1', 'message' => "Users found", 'data' => $rows);
break;
case "email_status":



	//email noti end	

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

//random 
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


//end cases.. 
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
<<<<<<< HEAD
>>>>>>> b109d9c7515b8dc4c7a9460ac875e33558aab8aa
=======
>>>>>>> eb838d9044a5d20129ccc220c9ee470a018050c9

}
catch (\Throwable $e) {
	$errorMsg = "BACKEND: " . $e->getMessage() . " om line " . $e->getLine();
	echo $errorMsg . PHP_EOL;
	return array("returnCode" => '0', 'message' => $errorMsg);
}}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

