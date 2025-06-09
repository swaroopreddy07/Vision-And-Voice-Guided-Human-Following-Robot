<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CareBot-Register</title>

        <link rel="stylesheet" href="css/register.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Varela Round' rel='stylesheet'>

    </head>

    <body>
        <div class="centerDiv">

            <div class="flex-child">
                <table id="leftTbl">
                    <tr>
                        <td><img id="fimg" src="img/word_logo.png"></td>
                    </tr>
                    <tr>
                        <td><img id="simg" src="img/robotics.svg"></td>
                    </tr>
                    <tr>
                        <td><img id="tim" src="img/welcome.png"></td>
                    </tr>
                    <tr>
                        <td><p id="descTxt">Hi, Welcome to Companion Robot For Elderly. I would <br/>
                                like to invite you for a journey in caring for your ageing <br/>
                                loved ones with us. Register yourself up now and enjoy <br/>
                                the benefits given!</p></td>
                    </tr>
                </table>

                <br/><br/>
                <a href="index.php" id="gotoLogin">Already have an account, Sign In→ </a>
            </div>

            <div class="flex-child">
                <h1>Register</h1>
                <form method="get" action="register.php">
                    <table id="rightTbl">
                        <tr>
                            <td><label class="lbl">USER ID</label></td>
                            <td><label class="lbl">GENDER</label></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 20px;"><input type="text" placeholder="Enter Your User ID" id="userID" name="userID" pattern="[a-zA-Z0-9]+" required maxlength="6" data-error="This field is required."/></td>
                            <td style="padding-bottom: 20px;"><input type="radio" id="male" name="gender" value="M" checked="checked"> <label class="rdlbl">Male</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" id="female" name="gender" value="F"> <label class="rdlbl">Female</label></td>
                        </tr>
                        <tr>
                            <td colspan="2"><label class="lbl">PASSWORD</label></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"><input type="password" placeholder="Enter Your Password" id="psd" name="psd" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" data-error="Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><label class="lbl">FULL NAME</label></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"><input type="text" placeholder="Enter Your Full Name" id="fname" name="fname" pattern="^[A-Za-z\s]*$" data-error="This field is required and no number is allowed." required/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><label class="lbl">EMAIL</label></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"><input type="email" placeholder="Enter Your Email" id="email" name="email" data-error="Invalid Email Format." required/></td>
                        </tr>
                        <tr>
                            <td><label class="lbl">PHONE NUMBER</label></td>
                            <td><label class="lbl">REFERRAL CODE</label></td>
                        </tr>
                        <tr>
                            <td><input type="text" id="phone" name="phone" placeholder="Enter Your Phone No" required pattern="[0-9]+" data-error="Invalid Phone Number."/></td>
                            <td><input type="text" id="ref" name="ref" placeholder="Enter Your Referral Code" required data-error="This field is required."/></td>
                        </tr>
                    </table>

                    <input class="registerbtn" value="Sign Up" type="submit" name="signup" >
                </form>
            </div>


        </div>

        <?php
        include('includes/dbconfig.php');

        if (isset($_GET['signup'])) {
            
            $data = [
                'userID' => $_GET['userID'],
                'userPassword' => password_hash($_GET['psd'], PASSWORD_DEFAULT),
                'userName' => $_GET['fname'],
                'userPhone' => $_GET['phone'],
                'userEmail' => $_GET['email'],
                'userGender' => $_GET['gender'],
                'userAuth' => $_GET['ref'],
            ];

            $ref_table = "user";
            $postRef = $database->getReference($ref_table)->push($data);
            
            echo '<script>window.location.replace("index.php");</script>';
        }
        ?>

        <script>
            $(function () {
                var inputs = document.getElementsByTagName("INPUT");
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].oninvalid = function (e) {
                        e.target.setCustomValidity("");
                        if (!e.target.validity.valid) {
                            e.target.setCustomValidity(e.target.getAttribute("data-error"));
                        }
                    };
                }
            });
        </script>        
    </body>
</html>
