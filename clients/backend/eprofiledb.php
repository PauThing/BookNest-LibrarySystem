<?php
session_start();
include('../connect.php');

if (isset($_POST["editprofile"])) {
    $userid = $_SESSION['userid'];

    $fullname = $_POST['fname'];
    $email = $_POST['uEmail'];

    //Set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $register = date('Y-m-d H:i:s');
    $update = date('Y-m-d H:i:s');

    // Check if a new image file was provided
    if ($_FILES['upload-image']['error'] === UPLOAD_ERR_OK) {
        //Profile Image
        $pimgPath = $_FILES['upload-image']['tmp_name'];
        //Read the image file as binary data
        $pimgBinary = file_get_contents($pimgPath);

        if ($pimgBinary === false) {
            die("Failed to read the image file.");
        }

        //Update the data in database
        $query = "UPDATE [user] SET [fullname] = ?, [user_email] = ?, [profile_img] = CONVERT(varbinary(max), ?), [registered_at] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array = [$fullname, $email, $pimgBinary, $register, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //Check if the statement executed successfully
        if ($statement) {
            $_SESSION['message'] = "Successfully updated the profile picture and details.";
            header("location: ../myprofile.php");
        } else {
            die(print_r(sqlsrv_errors(), true));
            //$_SESSION['message'] = "Failed to update the details. Please try again.";
            header("location: ../editprofile.php");
        }
    } else {
        //Update the data in database
        $query2 = "UPDATE [user] SET [fullname] = ?, [user_email] = ?, [registered_at] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array2 = [$fullname, $email, $register, $update];
        $statement2 = sqlsrv_query($conn, $query2, $array2);

        if ($statement2) {
            $_SESSION['message'] = "Successfully updated the details.";
            header("location: ../myprofile.php");
        } else {
            die(print_r(sqlsrv_errors(), true));
            //$_SESSION['message'] = "Failed to update the details. Please try again.";
            header("location: ../editprofile.php");
        }
    }
} else {
	$_SESSION['message'] = "Failed to update the details. Please make sure every input is correct.";
	header("location: ../editprofile.php");
}
?>