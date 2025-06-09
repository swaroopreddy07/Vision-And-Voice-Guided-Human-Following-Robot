<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

#use related libraries
use Google\Cloud\Core\Timestamp;
use Dompdf\Dompdf;

require_once 'vendor/autoload.php';

include('includes/dbconfig.php');

session_start();
$ref_table = 'report';
$userID = $_SESSION["userID"];
$reportdata = $database->getReference($ref_table)->orderByChild('uid')->equalTo($userID)->getValue();

#generate the output of the pdf file
$output = "";

$output .= "
<head>
<style>
.reportDiv {
    border: 1px solid white;
    width: 30%;
    margin: auto;
    padding-left: 50px;
    padding-bottom: 40px;  
    border-radius: 15px;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
}

#reporttbl th{
    text-align: left;  
}

#reporttbl, #reportbl tr, #reporttbl td, #reporttbl th,{
    border: 1px solid black;
    border-collapse: collapse;
    padding: 10px; 
}

</style>
</head>
";

$output .= "
    <h1>Report</h1>
		<table id='reporttbl'>
                    <tr>
                        <th width='20%'>No.</th>
                        <th width='20%'>Date and Time</th>
                        <th width='20%'>Description</th>
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



#create pdf object
$dompdf = new Dompdf();

#printing the output 
$dompdf->loadHtml($output);

#setting the dimensions of the pdf
$dompdf->setPaper('A4', 'portrait'); 
$dompdf->render();

#naming the pdf and allowing the user to preview the document before downloading
$dompdf->stream('report.pdf', ['Attachment'=>0]);
