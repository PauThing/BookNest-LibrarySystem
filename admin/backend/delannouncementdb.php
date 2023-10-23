<?php
	session_start();
	include('../../clients/connect.php');
	
	if (isset($_GET['annID'])) {
		$annid = $_GET['annID'];

		$query = "DELETE FROM [announcement] WHERE [ann_id] = ?";
		$array = [$annid];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location: ../../admin/editannouncement.php?st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this announcement.";
			header("location: ../../admin/editannouncement.php?st=error");
		}
	} else {
		$_SESSION['message'] = "Failed to complete delete action.";
		header("location: ../../admin/editannouncement.php?st=error");
	}
?>