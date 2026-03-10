<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

$game = null;
$error = "";

if (isset($_GET['id'])) {
	$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
	$request = array();
	$request['type'] = "get_game_details";
	$request['game_id'] = $_GET['id'];

	$response = $client->send_request($request);
	if (isset($response['returnCode']) && $response['returnCode'] == '1') {
	$game = $response['data'];
	}
	else {
	$error = "Game Not Found";
	}
}
else {
	header("Location: search_results.php");
	exit();
}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $game ? htmlspecialchars($game['title']) : "Game Profile"; ?> </title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/main.css">
</head>
<body class="gamer-background">
<?php include('navBar.php'); ?>
<div class="container mt-5">
<?php if ($game): ?>
<div class="gamer-card card shadow-lg border-0 overflow-hidden">
<div class="row g-0">
<?php if (isset($_SESSION['library_mess'])): ?>
    <div class="alert alert-<?php echo $_SESSION['library_mess_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['library_mess']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
        unset($_SESSION['library_mess']);
        unset($_SESSION['library_mess_type']);
    ?>
<?php endif; ?>
<div class="col-md-4">
	<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $game['cover_url']; ?>.jpg" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Cover">
</div>
<div class="col-md-8 p-4 gamer-card">
<h1 class="fw-bold mb-1"><?php echo htmlspecialchars($game['title']); ?></h1>
<p class="text-white ">Release Date: <?php echo htmlspecialchars($game['release_date']); ?></p>
<div class="badge bg-primary fs-5 mb-4">Rating: <?php echo round($game['rating']); ?>/100</div>
<h4>Description:</h4>
<p class="lead text-secondary"><?php echo htmlspecialchars($game['summary']); ?></p>
<p class="text-white"><strong>Available on:</strong> <?php echo htmlspecialchars($game['platform_list'] ?? 'N/A'); ?></p>
<div class="mt-5">
    <form action="userLibrary.php" method="POST" class="d-inline-block">
        <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['gameId']); ?>">
    <!--Right now this section just changes the status of the game when adding to library. NEXT STEPS: inside myLibrary user should be able to see a separate section for watchlist, playing, completed. Additionally users will be able to get EMAIL alerts when a game with a release data over to NOW() becomes current. Signaling new release.  push-->    
        <div class="input-group">
            <select name="status" class="form-select form-select-lg" style="max-width: 150px;">
                <option value="watchlist">Watchlist</option>
                <option value="playing">Playing</option>
                <option value="completed">Completed</option>
            </select>
            <button type="submit" class="btn btn-success btn-lg px-4 shadow">Add to Library</button>
        </div>
    </form>
    <br>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg ms-2">Back to Results</a>
</div>
</div>
</div>
</div>
<?php else: ?>
<div class="alert alert-danger text-center"><?php echo $error; ?>
</div>
<?php endif; ?>
</div>
</body>
</html>
