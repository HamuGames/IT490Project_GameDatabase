<?php
require_once('config.php');
$log = ('emailNotifications.log');
$currentTime = time();

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getIGDBToken($client_id, $client_secret) {

        $ch = curl_init('https://id.twitch.tv/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'client_credentials'
        ]));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
}

function pendingNoti($pdo, $currentTime) {
	$sql = "SELECT 
                u.email, 
                u.username, 
                g.title, 
                ul.user_id, 
                ul.game_id 
            FROM user_library ul
            JOIN users u ON ul.user_id = u.id
            JOIN games g ON ul.game_id = g.gameId
            WHERE ul.status = 'watchlist' 
              AND ul.email = 0 
              AND g.release_date <= FROM_UNIXTIME(?)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$currentTime]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pendingNoti = pendingNoti($pdo, $currentTime);

foreach ($pendingNoti as $row) {
	$to = $row['email'];
	$subject = $row['title'] . " release notice";
	$message = "Hello, " . $row['username'] . "!\n\n This email is to let you know that your watchlisted game " . $row['title'] . " has now released! How about you go and give it a try? You may also change the status of your game to 'playing' in your library! \n\n Best, \n Gamers Dungeon";
	$headers = "From: noreply@gamersdungeon.com";

	if(mail($to, $subject, $message, $headers)) {
		$update = $pdo->prepare("UPDATE user_library SET email = 1 WHERE user_id = ? AND game_id = ?");
		$update->execute([$row['user_id'], $row['game_id']]);
		
		$logTime = date('Y-m-d H:i:s');
		$logEntry = "[$logTime] SUCCESS: Email sent to {$row['email']} for '{$row['title']}' (User ID: {$row['user_id']})\n";

    file_put_contents($log, $logEntry, FILE_APPEND);
		echo "email has been sent to " . $row['email'] . " for " . $row['title'] . "\n";
	}
	else {
	$logTime = date('Y-m-d H:i:s');
	$logEntry = "[$logTime] FAIL: Email NOT sent to {$row['email']} for '{$row['title']}' (User ID: {$row['user_id']})\n";

    	file_put_contents($log, $logEntry, FILE_APPEND);

	}
}

?>
