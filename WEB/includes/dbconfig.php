<?php

require __DIR__ . '/../vendor/autoload.php'; // Adjust the path if needed

use Kreait\Firebase\Factory;

$firebase = (new Factory)
    ->withServiceAccount(__DIR__ . '/../firebase-credentials.json') // Adjust the path if needed
    ->withDatabaseUri('https://companionapp-acc4c-default-rtdb.firebaseio.com/'); // Replace with your actual database URL

$database = $firebase->createDatabase();

?>
