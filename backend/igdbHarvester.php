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
///
function harvestUpcomingGames($pdo, $client_id, $access_token, $user_platforms = [], $user_genres = []) {
	$current_time = time();

	$where = "first_release_date > " . $current_time . " & cover != null";
	if (!empty($user_platforms)) {
	$where .= " & platforms = (" . implode(',', $user_platforms) . ")";
	}
	if (!empty($user_genres)) {
        $where .= " & genres = (" . implode(',', $user_genres) . ")";
	}

	$query = 'fields id, name, summary, cover.image_id, rating, first_release_date, genres, platforms, external_games.url, external_games.category; where ' . $where . '; sort first_release_date asc; limit 4;';
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

	$stmt = $pdo->prepare("INSERT IGNORE INTO games (gameId, title, summary, cover_url, rating, release_date) VALUES (?, ?, ?, ?, ?, FROM_UNIXTIME(?))");
	$genremap = $pdo->prepare("INSERT IGNORE INTO game_genres (game_id, genre_id) VALUES (?, ?)");
	$platformmap = $pdo->prepare("INSERT IGNORE INTO game_platforms (game_id, platform_id) VALUES (?, ?)");
	$linkmap = $pdo->prepare("INSERT IGNORE INTO gameLinks (gameId, storeName, url) VALUES (?,?,?)");

    foreach ($games as $game) {
        $stmt->execute([
            $game['id'], $game['name'], $game['summary'] ?? "No game description available.",
            $game['cover']['image_id'] ?? null, $game['rating'] ?? null, $game['first_release_date'] ?? null
        ]);
        if (isset($game['genres']) && is_array($game['genres'])) {
            foreach ($game['genres'] as $genreId) { $genremap->execute([$game['id'], $genreId]); }
        }
        if (isset($game['platforms']) && is_array($game['platforms'])) {
            foreach ($game['platforms'] as $platformId) { $platformmap->execute([$game['id'], $platformId]); }
	}
	if (isset($game['external_games']) && is_array($game['external_games'])) {
		foreach ($game['external_games'] as $x) {
			if (!isset($x['url'])) continue;
			$url = strtolower($x['url']);
			$storeName = null;


          if (strpos($url, 'steampowered') !== false) {
            $storeName = 'Steam';
        } elseif (strpos($url, 'playstation.com') !== false) {
            $storeName = 'PlayStation Store';
        } elseif (strpos($url, 'xbox.com') !== false || strpos($url, 'microsoft.com') !== false) {
            $storeName = 'Microsoft Store';
        } elseif (strpos($url, 'epicgames.com') !== false) {
            $storeName = 'Epic Games';
        } elseif (strpos($url, 'nintendo.com') !== false) {
            $storeName = 'Nintendo eShop';
        } elseif (strpos($url, 'gog.com') !== false) {
            $storeName = 'GOG';
        } elseif (strpos($url, 'apple.com') !== false) {
            $storeName = 'Apple App Store';
        } elseif (strpos($url, 'play.google.com') !== false) {
            $storeName = 'Google Play Store';
        }elseif (strpos($url, 'nintendo') !== false) {
            $storeName = 'eShop';
        }


			if ($storeName === null) {
			continue;
			}
			$linkmap->execute([$game['id'], $storeName, $x['url']]);
		}
	}
    }
    return $games;
}
//
function harvestGameData($search_query, $pdo, $client_id, $access_token) {

	$query = 'fields id, name, summary, cover.image_id, rating, first_release_date, genres, platforms, external_games.url, external_games.category; search "' . $search_query . '"; where (rating > 60 | total_rating_count > 5) & cover != null; limit 12;';

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
	$stmt = $pdo->prepare("INSERT IGNORE INTO games (gameId, title, summary, cover_url, rating, release_date)
VALUES (?, ?, ?, ?, ?, FROM_UNIXTIME(?))
");
	$genremap = $pdo->prepare("INSERT IGNORE INTO game_genres (game_id, genre_id) VALUES (?, ?)");
	$platformmap = $pdo->prepare("INSERT IGNORE INTO game_platforms (game_id, platform_id) VALUES (?, ?)");
	$linkmap = $pdo->prepare("INSERT IGNORE INTO gameLinks (gameId, storeName, url) VALUES (?,?,?)");
	
foreach ($games as $game) {
	$stmt->execute([
	$game['id'],
	$game['name'],
        $game['summary'] ?? "No game description available.",
        $game['cover']['image_id'] ?? null,
        $game['rating'] ?? null,
        $game['first_release_date'] ?? null
        ]);
	if (isset($game['genres']) && is_array($game['genres'])) {
		foreach ($game['genres'] as $genreId) {			$genremap->execute([$game['id'], $genreId]);
		}
	}
if (isset($game['platforms']) && is_array($game['platforms'])) {
                foreach ($game['platforms'] as $platformId) {
             $platformmap->execute([$game['id'], $platformId]);
                }
}

if (isset($game['external_games']) && is_array($game['external_games'])) {
                foreach ($game['external_games'] as $x) {
                        if (!isset($x['url'])) continue;
			$url = strtolower($x['url']);
			$storeName = null;


          if (strpos($url, 'steampowered') !== false) {
            $storeName = 'Steam';
        } elseif (strpos($url, 'playstation.com') !== false) {
            $storeName = 'PlayStation Store';
        } elseif (strpos($url, 'xbox.com') !== false || strpos($url, 'microsoft.com') !== false) {
            $storeName = 'Microsoft Store';
        } elseif (strpos($url, 'epicgames.com') !== false) {
            $storeName = 'Epic Games';
        } elseif (strpos($url, 'nintendo.com') !== false) {
            $storeName = 'Nintendo eShop';
        } elseif (strpos($url, 'gog.com') !== false) {
            $storeName = 'GOG';
        } elseif (strpos($url, 'apple.com') !== false) {
            $storeName = 'Apple App Store';
        } elseif (strpos($url, 'play.google.com') !== false) {
            $storeName = 'Google Play Store';
        }elseif (strpos($url, 'nintendo') !== false) {
            $storeName = 'eShop';
        }


                        if ($storeName === null) {
                        continue;
                        }
                        $linkmap->execute([$game['id'], $storeName, $x['url']]);
                }

        }

}
return $games;
}

?>
