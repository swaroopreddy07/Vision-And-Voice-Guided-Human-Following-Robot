<?php

use Google\Cloud\Core\Timestamp;

include('includes/dbconfig.php');

session_start();
$ref_table = 'report';
$userID = $_SESSION["userID"];
$reportdata = $database->getReference($ref_table)->orderByChild('uid')->equalTo($userID)->getValue();

header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=report.xls");
header("Pragma: no-cache");
header("Expires: 0");

$output = "";

$output .= "
		<table>
                    <tr>
                        <td>No.</td>
                        <td>Date and Time</td>
                        <td>Description</td>
                    </tr>
	";

if ($reportdata >= 0) {
    $n = 1;
    foreach ($reportdata as $key => $row) {


        $output .= "
				<tr>
					<td>" . $n . "</td>
					<td>" . $row['datetime'] . "</td>
					<td>" . $row['description'] . "</td>
					
				</tr>
	";

        $n++;
    }
}

$output .= "
		</table>
	";

echo $output;
?>