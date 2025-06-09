<?php
use Google\Cloud\Core\Timestamp;
include('includes/dbconfig.php');

session_start();

#retrieve session
$userID = $_SESSION["userID"];
           
#Pushing data to firestore table report
#declaring varialbes
$stamp = new Timestamp(new DateTime());
$desc = "Logout Attempt (Successful)";
$data = [
    'datetime' => $stamp,
    'description' => $desc,
    'uid' => $userID
];
$rpt_table = "report";

#get number of rows in the table report
$noOfRows = $database->getReference($rpt_table)->getSnapshot()->numChildren();

#creating a unique key
$pKey = "R" . ++$noOfRows;

#push data into table using the unique key created
$postdata = $database->getReference($rpt_table)->getChild($pKey)->set($data);

#destroy the session
echo '<script>alert("You have successfully logged out!"); window.location.href="index.php";</script>';
session_destroy();


