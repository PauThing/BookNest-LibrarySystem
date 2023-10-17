<?php
    session_start();
    include('../../clients/connect.php');

    if (isset($_POST["chgpass"])) {
        $userid = $_SESSION['userid'];

        $currentpassw = $_POST['cpass'];
        $newpassw = $_POST['npass'];
        $confirmpassw = $_POST['cfpass'];

        //set time zone
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $update = date('Y-m-d H:i:s');

        $query = "SELECT * FROM [user] WHERE [user_id] = ?";
        $array = [$userid];
        $statement = sqlsrv_query($conn, $query, $array);
        $row = sqlsrv_fetch_array($statement);

        $hashedPassword = $row['user_password'];

        //check if old password match with password in database
        if (password_verify($currentpassw, $hashedPassword)) {
            if ($newpassw === $confirmpassw) {
                $hashedNewPassword = password_hash($newpassw, PASSWORD_DEFAULT);

                //update the data in database
                $query = "UPDATE [user] SET [user_password] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
                $array = [$hashedNewPassword, $update];
                $statement = sqlsrv_query($conn, $query, $array);

                //check if the statement executed successfully
                if ($statement) {
                    $_SESSION['message'] = "Successfully change the password. Please use the new password to login next time.";
                    header("location: ../../clients/changepassw.php?st=success");
                } else {
                    //die(print_r(sqlsrv_errors(), true));
                    $_SESSION['message'] = "Failed to change the password. Please try again.";
                    header("location: ../../clients/changepassw.php?st=error");
                }
            } else {
                $_SESSION['message'] = "Passwords do not match. Please ensure the new password and confirm password are the same.";
                header("location: ../../clients/changepassw.php?st=error");
            }
        } else {
            $_SESSION['message'] = "Incorrect current password. Please try again.";
            header("location: ../../clients/changepassw.php?st=error");
        }
    } else {
        $_SESSION['message'] = "Failed to change the password.";
        header("location: ../../clients/changepassw.php?st=error");
    }
?>