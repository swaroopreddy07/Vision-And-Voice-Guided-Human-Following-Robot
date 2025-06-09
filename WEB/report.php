<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>CareBot-Report</title>
        <link rel="stylesheet" href="css/report.css">
        <link href='https://fonts.googleapis.com/css?family=Varela Round' rel='stylesheet'>
    </head>
    <body>
        <?php
        include 'header.php';
        ?>
        <br/><br/><br/><br/><br/>

        <?php

        use Google\Cloud\Core\Timestamp;

include('includes/dbconfig.php');
        session_start();
        $ref_table = 'report';

        if (isset($_SESSION["userID"])) {
            $userID = $_SESSION["userID"];
            $reportdata = $database->getReference($ref_table)->orderByChild('uid')->equalTo($userID)->getValue();
        } else {            
            echo '<script>alert("You are not logged in!."); window.location.href="index.php";</script>';
        }
        ?>

        <div class="reportDiv" id="reportDiv">
            <h1>Report</h1>
            <form method="get" action="report.php">

                <table id="reporttbl" class="reporttbl">
                    <tr>
                        <th width="20%">No.</th>
                        <th width="40%">Date and Time</th>
                        <th width="40%">Description</th>
                    </tr>

                    <?php
                    if ($reportdata >= 0) {
                        $n = 1;
                        foreach ($reportdata as $key => $row) {
                            ?>                            
                            <tr>
                                <td><?php echo $n; ?></td>
                                <td><?php echo $row['datetime']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                            </tr>
                            <?php
                            $n++;
                        }
                    }
                    ?>

                    <div class="btncontainer">
                        <!-- <a class="pdfbtn" href="pdf.php">Save as PDF</a> -->
                        <a class="generatebtn" href="excel.php">Save as Excel</a>
                    </div>
                </table>

            </form>
        </div>

    </body>

    <script src="js/jquery-3.2.1.min.js"></script>		

</html>
