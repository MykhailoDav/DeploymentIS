<?php
include 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL
)";
$conn->query($sql);

if (isset($_POST['message'])) {
    $msg = $conn->real_escape_string($_POST['message']);
    $conn->query("INSERT INTO messages (text) VALUES ('$msg')");
}

$result = $conn->query("SELECT * FROM messages");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LEMP Docker App</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f8f8f8; }
        form { margin-bottom: 20px; }
        input[type="text"] { padding: 8px; width: 300px; }
        input[type="submit"] { padding: 8px 12px; }
        .msg { background: white; padding: 10px; margin: 5px 0; border-radius: 5px; }
    </style>
</head>
<body>
<h2>LEMP Stack Web App</h2>
<form method="POST">
    <input type="text" name="message" placeholder="Введіть повідомлення">
    <input type="submit" value="Надіслати">
</form>
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='msg'>" . htmlspecialchars($row['text']) . "</div>";
    }
}
?>
</body>
</html>
