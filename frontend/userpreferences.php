<?php
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
    $_SESSION['console'] = htmlspecialchars($_POST['console']);
    $_SESSION['games'] = htmlspecialchars($_POST['games']);

    if (isset($_POST['purpose'])) {
        $_SESSION['purpose'] = $_POST['purpose'];
    } else {
        $_SESSION['purpose'] = array();
    }

    header("Location: HomePage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>User Preferences - GAMERS DUNGEON</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-dark text-white">

<div class="container mt-5">

<div class="card bg-secondary text-white shadow">
<div class="card-body">

<h2 class="mb-3">
Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>
</h2>

<p class="lead">
Tell us about your gaming preferences
</p>

<hr>

<form method="POST">

<!-- Console -->
<div class="mb-3">
<label class="form-label"><b>What console(s) do you own?</b></label>
<input type="text" name="console" class="form-control" placeholder="PC, PS5, Xbox, Switch..." required>
</div>

<!-- Games -->
<div class="mb-3">
<label class="form-label"><b>What games do you play?</b></label>
<textarea name="games" class="form-control" rows="3" placeholder="Fortnite, Elden Ring, Call of Duty..." required></textarea>
</div>

<!-- Purpose -->
<div class="mb-3">

<label class="form-label"><b>What are you using this website for?</b></label>

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

<hr>

<div class="d-grid">
<button type="submit" class="btn btn-success">
Save Preferences & Continue
</button>
</div>

</form>

</div>
</div>

</div>

</body>
</html>
