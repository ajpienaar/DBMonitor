# DBMonitor
a DB monitor that sends slack notifications on new activity "user added" and creates Asana task if failed

This application monitors a specified database table for the addition of new tasks. Upon detection of a new task, it sends a detailed Slack message derived from the table's data to a designated channel. In the event of any failures or hitches in the notification process, the application automatically craetes an Asana task to alert and assist the responsible team.

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features
1. **DB Monitor**: This specific setup monitors a table for new entries in the form of new users.
2. **Asana Integration**: Automatically creates tasks in Asana with relevant error details if any occur.
3. **Slack Integration**: Sends an instant notification to a Slack channel with details of a new user that was added to the DB table.

## Installation
1. Clone the repository to your project directory.
2. Navigate to the cloned directory and include the files in your project.

## Configuration

### Database - open `config.php` and edit:
1. Add the credentials of the database that will be monitored in the config file

### Create DB and Tables - open and run `tasks.sql` 
1. Creates a DB and relevant tables (edit as needed)
2. Adds a triger to the member table that listens for new members added and sends the data to the pending tasks table,script listning for changes and triggers a slack message on new member

### Asana - open `create_asana_task.php` and edit:
1. Set up an Asana Access Token and create an ENV variable to host it.
2. Provide the Asana workspace ID where tasks should be created.
3. Add a task name.
4. Add the assignee ID.
5. Add the project ID.
6. Set a custom due date.
7. Download the latest cacerts and store as `cacert.pem`. See [cURL CA Extract](https://curl.se/docs/caextract.html) for more details.

### Slack - open `send_slack_message.php` and edit:
1. Set up a custom slack webhook and create a slack app.
2. Edit the webhook details.
3. Download the latest cacerts and store as `cacert.pem`. See [cURL CA Extract](https://curl.se/docs/caextract.html).

## Contributing
Contributions are welcome!

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.