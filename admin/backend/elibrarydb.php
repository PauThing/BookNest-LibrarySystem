<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["editlibrary"])) {
    $infotype = "librarian";

    $infotext = $_POST['info_text'];

    //Set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

    // Check if a new image file was provided
    if ($_FILES['upload-image']['error'] === UPLOAD_ERR_OK) {
        $imgPath = $_FILES['upload-image']['tmp_name'];
        //Read the image file as binary data
        $imgBinary = file_get_contents($imgPath);

        if ($imgBinary === false) {
            die("Failed to read the image file.");
        }

        //Update the data in database
        $query = "UPDATE [libraryinfo] SET [info_img] = CONVERT(varbinary(max), ?), [info_text] = ?, [updated_at] = ? WHERE [info_type] = '$infotype'";
        $array = [$imgBinary, $infotext, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //Check if the statement executed successfully
        if ($statement) {
            $_SESSION['message'] = "Successfully updated the librarian details.";
            header("location: ../editlibrary.php");
        } else {
            die(print_r(sqlsrv_errors(), true));
            //$_SESSION['message'] = "Failed to update the details. Please try again.";
            header("location: ../editlibrary.php");
        }
    } else {
        //Update the data in database
        $query2 = "UPDATE [user] SET [fullname] = ?, [user_email] = ?, [registered_at] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array2 = [$fullname, $email, $register, $update];
        $statement2 = sqlsrv_query($conn, $query2, $array2);

        if ($statement2) {
            $_SESSION['message'] = "Successfully updated the details.";
            header("location: ../editlibrary.php");
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