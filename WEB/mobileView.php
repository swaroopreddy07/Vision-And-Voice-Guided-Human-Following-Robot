<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CareBot-Home</title>
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/home.css">

    </head>
    <body>
        <br/><br/><br/><br/><br/>
        <iframe src="http://192.168.0.120:8000/" frameBorder="0" scrolling="no" width="95%" height="550px;"></iframe>
        <br/><br/><br/><br/><br/>
        <table id="ctrltbl" border="0" style="margin: auto;">
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

        <?php
        if (isset($_GET['move'])) {
            switch ($_GET['move']) {
                case "f":
                    echo exec("sudo python /var/www/html/py/forward.py");
                    break;
                case "l":
                    echo exec("sudo python /var/www/html/py/left.py");
                    break;
                case "r":
                    echo exec("sudo python /var/www/html/py/right.py");
                    break;
                case "b":
                    echo exec("sudo python /var/www/html/py/backward.py");
                    break;
                case "s":
                    echo exec("sudo python /var/www/html/py/stop.py");
                    break;
            }
        }
        ?>

    </body>
</html>
