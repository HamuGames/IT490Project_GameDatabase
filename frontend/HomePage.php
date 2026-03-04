<?php
session_start();
if (!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] !== true) {
        header("Location: index.php");
        exit(0);
}
if (!isset($_SESSION['console'])) {
    header("Location: UserPreferences.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Home - GAMERS DUNGEON</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-dark text-white">

<div class="container mt-5">

<div class="card bg-secondary text-white shadow">
<div class="card-body">

<h1 class="display-5">
Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
</h1>

<hr>

<h4>Your Gaming Profile</h4>

<p>
<b>Console(s):</b>
<?php echo htmlspecialchars($_SESSION['console']); ?>
</p>

<p>
<b>Games You Play:</b><br>
<?php echo nl2br(htmlspecialchars($_SESSION['games'])); ?>
</p>

<p>
<b>Purpose:</b>
<ul>
<?php
if (isset($_SESSION['purpose'])) {
    foreach ($_SESSION['purpose'] as $p) {
        echo "<li>" . htmlspecialchars($p) . "</li>";
    }
}
?>
</ul>
</p>

<hr>

<div class="d-grid gap-2">
<a href="logout.php" class="btn btn-danger">

</a>
</div>

</div>
</div>

</div>

</body>
</html>

