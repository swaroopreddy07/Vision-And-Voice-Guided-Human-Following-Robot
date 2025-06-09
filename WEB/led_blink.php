<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

// Firebase credentials path
$firebaseCredentials = __DIR__ . '/firebase-credentials.json';

// Initialize Firebase
$factory = (new Factory)->withServiceAccount($firebaseCredentials)->withDatabaseUri('https://companionapp-acc4c-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$reference = $database->getReference('robot_commands/led');

// Handle button clicks
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['on'])) {
        $reference->set("ON");
    } elseif (isset($_POST['off'])) {
        $reference->set("OFF");
    }
}

// HTML for buttons
?>
<!DOCTYPE html>
<html>
<head>
    <title>LED Control</title>
</head>
<body>
    <h2>Control LED</h2>
    <form method="post">
        <button name="on">Turn LED ON</button>
        <button name="off">Turn LED OFF</button>
    </form>
</body>
</html>
