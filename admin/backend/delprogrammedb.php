<?php
	session_start();
	include('../../clients/connect.php');

	$programme = $_GET['programme'];

	$query = "DELETE FROM [programme] where [programme] = '$programme'";
	$statement = sqlsrv_query($conn, $query);

	if ($statement) {
		header("location: ../../admin/eprogrammelist.php?st=success");
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Failed to delete this programme.";
		header("location: ../../admin/eprogrammelist.php?st=error");
	}
?>