<?php

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

function harvestGameData($search_query, $pdo, $client_id, $access_token) {

	$query = 'fields id, name, summary, cover.image_id, rating, first_release_date; search "' . $search_query . '"; where cover != null; limit 5;';

	$ch = curl_init('https://api.igdb.com/v4/games');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Client-ID: ' . $client_id,
        'Authorization: Bearer ' . $access_token,
        'Accept: application/json'
	]);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

	$games_json = curl_exec($ch);
	curl_close($ch);
	$games = json_decode($games_json, true);

	if (empty($games)) return [];

	$stmt = $pdo->prepare("
INSERT IGNORE INTO games (gameId, title, summary, cover_url, rating, release_date)
VALUES (?, ?, ?, ?, ?, FROM_UNIXTIME(?))
");

foreach ($games as $game) {
	$stmt->execute([
	$game['id'],
	$game['name'],
        $game['summary'] ?? "No game description available.",
        $game['cover']['image_id'] ?? null,
        $game['rating'] ?? null,
        $game['first_release_date'] ?? null
        ]);
}
return $games;
}

?>
