<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
	header("Location: HomePage.php");
	exit(0);
}
?>
<html>
<script>



<!DOCTYPE html>
<html>

<head>

<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
rel="stylesheet"
integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
crossorigin="anonymous">

<link href="css/main.css" rel="stylesheet">

<script>

function HandleRegisterResponse(response)
{
    console.log("SERVER RAW RESPONSE:', response);

    if(!response || response.trim() === '') {
	    alert("ERROR: The server sent empty response');
	    return;
    }
    try {
	    var text = JSON.parse(response);
	    document.getElementById("testResponse').innerHTML = text.message;

	    if (text.status === true) {
		    if (text.direct) {
			    window.location.href = text.redirect;
		    } else {
			    window.location.href = "HomePage.php";
		    }
	    }
    } catch (e) {
	    alert("CRASH! The server sent invalid JSON. Check the Console (F12) to see what it sent.");
	    console.error("The invalid response was:", response);
    }
}

function SendRegisterRequest(fName,lName,email,username,password)
{
    var request = new XMLHttpRequest();

    request.open("POST","registration.php",true);

    request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    request.onreadystatechange = function ()
    {
        if ((this.readyState == 4) && (this.status == 200))
        {
            HandleRegisterResponse(this.responseText);
        }
    }

    request.send(
        "type=register"
        +"&fName="+encodeURIComponent(fName)
        +"&lName="+encodeURIComponent(lName)
        +"&email="+encodeURIComponent(email)
        +"&username="+encodeURIComponent(username)
        +"&password="+encodeURIComponent(password)
    );
}

</script>

</head>


<body class="bg-light">


<div class="container mt-5">
<div class="row justify-content-center">

<div class="col-md-4 card p-4 shadow-sm">

<h2 class="text-center mb-4">Registration Page</h2>

<form>

<div class="mb-3">

<label class="form-label">First Name</label>
<input class="form-control" type="text" id="fName" placeholder="First Name" required><br>

<label class="form-label">Last Name</label>
<input class="form-control" type="text" id="lName" placeholder="Last Name" required><br>

<label class="form-label">Email Address</label>
<input class="form-control" type="text" id="email" placeholder="Your Email" required><br>

<label class="form-label">New Username</label>
<input class="form-control" type="text" id="username" placeholder="New Username" required><br>

<label class="form-label">New Password</label>
<input class="form-control" type="password" id="password" placeholder="New Password" required><br>

</div>


<div class="d-grid gap-2">

<button class="btn btn-primary" type="button"

onclick="SendRegisterRequest(
document.getElementById('fName').value,
document.getElementById('lName').value,
document.getElementById('email').value,
document.getElementById('username').value,
document.getElementById('password').value
);">

Register Now

</button><br>

</div>


<div class="alert alert-info text-center" id="textResponse">
</div>


<div class="mt-3 text-center">
<a href="index.php">Login here</a>
</div>

</form>

</div>
</div>
</div>

</body>
</html>
