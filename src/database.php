<?php

error_reporting(E_ALL);
echo "version 3";
// Read the database connection parameters from environment variables
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

echo "DB_HOST -> $db_host";
echo "DB_NAME -> $db_name";
echo "DB_USER -> $db_user";
echo "DB_PASS -> $db_pass";

// Create a new PDO instance
$db_handle = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

// Create the "messages" table if it doesn't exist
$db_handle->exec("
 CREATE TABLE IF NOT EXISTS messages (
     id INT AUTO_INCREMENT PRIMARY KEY,
     message VARCHAR(255) NOT NULL
 )
");

// Create message
$stmt = $db_handle->query("SELECT COUNT(*) as count FROM messages");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$count = $row['count'];
$db_handle->exec("
 INSERT INTO messages (message)
 SELECT CONCAT('message-', '$count')
 WHERE NOT EXISTS (
  SELECT 1 FROM messages WHERE message = CONCAT('message-', '$count')
 )
");

// Retrieve all records from the "messages" table
$stmt = $db_handle->query("SELECT * FROM messages");

// Print all records
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 echo $row['id'] . " " . $row['message'] . "<br>";
}

// Close the database connection
$db_handle = null;
?>
