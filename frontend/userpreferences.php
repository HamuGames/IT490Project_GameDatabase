<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Preferences</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #111;
            color: white;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
        }
        input, textarea {
            width: 80%;
            padding: 8px;
            margin: 8px 0;
            border-radius: 5px;
            border: none;
        }
        .checkbox-group {
            text-align: left;
            margin-left: 10%;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <h3>Set Your Gaming Preferences</h3>

    <form method="POST" action="SavePreferences.php">

        <!-- Console -->
        <label><b>What console(s) do you own?</b></label><br>
        <input type="text" name="console" placeholder="Example: PS5, Xbox Series X, PC"><br>

        <!-- Games -->
        <label><b>What games do you play?</b></label><br>
        <textarea name="games" rows="3" placeholder="Example: Call of Duty, Fortnite, Elden Ring"></textarea><br>

        <!-- Website Purpose -->
        <label><b>What are you using this website for?</b></label><br>
        <div class="checkbox-group">
            <input type="checkbox" name="purpose[]" value="Regular Gamer Preferences"> Regular Gamer Preferences<br>
            <input type="checkbox" name="purpose[]" value="Gamer Recommendations"> Gamer Recommendations<br>
            <input type="checkbox" name="purpose[]" value="Content Creation"> Content Creation<br>
            <input type="checkbox" name="purpose[]" value="Just for Curiosity"> Just for Curiosity<br>
        </div>

        <br>
        <button type="submit">Save Preferences</button>
    </form>
</div>

</body>
</html>
