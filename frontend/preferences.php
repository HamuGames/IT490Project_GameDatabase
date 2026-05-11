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
$request = array();
$request['type'] = "get_preferences";
$request['session_key'] = $_SESSION['session_key'];

$response = $client->send_request($request);

$evryPlatform = [];
$evryGenre = [];
$usrPlats = [];
$usrGens = [];

if (isset($response['returnCode']) && $response['returnCode'] == '1') {
$evryPlatform = $response['data']['all_platforms'] ?? [];
$evryGenre = $response['data']['all_genres'] ?? [];
$usrPlats = $response['data']['user_platforms'] ?? [];
$usrGens = $response['data']['user_genres'] ??[];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>My Preferences - GAMERS DUNGEON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include('navBar.php'); ?>
<!--Similar to the onboardign file, this page first gets the current settings using user id in the user_genres and user_platforms tables so that they already show clicked. whatever changes user makes first deletes all entries from table and selecs their new choice.s-->
<div class="container mt-5">
<h1 class="mb-2 fw-bold">Gaming Preferences</h1>
<p class="text-muted mb-4">Select the platforms you own and the genres you like to receive personalized reccomendaitions!</p>
<form action="savePreferences.php" method="POST" class="bg-white p-4 shadow-sm rounded border">
 <div class="row">
 <div class="col-md-6 mb-4">
 <h4 class="mb-3 text-primary border-bottom pb-2">My Platforms</h4>
  <div class="row">
   <?php foreach ($evryPlatform as $platform): ?>
   <div class="col-6 mb-2">
    <div class="form-check">
 <input class="form-check-input" type="checkbox" name="platforms[]"
 value="<?php echo $platform['id']; ?>"
 id="plat_<?php echo $platform['id']; ?>"
 <?php echo in_array($platform['id'], $usrPlats) ? 'checked' : ''; ?>>
 <label class="form-check-label" for="plat_<?php echo $platform['id']; ?>">
<?php echo htmlspecialchars($platform['name']); ?>
</label>
</div>
   </div>
  <?php endforeach; ?>
 </div>
  </div>
  <div class="col-md-6 mb-4">
  <h4 class="mb-3 text-success border-bottom pb-2">Favorite Genres</h4>
 <div class="row">
<?php foreach ($evryGenre as $genre): ?>
 <div class="col-6 mb-2">
 <div class="form-check">
<input class="form-check-input" type="checkbox" name="genres[]"
 value="<?php echo $genre['id']; ?>"
  id="genre_<?php echo $genre['id']; ?>"
 <?php echo in_array($genre['id'], $usrGens) ? 'checked' : ''; ?>>
 <label class="form-check-label" for="genre_<?php echo $genre['id']; ?>">
 <?php echo htmlspecialchars($genre['name']); ?>
 </label>
</div>
 </div>
 <?php endforeach; ?>
 </div>
</div>
</div>
  <hr>
  <div class="d-flex justify-content-between align-items-center mt-3">
<small class="text-muted">You can update these at any time.</small>
<button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">Save Preferences</button>
</div>
</form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
