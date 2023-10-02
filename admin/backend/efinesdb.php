<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["editfines"])) {
    $infotype = "fines";

    $infotext = $_POST['fines-info'];

    //Set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

    //Update the data in database
    $query = "UPDATE [libraryinfo] SET [info_text] = ?, [updated_at] = ? WHERE [info_type] = '$infotype'";
    $array = [$infotext, $update];
    $statement = sqlsrv_query($conn, $query, $array);

    //Check if the statement executed successfully
    if ($statement) {
        $_SESSION['message'] = "Successfully updated the fines and penalty.";
        header("location: ../editfines.php");
    } else {
        $_SESSION['message'] = "Failed to update the fines.";
        header("location: ../editfines.php");
    }
} else {
	$_SESSION['message'] = "Failed to update the fines.";
	header("location: ../editfines.php");
}
