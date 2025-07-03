<?php
$config_file = __DIR__ . '/../../private/config.ini';

if (!file_exists($config_file)) {
    die("Error: Configuration file not found at $config_file");
}

$config = parse_ini_file($config_file, true);

if ($config === false) {
    die("Error: Unable to parse configuration file. Check its syntax.");
}

$servername = $config['database']['host'];
$username = $config['database']['username'];
$password = $config['database']['password'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
