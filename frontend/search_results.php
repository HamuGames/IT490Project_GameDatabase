<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

$query = $_REQUEST['search_query'] ?? "";
$games = [];
$error = "";

if (!empty($query)) {
	$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
    $request = [
        'type' => "search_games",
        'query' => $query
    ];

    $response = $client->send_request($request);

    if (isset($response['returnCode']) && $response['returnCode'] == '1') {
        $games = $response['data'];
    } else {
        $error = $response['message'] ?? "No games found for '" . htmlspecialchars($query) . "'";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Search Results</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include('navBar.php'); ?>
<div class="row g-4">
	<?php if (!empty($games)) : ?>
<?php foreach ($games as $game): ?>
<div class="col-6 col-md-4 col-lg-3">
<div class="card h-100 shadow-sm border-0">
<?php
$cover_url = $game['cover_image']
	? "https://images.igdb.com/igdb/image/upload/t_cover_big/{$game['cover_image']}.jpg"
:  "https://via.placeholder.com/264x352?text=No+Cover";
?>
	<img src="<?php echo $cover_url; ?>" class="card-img-top" alt="Cover" style="height: 300px; object-fit: cover;">
<div class="card-body d-flex flex-column text-center">
<h6 class="card-title text-truncate fw-bold"><?php echo htmlspecialchars($game['name']); ?></h6>
<a href="game_profile.php?id=<?php echo $game['id']; ?>" class="btn btn-outline-primary mt-auto btn-sm">View Profile</a>
</div>
</div>
</div>
<?php endforeach; ?>
<?php elseif ($error): ?>
	<div class="col-12"><div class="alert alert-warning"><?php echo $error; ?> </div></div>
<?php endif; ?>
</div>
</div>
</body>
</html>
