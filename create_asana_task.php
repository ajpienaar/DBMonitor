<?php

function postAsana($errorDetails,$errorName){
$asanaApiUrl = "https://app.asana.com/api/1.0/tasks?opt_fields=";
$bearer = getenv('ASANA');// Global Environment variable
$taskData = [
    "data" => [
        'workspace' => '', // Replace with the actual workspace ID in Asana
        'name' => "{$errorName}", // Name of the task
        'notes' => "{$errorDetails}", // Error message to include in the task
        'assignee' => '', // Replace with the assignee ID in Asana
        'projects' => [ 
            '' // Replace with the project ID in Asana
        ],
        'due_on' => date("Y-m-d", strtotime("+5 days")) //due date
    ]
];
$jsonPayload = json_encode($taskData);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $asanaApiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json","authorization: Bearer {$bearer}","Content-Length: " . strlen($jsonPayload)));
// SSl location for reference
curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
$response = curl_exec($curl);
if ($response === false) {
    echo ("cURL Error: " . curl_error($curl));
} 
// else 
// {
//     echo ("cURL Response: " . $response);
// }    
// Close cURL resource
curl_close($curl);
}
?>
