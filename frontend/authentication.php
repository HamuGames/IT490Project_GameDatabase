<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

//if (!isset($_SESSION['authentication']) || $_SESSION['authentication'] !== true) {
//        header("Location: index.php");
//        exit(0);
//}

$loggedUser = $_SESSION['username'];

?>
<!DOCTYPE html>

<html>
<head>
<title> TWO FACTOR AUTHENTICATION </title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/main.css">
<style>
.game-card img { height: 300px; object-fit: cover; transition: transform .3s; }
.game-card:hover { transform: translateY(-5px); transition: 0.3s; }
</style>

<script>
	function requestCode() {
		var method = document.querySelector('input[name="method"]:checked').value;
		var username = "<?php echo $loggedUser; ?>";

		var request = new XMLHttpRequest();
		request.open("POST", "2faBackend.php", true);
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.onreadystatechange = function () {
			if ((this.readyState == 4) && (this.status == 200)) {
				var text = JSON.parse(this.responseText);
				if (text.status === true) {
					document.getElementById("textResponse").innerHTML = "<b style='color:green;'>Code has been sent</b>";
					document.getElementById("verification").style.display = "block";
				} else {
					document.getElementById("textResponse").innerHTML = "<b style='color:red;>" + text.message + "</b>";
				}
			}
		}
		request.send("type=sendCode&User" + username + "&Method=" + method);
	}

	function verifyCode() {
		var code = document.getElementById('code').value;
		var username = "<?php echo $loggedUser; ?>";
		var request = new XMLHttpRequest();
		request.open("POST", "2faBackend.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		request.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				var text = JSON.parse(this.responseText);
				if (text.status === true) {
					window.location.href = "HomePage.php";
				} else {
					document.getElementById("textResponse").innerHTML = "<b style='color:red;'>" + text.message + "</b>";
				}
			}
		}
		request.send("type=verifyCode&User" + username + "&Code=" + code);
	}
</script
</head>
<body class="gamer-background">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="gamer-card col-md-4 card p-4 shadow-sm">
<h2 class="text-center mb-4">Two Factor Authentication</h2>
<div class="mb-3 text-center">
        <label class=="form-label">How do you want to receive your code?</label>
	<div class="form-check form-check-inline">
		<input class="form-check-input" type="radio" name="method" id="email" value="email" checked>
		<label class="form-check-label" for="email">Email</label>
	</div>
	<div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="method" id="sms" value="sms">
                <label class="form-check-label" for="sms">SMS</label>
        </div>
</div>
<div class "d-grid gap-2 mb-3">
	<button class="btn btn-warning" type="button" onclick="requestCode();">Send Code</button>
</div>
<div id="verification" style="display: none;">
	<hr>
	<div class "mb-3">
		<label class="form-label">Enter 6 Digit verification code</label>
		<input type="text" id="code" class="form-control" placeholder="123456" maxlenght="6">
	</div>
	<div class="d-grid gap-2 mb-3">
		<button class="btn btn-primary" type="button" onclick="verifyCode();">Verify</button>
	</div>
</div>
        <div class="alert alert-info text-center" id="textResponse">

        </div>
<div class="mt-3 text-center">
        <a href="logout.php">Back to login</a>
</div>
</div>
</div>
</div>



</html>

