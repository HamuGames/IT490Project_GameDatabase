<?php
// Created SMS API Integration for SMS Notification Deliverable
require_once('config.php');
$log = ('smsNotifications.log');
$currentTime = time();

$pdo = new PDO("mysql:host=$db_host;dbname = $db_name;charset=utf8mb4", $dbuser, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function sendSMS($phone_number, $message, $telnyx_api_key) {

    $ch = curl_init('https://api.telynx.com/v2/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $telnyx_api_key,
        'content-type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['data']['id'] ?? null;
}
function pendingNoti($pdo, $currentTime) {
    $sql = "SELECT
            u.phone,
            u.username,
            g.title,
            ul.user_id,
            ul.game_id
        FROM user_library ul
        JOIN users u ON ul.user_id = u.id
        JOIN games g ON ul.game_id = g.gameId
        WHERE ul.ststus = 'watchlist'
            AND ul.sms = 0
            AND g.release_date <= FROM_UNIXTIME(?)";
    $stmt = $pdo ->prepare($sql);
    $stmt ->execute([$currentTime]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);


}

$pendingNoti = pendingNoti($pdo, $currentTime);

foreach ($pendingNoti as $row) {
    $phone = $row['phone'];
    $message = "Hello, " . $row['username'] . "!\n\nYour WatchListed Game " . $row['title'] . "has now released! How about you go and give it a try? You may also change the status of your game to 'playing' in your library! \n\n Best, \n Gamers Dungeon";
    if(sendSMS($phone, $message, $telnyx_api_key)) {
        $update = $pdo->prepare("UPDATE user_library SET sms = 1 Where user_id = ? AND game_id = ?");
        $update->execute([$row['user_id'], $row['game_id']]);

        $logTime = date('Y-m-d H:i:s');
        $logEntry = "[$logTime] SUCCESS: SMS SENT TO {$row['phone']} for '{$row['title']}' (User ID: {$row['user_id']})\n";
        file_put_contents($log, $logEntry, FILE_APPEND);
        echo "SMS has been sent to " . $row['phone'] . "for" . $row['title'] . "\n";
    }

    else {
        $logTime = date('Y-m-d H:i:s');
        $logEntry = "[$logTime] FAIL: SMS NOT sent to {$row['title']} (User ID: {$row['user_id']})\n";
        file_put_contents($log, $logEntry, FILE_APPEND);
    }
}

?>
