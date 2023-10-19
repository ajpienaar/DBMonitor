CREATE DATABASE IF NOT EXISTS tasks;
USE tasks;

-- Customers
DROP TABLE IF EXISTS member;
CREATE TABLE IF NOT EXISTS member (
  memberID INT AUTO_INCREMENT NOT NULL,
  firstname VARCHAR(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  lastname VARCHAR(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  email VARCHAR(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  username VARCHAR(32) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  password VARCHAR(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  role INT NOT NULL DEFAULT 1,
  PRIMARY KEY (memberID)
) AUTO_INCREMENT=1;


-- Tasks to complete
DROP TABLE IF EXISTS pending_tasks;
CREATE TABLE IF NOT EXISTS pending_tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    task_type VARCHAR(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
    task_data TEXT COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    status VARCHAR(50) COLLATE utf8mb4_0900_ai_ci DEFAULT "pending",
    created_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_timestamp TIMESTAMP DEFAULT NULL
);

-- Adds a triger to the member table that listens for new members added and sends the data to the pending tasks table, i then have some script listning for changes and triggers a slack message on new member
DELIMITER //
CREATE TRIGGER new_member_insert_trigger
AFTER INSERT ON member FOR EACH ROW
BEGIN
    INSERT INTO pending_tasks (task_type, task_data)
    VALUES (
        'send_notification',
        CONCAT('Name: ', NEW.firstname, ', Last Name: ', NEW.lastname, ', Email: ', NEW.email, ', MemberID: ', NEW.memberID)
    );
END;
//
DELIMITER ;