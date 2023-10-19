<?php

include "config.php"; // Load in any variables for Db connection
include 'send_slack_message.php'; //includes slack function
include "create_asana_task.php"; //includes asana function

function checkAndNotifyChanges() {
    $db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    // check if db connection successful
    if (!$db_connection) {
        echo "Error: Unable to connect to MySQL " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit;
    }

    // Save query to retrieve items from the "pending_tasks" table where status is pending in a variable
    $query = "SELECT * FROM pending_tasks WHERE status = ?";
    $stmt = mysqli_prepare($db_connection,$query);
    //update the variable with the string to look for
    $task = "pending";
    mysqli_stmt_bind_param($stmt, "s", $task);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // if the query executed successfully with results
    if ($result) {
        // (this is overcomplicated and only as a result of the trigger adding the data in teh table in one column - edit as needed)
        while ($row = mysqli_fetch_assoc($result)) {
            // assign the "data" row from the table to the inputString variable
            $inputString = $row['task_data'];
            // split the data at the , and store it in an array called itemsArray
            $itemsArray = explode(',', $inputString);
            // prepare a second array to store the key-value pairs to split the data from the string
            $keyValue = [];
            // loop through the items array
            foreach ($itemsArray as $item) {
                // trim each item from white spaces
                $item = trim($item);
                // separate the data at the : and prepare to populate the second array with the key values
                list($key, $value) = explode(':', $item, 2);
                // adds the values and keys to the array
                $keyValue[$key] = $value;
            }
        
            // if the task type listed in the pending_tasks db is marked as send notification, run the slack function with the prepared parameters from the loop above
            if ($row['task_type'] === 'send_notification') {
                $slackMessage = "New member added => Name: {$keyValue['Name']}, Surname: {$keyValue['Last Name']}, Email: {$keyValue['Email']}, Member ID: {$keyValue['MemberID']}";
                // if it was sent successfully
                if (sendToSlack($slackMessage, ":eyes:")) {
                    // Prepare the update query to change the status to complete in the pending_tasks db when processed
                    $updateQuery = "UPDATE pending_tasks SET status = ?, completed_timestamp = NOW() WHERE task_id = ?";
                    // prepare the update statement
                    $stmt = mysqli_prepare($db_connection, $updateQuery);
                    $status = "complete";
                    $taskID = $row['task_id'];
                    // Bind the status and id parameters to the prepared statement
                    mysqli_stmt_bind_param($stmt, "si", $status, $taskID);
                    
                    // run the SQL update task and handle errors
                    if (mysqli_stmt_execute($stmt)) {
                        echo "Task updated successfully.";
                    } else {
                        echo "Error updating task: {$taskID}" . mysqli_error($db_connection);
                        postAsana("Error updating task {$taskID} status to: {$status}", mysqli_error($db_connection));
                    }
                    // Close the prepared statement
                    mysqli_stmt_close($stmt);
                } 
                
                else {
                    // if the message was not sent
                    echo "Error sending new user DB slack notification";
                    // create asana task to investigate
                    postAsana("Error sending slack notification: {$slackMessage}","Slack new user DB entry sending error");
                }
            }
            // Can elaborate on the task_types here eventually with more tasks in if statements
        }    
    } else {

        echo "No new tasks: " . mysqli_error($db_connection);
    }
    

    // Close the database connection
    mysqli_close($db_connection);
}

// Call the function to check and process pending tasks
checkAndNotifyChanges();

?>
