<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
	header("Location: HomePage.php");
	exit(0);
}
?>
<html>
<script>

function HandleLoginResponse(response)
{
    console.log("SERVER RAW RESPONSE:", response); 
    if (!response || response.trim() === "") {
        alert("ERROR: The server sent empty response");
        return;
    }

    try {
        var text = JSON.parse(response);
	    if (text.status === true) {
		    window.location.href = "HomePage.php";}
	    else {
		    document.getElementById("textResponse").innerHTML = "<b style='color:red;'>" + text.message + "</b>";
	    }
    } catch (e) {
        alert("Server Error.");
        console.error("The invalid response was:", response);
    }
}
function SendLoginRequest(username,password)
{
	var request = new XMLHttpRequest();
	request.open("POST","login.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange= function ()
	{
		
		if ((this.readyState == 4)&&(this.status == 200))
		{
			console.log("RESPONSE: ", this.responseText);
			HandleLoginResponse(this.responseText);
		}
	}
	request.send("type=login&username="+username+"&password="+password);
}
</script>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
	  crossorigin="anonymous">
<link href="css/main.css" rel="stylesheet">

</head>
<body class="gamer-background">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="gamer-card col-md-4 card p-4 shadow-sm">
<h2 class="text-center mb-4">login page</h2>
<form>
<div class="mb-3">
	<label class=="form-label">UserName</label>
	<input type="text" id="user" class="form-control"  placeholder="Username" required>
</div>
<div class="mb-3">
	<label class=="form-label">Password</label>
	<input type="password" id="pass" class="form-control" placeholder"Password" required>
</div>
<div class="d-grid gap-2">
	<button class="btn btn-primary" type="button" onclick="SendLoginRequest(document.getElementById('user').value,document.getElementById('pass').value);">Login</button><br>
</div>
	<div class="alert alert-info text-center" id="textResponse">
		 
	</div>
<div class="mt-3 text-center">
	<a href="register.php">register here</a>
</div>
</form>
</div>
</div>
</div>
</body>
</html>
