<?php
// Start the session to check if user is already logged in
session_start();

// If user is already logged in, redirect them to homepage
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        header("Location: HomePage.php");
        exit(0);
}
?>
<html>
<script>

/**
 * Handles the response from the registration server
 * @param {string} response - The raw JSON response from the server
 */
function HandleRegisterResponse(response)
{
    // Log the raw response for debugging purposes
    console.log("SERVER RAW RESPONSE:", response);
    
    // Check if response is empty
    if (!response || response.trim() === "") {
        alert("ERROR: The server sent empty response");
        return;
    }

    try {
        // Parse the JSON response
        var text = JSON.parse(response);
        
        // Display the message from server in the alert div
        document.getElementById("textResponse").innerHTML = text.message;
        
        // If registration was successful
        if (text.status === true) {
            // Check if there's a redirect URL (for new users going to preferences)
            if (text.redirect) {
                // Redirect to preferences page for new users
                window.location.href = text.redirect;
            } else {
                // For existing users or if no redirect specified, go to homepage
                window.location.href = "HomePage.php";
            }
        }
    } catch (e) {
        // Handle JSON parsing errors
        alert("CRASH! The server sent invalid JSON. Check the Console (F12) to see what it sent.");
        console.error("The invalid response was:", response);
    }
}

/**
 * Sends the registration request to the server
 * @param {string} fName - User's first name
 * @param {string} lName - User's last name
 * @param {string} email - User's email address
 * @param {string} username - Desired username
 * @param {string} password - Desired password
 */
function SendRegisterRequest(fName, lName, email, username, password)
{
    // Create a new XMLHttpRequest
    var request = new XMLHttpRequest();
    
    // Configure POST request to registration.php
    request.open("POST", "registration.php", true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // Set up callback for when request completes
    request.onreadystatechange = function ()
    {
        // Check if request is complete and successful
        if ((this.readyState == 4) && (this.status == 200))
        {
            // Pass the response to our handler function
            HandleRegisterResponse(this.responseText);
        }		
    }
    
    // Send the request with all form data
    request.send("type=register&fName=" + fName + "&lName=" + lName + 
                 "&email=" + email + "&username=" + username + "&password=" + password);
}
</script>
<head>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <!-- Custom CSS -->
    <link href="css/main.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4 card p-4 shadow-sm">
            <h2 class="text-center mb-4">Registration Page</h2>
            
            <!-- Registration Form -->
            <form>
                <div class="mb-3">
                    <!-- First Name Input -->
                    <label class="form-label">First Name</label>
                    <input class="form-control" type="text" id="fName" placeholder="First Name" required><br>
                    
                    <!-- Last Name Input -->
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lName" placeholder="Last Name" required><br>
                    
                    <!-- Email Input -->
                    <label class="form-label">Email Address</label>	
                    <input type="text" class="form-control" id="email" placeholder="Your Email" required><br>
                    
                    <!-- Username Input -->
                    <label class="form-label">New Username</label>	
                    <input type="text" class="form-control" id="username" placeholder="New Username" required><br>
                    
                    <!-- Password Input -->
                    <label class="form-label">New Password</label>	
                    <input type="password" class="form-control" id="password" placeholder="New Password" required><br>
                </div>
                
                <!-- Register Button -->
                <div class="d-grid gap-2">	
                    <button class="btn btn-primary" type="button" 
                            onclick="SendRegisterRequest(
                                document.getElementById('fName').value,
                                document.getElementById('lName').value,
                                document.getElementById('email').value,
                                document.getElementById('username').value,
                                document.getElementById('password').value
                            );">Register Now</button><br>
                </div>
                
                <!-- Response Message Area -->
                <div class="alert alert-info text-center" id="textResponse"></div>
                
                <!-- Link to Login Page -->
                <div class="mt-3 text-center">
                    <a href="index.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
