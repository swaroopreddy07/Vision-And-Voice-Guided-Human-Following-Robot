<?php
include(__DIR__ . '/includes/dbconfig.php'); // Corrected path

try {
    $testRef = $database->getReference('test_connection')->set(['status' => 'connected']);
    echo "✅ Firebase Realtime Database is working!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
