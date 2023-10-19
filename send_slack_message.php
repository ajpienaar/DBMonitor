<?php
// Function to send a custom message to Slack webhook, utalized here for error and exception mesages, but can be used for pretty much anything
function sendToSlack($message,$emoji) {
    $slackWebhookURL = ''; // Slack webhook URL that was created in slack admin
    //takes the emoji and message passed as a parameter and stores it in a variable
    $errorMessage = "$emoji $message";
    $postData = array('text' => $errorMessage);
    $jsonPayload = json_encode($postData);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $slackWebhookURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($jsonPayload)));
    // checks for a valid SSL certificate using the file downloaded as a reference
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    
    $response = curl_exec($ch);
    
    // Check for errors and success in cURL request / response and loggs them
    if ($response === 'ok') {
        return true;
    } else {
        return false;
    }    
    // Close cURL resource
    curl_close($ch);
}

?>
