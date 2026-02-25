<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
	header("Location: HomePage.php");
	exit(0);
}
?>
<html>
<script>

///function HandleLoginResponse(response)
///{
///	console.log("SERVER: '", response, "'");
///		var text = JSON.parse(response);
// document.getElementById("textResponse").innerHTML = response+"<p>";     
      //  document.getElementById("textResponse").innerHTML = "response: "+text+"<p>";
///alert("MESSAGE: " + text.message);
///	document.getElementById("textResponse").innerHTML = text.message;
///}
function HandleLoginResponse(response)
{
    console.log("SERVER RAW RESPONSE:", response); 
    if (!response || response.trim() === "") {
        alert("ERROR: The server sent empty response");
        return;
    }

    try {
        var text = JSON.parse(response);
        //alert("SUCCESS! MESSAGE: " + text.message);
	    if (text.status === true) {
		    window.location.href = "HomePage.php";}
	    else {
		    document.getElementById("textResponse").innerHTML = "<b style='color:red;'>" + text.message + "</b>";
	    }
    } catch (e) {
        alert("CRASH! The server sent invalid JSON. Check the Console (F12) to see what it sent.");
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
	//	else{
	//		alert("Server error: " + this.status);
	//	}
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
<h1>login page</h1>
<body>
<form>
	<input type="text" id="user" placeholder="Username" required>
	<input type="password" id="pass" placeholder"Password" required>
	<button type="button" onclick="SendLoginRequest(document.getElementById('user').value,document.getElementById('pass').value);">Login</button><br>
	<div id="textResponse">
		 
	</div>
	<a href="register.php">register here</a>
</form>
</body>
</html>
