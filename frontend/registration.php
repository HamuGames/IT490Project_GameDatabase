<?php
// Start session to store user data after successful registration
session_start();

// Turn off error display for production (security)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

// Include RabbitMQ library for backend communication
require_once('../backend/rabbitMQLib.inc');

// Check if POST data exists
if (!isset($_POST))
{
    // If no POST data, return error message
    $msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
    echo json_encode($msg);
    exit(0);
}

// Get the POST request data
$request = $_POST;
$response = "unsupported request type, politely BYE";

// Initialize RabbitMQ client
$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

// Handle different request types
switch ($request["type"])
{
    case "register":
        // Send registration request to backend via RabbitMQ
        $response = $client->send_request($request);
        
        // Check if registration was successful
        if (is_array($response) && isset($response['status']) && $response['status'] === true) {
            // Set session variables for auto-login
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $request['username'];
            $_SESSION['session_key'] = $response['session_key'];
            
            // Set flag to indicate this is a new user (to show preferences)
            $_SESSION['new_registration'] = true;
            
            // Return success response with redirect to preferences page
            echo json_encode([
                'status' => true, 
                'message' => 'Registration successful!',
                'redirect' => 'preferences.php'  // New users go to preferences
            ]);
            exit(0);
        }
        break;
}

// Return response (either error or registration result)
echo json_encode($response);
exit(0);
?>
