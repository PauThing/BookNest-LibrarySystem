<?php
	session_start();
	include('../../clients/connect.php');
	
	if (isset($_GET['dbtitle'])) {
		$dbtitle = $_GET['dbtitle'];

		$query = "DELETE FROM [onlinedb] WHERE [title] = ?";
		$array = [$dbtitle];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location: ../../admin/eonlinedblist.php?st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this online database.";
			header("location: ../../admin/eonlinedblist.php?st=error");
		}
	} else if (isset($_GET['programme'])) {
		$programme = $_GET['programme'];

		$query = "DELETE FROM [programme] WHERE [programme] = ?";
		$array = [$programme];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location: ../../admin/eprogrammelist.php?st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this programme.";
			header("location: ../../admin/eprogrammelist.php?st=error");
		}
	} else if (isset($_GET['eptitle'])) {
		$eptitle = $_GET['eptitle'];

		$query = "DELETE FROM [exampaper] WHERE [title] = ?";
		$array = [$eptitle];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location: ../../admin/epastyearlist.php?st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this exam paper folder.";
			header("location: ../../admin/epastyearlist.php?st=error");
		}
	} else if (isset($_GET['sptitle'])) {
		$sptitle = $_GET['sptitle'];
		$spprogramme = $_GET['spprogramme'];

		$query = "DELETE FROM [studentproject] WHERE [title] = ? AND [programme] = ?";
		$array = [$sptitle, $spprogramme];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location: ../../admin/estuprojectlist.php?st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this programme.";
			header("location: ../../admin/estuprojectlist.php?st=error");
		}
	}
?>