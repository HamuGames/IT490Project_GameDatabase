<?p<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
	    header("Location: register.php");
	        exit();
}

if (isset($_SESSION['console'])) {
	    header("Location: HomePage.php");
	        exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	    $_SESSION['console'] = $_POST['console'];
	        $_SESSION['games'] = $_POST['games'];
	        $_SESSION['purpose'] = isset($_POST['purpose']) ? $_POST['purpose'] : [];

		    header("Location: HomePage.php");
		    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Preferences</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card bg-secondary">
<div class="card-body">

<h3 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h3>
<p class="text-center">Set Your Gaming Preferences</p>

<form method="POST">

<div class="mb-3">
<label class="form-label">What console(s) do you own?</label>
<input type="text" name="console" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">What games do you play?</label>
<textarea name="games" class="form-control" rows="3" required></textarea>
</div>

<div class="mb-3">
<label class="form-label">What are you using this website for?</label><br>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Regular Gamer Preferences">
<label class="form-check-label">Regular Gamer Preferences</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Gamer Recommendations">
<label class="form-check-label">Gamer Recommendations</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Content Creation">
<label class="form-check-label">Content Creation</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Just for Curiosity">
<label class="form-check-label">Just for Curiosity</label>
</div>

</div>

<div class="d-grid">
<button class="btn btn-success">Continue to Homepage</button>
</div>

</form>

</div>
</div>
</div>
</div>
</div>

</body>
</html>
session_
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: register.php");
    exit();
}

if (isset($_SESSION['console'])) {
    header("Location: HomePage.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['console'] = $_POST['console'];
    $_SESSION['games'] = $_POST['games'];
    $_SESSION['purpose'] = isset($_POST['purpose']) ? $_POST['purpose'] : [];

    header("Location: HomePage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Preferences</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card bg-secondary">
<div class="card-body">

<h3 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h3>
<p class="text-center">Set Your Gaming Preferences</p>

<form method="POST">
<div class="mb-3">
<label class="form-label">What console(s) do you own?</label>
<input type="text" name="console" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">What games do you play?</label>
<textarea name="games" class="form-control" rows="3" required></textarea>
</div>

<div class="mb-3">
<label class="form-label">What are you using this website for?</label><br>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Regular Gamer Preferences">
<label class="form-check-label">Regular Gamer Preferences</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Gamer Recommendations">
<label class="form-check-label">Gamer Recommendations</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Content Creation">
<label class="form-check-label">Content Creation</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Just for Curiosity">
<label class="form-check-label">Just for Curiosity</label>
</div>

</div>

<div class="d-grid">
<button class="btn btn-success">Continue t<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
	    header("Location: register.php");
	        exit();
}

if (isset($_SESSION['console'])) {
	    header("Location: HomePage.php");
	        exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	    $_SESSION['console'] = $_POST['console'];
	        $_SESSION['games'] = $_POST['games'];
	        $_SESSION['purpose'] = isset($_POST['purpose']) ? $_POST['purpose'] : [];

		    header("Location: HomePage.php");
		    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Preferences</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card bg-secondary">
<div class="card-body">

<h3 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h3>
<p class="text-center">Set Your Gaming Preferences</p>

<form method="POST">

<div class="mb-3">
<label class="form-label">What console(s) do you own?</label>
<input type="text" name="console" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">What games do you play?</label>
<textarea name="games" class="form-control" rows="3" required></textarea>
</div>

<div class="mb-3">
<label class="form-label">What are you using this website for?</label><br>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Regular Gamer Preferences">
<label class="form-check-label">Regular Gamer Preferences</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Gamer Recommendations">
<label class="form-check-label">Gamer Recommendations</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Content Creation">
<label class="form-check-label">Content Creation</label>
</div>

<div class="form-check">
<input class="form-check-input" type="checkbox" name="purpose[]" value="Just for Curiosity">
<label class="form-check-label">Just for Curiosity</label>
</div>

</div>

<div class="d-grid">
<button class="btn btn-success">Continue to Homepage</button>
</div>

</form>

</div>
</div>
</div>
</div>
</div>

</body>
</html>
