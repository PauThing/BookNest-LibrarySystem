<?php
	session_start();
	include('../../clients/connect.php');

	$title = $_GET['title'];

	$query = "DELETE FROM [onlinedb] where [title] = '$title'";
	$statement = sqlsrv_query($conn, $query);

	if ($statement) {
		$_SESSION['message'] = "This online database has been deleted.";
		header("location: ../../admin/eonlinedblist.php?st=success");
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Failed to delete this online database.";
		header("location: ../../admin/eonlinedblist.php?st=error");
	}
?>