<?php 
// Assuming the user is logged in and the user_id is stored in the session
session_start();
$user_id = $_SESSION['user_id'];  // Get the user ID from session

// Send user_id to the Flask app
$python_url = 'http://localhost:5000/recommendations';
$data = array('user_id' => $user_id);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $python_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Now $response will contain the recommended products in JSON format
echo $response;
