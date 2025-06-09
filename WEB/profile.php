<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CareBot-Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <?php 
    session_start();
    include 'header.php'; 
    include('includes/dbconfig.php');

    // Get user data from Firebase
    $ref_table = 'user';
    $user_data = $database->getReference($ref_table)->getValue(); // Get all users

    if (!$user_data) {
        echo '<script>alert("No users found in the database!"); window.location.href="index.php";</script>';
        exit();
    }

    // Get the first user key dynamically
    $first_user_key = array_key_first($user_data);
    $userprofile = $user_data[$first_user_key];

    if (!$userprofile) {
        echo '<script>alert("User profile not found!"); window.location.href="index.php";</script>';
        exit();
    }
    ?>

    <br/><br/><br/><br/><br/>

    <div class="profileDiv">
        <h1>Profile</h1>
        <form method="get" action="profile.php">
            <table id="rightTbl">
                <tr>
                    <td><label class="lbl">USER ID</label></td>
                    <td><label class="lbl">GENDER</label></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 20px;">
                        <input readonly type="text" id="userID" name="userID" value="<?= htmlspecialchars($userprofile['userID'] ?? 'N/A'); ?>" />
                    </td>
                    <td style="padding-bottom: 20px;">
                        <input type="radio" id="male" name="gender" value="M" <?= (isset($userprofile['gender']) && $userprofile['gender'] === 'M') ? 'checked' : ''; ?> disabled>
                        <label class="rdlbl">Male</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" id="female" name="gender" value="F" <?= (isset($userprofile['gender']) && $userprofile['gender'] === 'F') ? 'checked' : ''; ?> disabled>
                        <label class="rdlbl">Female</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label class="lbl">PASSWORD</label></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 20px;">
                        <input readonly type="password" value="****************" id="psd" name="psd" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label class="lbl">FULL NAME</label></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 20px;">
                        <input readonly type="text" id="fname" name="fname" value="<?= htmlspecialchars($userprofile['userName'] ?? 'N/A'); ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label class="lbl">EMAIL</label></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-bottom: 20px;">
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userprofile['userEmail'] ?? ''); ?>" required/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label class="lbl">PHONE NUMBER</label></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userprofile['userPhone'] ?? ''); ?>" required pattern="[0-9]+"/>
                    </td>
                </tr>
            </table>

            <input class="registerbtn" value="Edit" type="submit" name="edit">
        </form>
    </div>

    <?php
    if (isset($_GET['edit'])) {
        $updateData = [
            'userEmail' => $_GET['email'],
            'userPhone' => $_GET['phone'],
        ];

        $ref_table = "user/$first_user_key";
        $updatequery = $database->getReference($ref_table)->update($updateData);

        echo '<script>alert("Profile updated successfully."); window.location.href="profile.php";</script>';
    }
    ?>
</body>
</html>
