<!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<title><?php echo $game ? htmlspecialchars($game['title']) : "Game Profile"; ?> </title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<body class="bg-light">
<?php include('navBar.php'); ?>
<div class="container mt-5">
<?php if ($game): ?>
<div class="card shadow-lg border-0 overflow-hidden">
<div class="row g-0">
<?php if (isset($_SESSION['library_mess'])): ?>
    <div class="alert alert-<?php echo $_SESSION['library_mess_type']; ?> alert-dismissible fade show" role="alert">

@@ -57,42 +54,41 @@ else {
<div class="col-md-4">
	<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/<?php echo $game['cover_url']; ?>.jpg" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Cover">
</div>
<div class="col-md-8 p-4 bg-white">
	<h1 class="fw-bold mb-1"><?php echo htmlspecialchars($game['title']); ?></h1>
	<p class="text-muted">Released on: <?php echo htmlspecialchars($game['release_date']); ?></p>
	<div class="badge bg-primary fs-5 mb-4">Rating: <?php echo round($game['rating']); ?>/100</div>
	<h4>Description:</h4>
	<p class="lead text-secondary"><?php echo htmlspecialchars($game['summary']); ?></p>
	<p class="text-muted"><strong>Available on:</strong> <?php echo htmlspecialchars($game['platform_list'] ?? 'N/A'); ?></p>
		<div class="mt-5">
			<form action="userLibrary.php" method="POST" class="d-inline-block">
				<input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['gameId']); ?>">		
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
	<!-- this section is for writing the review of the game -->
		 	<form action="game_review.php" method="POST" class="d-inline-block">
				<label> Write a Review: </label>
				<input type="number" name="gameReview" placeholder= "rate ot of /10">
				<br>
				<input type="text" name="gameReview" placeholder="Write a review...">
				<button type="submit">Post Review</button>
			</form>
	<!-- show reviews here -->
			<div class="row">
			<?php foreach ($review as $gameReviews): ?>
			<a href="game_review.php?id=<?php echo $game['gameId']; ?>" class="text-decoration-none text-dark">
			</div>
			<a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg ms-2">Back to Results</a>
		</div>

</div>
</div>
</div>

