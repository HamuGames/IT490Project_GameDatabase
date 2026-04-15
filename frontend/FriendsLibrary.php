<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	header("Location: index.php");
	exit(0);
}

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
$request = [
	'type' => "homepage_data",
	'session_key' => $SESSION['session_key']
	];
$response = $clinet->send_request($request);

$recs = $response['data']['recommendations'] ?? [];
$upc = $response['data']['upcoming'] ?? [];
$libGame = $response['data']['lib_game'] ?? null;
$related = $response['data']['related'] ?? [];

?>
<!DOCTYPE html>

<html>
<head>
<title> Home - GAMERS DUNGEON </title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/main.css">
<style>
.game-card img { height: 300px; object-fit: cover; transition: transform 0.3s; }
.game-card:hover { transform: translateY(-5px); transition: 0.3s; }
</style>
</head>

<body class=bg-light">

<?php include('navBar.php'); ?>

<div class="container mt-4">

	<div class='d-flex justify-content-between align-items-center mb-4">
		<h1 class="display-5 fw-bold text-dark mb-0">
	  		 Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
		<h1>

		<a href="friendsLibrary.php" class="btn btn-success btn-lg">
			Friends Library
		</a>
	</div>
<section>
<form>
	<input type="text" name=searchcategory" placeholder="Search Games..">
	<button> Search</button>
</form>
</section>

<section class="mb-5">
	<h3 class="text-primary border-bottom pb-2">For Your:</h3>
<div class="row mt-3">
	<?php if (empty($recs)): ?>
<div class="col-12"><p class="text-muted"> Based on your preferences</p></div>
	<?php else: ?>
	<?php for each ($recs as $g): ?>
<div class="col-6 col-md-3 mb-4">
<div class="card h-100 shadow-sm game-card border-0">
	<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $g['cover_url']; ?>.jpg" class="card-img-top rounded-top">
<div class="card-body text-center">
	<h6 class="card-title text-truncate fw-bold"><?php echo htmlspecialchars($g['title']); ?></h6>
	<a href="game_profile.php?id=<?php echo $g['gameID']; ?>" class="btn btn-sm btn-primary w-100">View Game</a>
</div>
</div>
</div>
	<?php endforeach; ?>
	<?php endif; ?>
</div>
</section>

<?php if ($libGame): ?>
<section class="mb-5">
	<h3 class="text-success border-bottom pb-2">
		Because you liked: <?php echo htmlspecialchars($libGame['title']); ?>
	</h3>
<div class="row mt-3">
	<?php if (empty($related)): ?>
<div class="col-12"><p class="text-muted">No related games found yet.</p></div>
	<?php else: ?>
	<?php foreach ($related as $g): ?>
<div class="col-6 col-md-3 mb-4">
<div class="card h-100 shadows-sm game-card border-0">
	<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $g['cover_url']; ?>.jpg" class="card-img-top rounded-top">
<div class="card-body text-center">
	<h6 class="card-title text-truncate fw-bold"><?php echo htmlspecialchars($g['title']); ?></h6>
	<a href="game_profile.php?id=<?php echo $g['gameID']; ?>" class="btn-sm btn-success w-100">See More</a>
</div>
</div>
</div>
	<?php endforeach; ?>
	<?php endif;?>
</div>
</section>
<?php endif; ?>

<section class="mb-5">
	<h3 class="text-danger border-bottom pb-2">Upcoming Games!</h3>
<div class="row mt-3">
	<?php foreach ($upc as $g): ?>
<div class="col-6 col-md-3 mb-4">
<div class="card h-100 shadows-sm game-card border-0">
	<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $g['cover_url']; ?>.jpg" class="card-img-top rounded-top">
<div class="card-body text-center">
	<h6 class="card-title text-truncate fw-bold"><?php echo htmlspecialchars($g['title']); ?></h6>
	<a href="game_profile.php?id=<php echo $g['gameId']; ?>" class="btn btn-sm w-100">Watchlist</a>
</div>
</div>
</div>
	<?php endforeach; ?>
</div>
</section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
