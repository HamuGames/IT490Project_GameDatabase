<?php
// seed_db.php
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

$token = getIGDBToken($client_id, $client_secret);
if (!$token) die("Failed to get token.");

echo "Fetching upcoming games...\n";
harvestUpcomingGames($pdo, $client_id, $token);

// Force the database to learn some heavy-hitters to fuel the recommendation engine
$classics = ['Minecraft', 'God of War', 'Pokemon', 'The Last of Us', 'Grand Theft Auto V'];

foreach ($classics as $title) {
    echo "Harvesting: $title...\n";
    harvestGameData($title, $pdo, $client_id, $token);
    sleep(1); // Give the API a brief pause
}

echo "\nDatabase Seeded Successfully! You are ready for the IT490 demo.\n";
?>
