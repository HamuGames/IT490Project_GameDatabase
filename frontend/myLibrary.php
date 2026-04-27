<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

$myGames = [];
$error_msg = "";

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
$request = array();
$request['type'] = "get_user_library";
$request['session_key'] = $_SESSION['session_key'];

$response = $client->send_request($request);
if (isset($response['returnCode']) && $response['returnCode'] == '1') {
    $myGames = $response['data'];
} else {
    $error_msg = $response['message'] ?? "No games found.";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>My Game Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<?php include('navBar.php'); ?>
<div class="container mt-5">
  <h1 class="mb-4 fw-bold">My Library</h1>
  <a href="search_results.php" class="btn btn-outline-primary mb-4">Search Another Game</a>
<?php if (empty($myGames)): ?>
<div class="alert alert-warning text-center">
<?php echo htmlspecialchars($error_msg); ?><br>
            Add games to your watchlist! 
</div>
<?php else: ?>
 <div class="row">
 <?php foreach ($myGames as $game): ?>
 <div class="col-md-3 mb-4">
 <div class="card h-100 shadow-sm">
 <a href="game_profile.php?id=<?php echo $game['gameId']; ?>" class="text-decoration-none text-dark">
<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $game['cover_url']; ?>.jpg" class="card-img-top" style="height: 300px; object-fit: cover;">
 <div class="card-body text-center">
 <h6 class="card-title fw-bold text-truncate"><?php echo htmlspecialchars($game['title']); ?></h6>
 <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($game['status']); ?></span>
</div>
 </a>
<div class="card-footer bg-white border-0 text-center pt-2">
<a href="removeGame.php?id=<?php echo $game['gameId']; ?>" class="btn btn-sm btn-danger w-100">Remove from Library</a>
</div>
 </div>
  </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>
</div>

</body>
</html>
