<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["editmember"])) {
    $infotype = "membership";

    $infotext = $_POST['member-info'];

    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

    //update the data in database
    $query = "UPDATE [libraryinfo] SET [info_text] = ?, [updated_at] = ? WHERE [info_type] = '$infotype'";
    $array = [$infotext, $update];
    $statement = sqlsrv_query($conn, $query, $array);

    //check if the statement executed successfully
    if ($statement) {
        $_SESSION['message'] = "Successfully updated the membership details.";
        header("location: ../../admin/editlibrary.php");
    } else {
        $_SESSION['message'] = "Failed to update the details.";
        header("location: ../../admin/editlibrary.php");
    }
} else {
	$_SESSION['message'] = "Failed to update the details.";
	header("location: ../../admin/editlibrary.php");
}
?>