<?php
session_start();
include('../../clients/connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:/php/mail/vendor/autoload.php';
//$mail = PHPMailer()

if (isset($_POST["user-approve"])) {
    $userid = $_SESSION['uid'];

    $status = "Approved";

    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

        //update the data in database
        $query = "UPDATE [user] SET [acc_status] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array = [$status, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //check if the statement executed successfully
        if ($statement) {
            $query2 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
            $_SESSION['message'] = "Successfully approve the user. An email will be sending out as notification to the user.";
            header("location: ../../admin/userlist.php");
        } else {
            //die(print_r(sqlsrv_errors(), true));
            $_SESSION['message'] = "Failed to approve the user.";
            header("location: ../../admin/userlist.php?st=error");
        }
} else if (isset($_POST["user-reject"])) {
    $userid = $_SESSION['uid'];

    $status = "Rejected";

    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

        //update the data in database
        $query = "UPDATE [user] SET [acc_status] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array = [$status, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //check if the statement executed successfully
        if ($statement) {

            $_SESSION['message'] = "Rejected the user. An email will be sending out as notification to the user.";
            header("location: ../../admin/userlist.php");
        } else {
            //die(print_r(sqlsrv_errors(), true));
            $_SESSION['message'] = "Failed to reject the user.";
            header("location: ../../admin/userlist.php?st=error");
        }
} else {
	$_SESSION['message'] = "Failed to do any action.";
	header("location: ../../admin/userlist.php?st=error");
}
?>