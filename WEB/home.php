<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CareBot-Home</title>
        <link rel="stylesheet" href="css/home.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <br/><br/><br/><br/><br/><br />

        <div class="float-container">
        <div class="float-child left">
    <h1>Live View</h1>
    <?php
    $firebase_url_mode = "https://companionapp-acc4c-default-rtdb.firebaseio.com/robot_mode.json";

    if (isset($_GET['humanBtn'])) {
        $data = json_encode("human_following");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebase_url_mode);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);

        echo exec("sudo kill $(pgrep -f camera.py)");
        exec("sudo python /var/www/html/human_follower.py");
        echo '<iframe src="http://192.168.59.62:5000/video_feed" frameBorder="1" scrolling="no" width="1000px" height="600px"></iframe>';
    } else {
        $data = json_encode("robot_movement");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebase_url_mode);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);

        echo exec("sudo kill $(pgrep -f human_follower.py)");
        echo exec("sudo python /var/www/html/py/camera.py");
        echo '<iframe src="http://192.168.59.62:5000/video_feed" frameBorder="1" scrolling="no" width="1000px" height="600px"></iframe>';
    }
    ?>
</div>


            <div class="float-child right">           
                <span id="txtlbl">Movement Mode</span>
                <div class="tooltip"><img id="ttimg" src="img/information.png" />
                    <span class="tooltiptext">
                        <b>Remote Movement Control</b><br/>
                        Control through button below:<br/>
                        ↑ - Forward&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ↓ - Backward<br/>
                        ← - Left&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        → - Right<br/>
                        Spacebar - Stop<br/><br/>
                        <b>Human Following Mode</b><br/>
                        The robot will automatically track human.
                    </span>
                </div>

                <br />  
                <form method="get">
                    <input type="submit" id="remoteMovement" name="remoteBtn" value="Remote Movement">&nbsp;&nbsp;&nbsp;
                    <input type="submit" id="humanFollowing" name="humanBtn" value="Human Following">
                </form>                

                <table id="ctrltbl" border="0">
                    <tr>
                        <td colspan="3">
                            <a href="home.php?move=f"><img class="icn" src="img/arrow_forward.png"/></a>
                        </td>
                    </tr>
                    <tr>
                        <td><a href="home.php?move=l"><img class="icn" src="img/arrow_left.png"/></a></td>
                        <td><a href="home.php?move=s"><img id="stopicn" class="icn" src="img/arrow_stop.png"/></a></td>
                        <td><a href="home.php?move=r"><img class="icn" src="img/arrow_right.png"/></a></td>
                    </tr>
                    <tr>
                        <td colspan="3"><a href="home.php?move=b"><img class="icn" src="img/arrow_backward.png"/></a></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php
        // Firebase Database URL
        $firebase_url = "https://companionapp-acc4c-default-rtdb.firebaseio.com/robot_control.json";

        // Initialize Firebase with 'none' command
        $initial_data = json_encode(["command" => "none"]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebase_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $initial_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);

        // Handle movement commands
        if (isset($_GET['move'])) {
            // Map button inputs to actual movement commands
            $command_map = [
                "f" => "forward",
                "l" => "left",
                "r" => "right",
                "b" => "backward",
                "s" => "stop"
            ];

            $move = $_GET['move'];
            if (array_key_exists($move, $command_map)) {
                $data = json_encode(["command" => $command_map[$move]]);

                // Send data to Firebase
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $firebase_url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                $response = curl_exec($ch);
                curl_close($ch);

                echo "<script>console.log('Command Sent: " . $command_map[$move] . "');</script>";
            }
        }
        ?>
    </body>
</html>