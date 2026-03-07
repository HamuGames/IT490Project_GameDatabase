<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: index.php");
        exit(0);
}
?>
<!DOCTYPE html>

<html>
<head>
<title> Home - GAMERS DUNGEON (or whatever the name of our site is lol) </title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include('navBar.php'); ?>
	<div class="container mt-5">
		<h1 class="display-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<hr>
<p class="lead">This is the HomePage! </p>
</div>
</body>
</html>

