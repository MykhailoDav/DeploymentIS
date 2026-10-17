<?php
$servername = "db";
$username = "webuser";
$password = "webpass";
$dbname = "webdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}
?>
