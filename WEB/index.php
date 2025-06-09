<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CareBot-Login</title>

        <link rel="stylesheet" href="css/index.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    </head>

    <body>

        <div class="container">
            <div class="manager-textbox">
                <img src="" class="manager-portrait" style="height:100px">
                <div class="manager-text">
                    <form method="get" action="index.php">
                        <table cellspacing="0">
                            <tr>
                                <td class="fcol"><img class="iconimgf" src="img/usericon.png"/></td>
                                <td class="scol"><input name="userID" class="input-css" type="text" placeholder="User ID"></td>
                            </tr>
                        </table>

                        <table cellspacing="0">
                            <tr>
                                <td class="fcol"><img class="iconimgs" src="img/pwdicon.png"/></td>
                                <td class="scol"><input name="pwd" class="input-css" type="password" placeholder="Password"></td>
                            </tr>
                        </table>

                        <input class="loginbtn" value="Sign In" type="submit" name="login" >
                    </form>

                    <br/><br/><br/><br/><br/>
                    <a href="register.php" id="gotoCreate">Create Your Account→ </a>

                </div>
            </div>       
        </div>

        <?php
        include('includes/dbconfig.php');
        use Google\Cloud\Core\Timestamp;
        
        if (isset($_GET['login'])) {

            $ref_table = "user";
            $userprofile = $database->getReference($ref_table)->orderByChild('userID')->equalTo($_GET['userID'])->getValue();

            if ($userprofile >= 0) {

                foreach ($userprofile as $key => $row) {
                    //if ($row['userPassword'] == $_GET['pwd']) {
                    if (password_verify($_GET['pwd'], $row['userPassword'])) {
                        
                        #Create session
                        session_start();
                        $_SESSION["userID"] = $row['userID'];
                        
                        #Pushing data to firestore table report
                        #declaring varialbes
                        $stamp = new Timestamp(new DateTime());
                        $desc = "Login Attempt (Successful)";
                        $data = [
                            'datetime' => $stamp,
                            'description' => $desc,
                            'uid' => $row['userID']
                        ];
                        $rpt_table = "report";
                        
                        #get number of rows in the table report
                        $noOfRows = $database->getReference($rpt_table)->getSnapshot()->numChildren();
                        
                        #creating a unique key
                        $pKey = "R". ++$noOfRows;
                        
                        #push data into table using the unique key created
                        $postdata = $database->getReference($rpt_table)->getChild($pKey)->set($data);

                        header('Location: home.php');
                    } else {
                        
                        #Pushing data to firestore table report
                        #declaring variables
                        $stamp = new Timestamp(new DateTime());
                        $desc = "Login Attempt (Failed)";
                        $data = [
                            'datetime' => $stamp,
                            'description' => $desc,
                            'uid' => $row['userID']
                        ];
                        $rpt_table = "report";
                        
                        #get number of rows in the table report
                        $noOfRows = $database->getReference($rpt_table)->getSnapshot()->numChildren();
                        
                        #creating a unique key
                        $pKey = "R". ++$noOfRows;
                        
                        #push data into table using the unique key created
                        $postdata = $database->getReference($rpt_table)->getChild($pKey)->set($data);
                        
                        echo '<script>alert("Password incorrect.");</script>';
                    }
                }               
            } else {
                echo '<script>alert("User ID does not exist.");</script>';
            }
        }
        ?>


    </body>
</html>
