<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["new-announcement"])) {
		$anntitle = $_POST['anntitle'];
		$anndetail = $_POST['announcement-info'];

		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$create = date('Y-m-d H:i:s');

		if (!preg_match("/^[a-zA-Z-' ]*$/", $anntitle)) {
			$_SESSION['message'] = "The announcement title can only contain letters and white space.";
			header("location: ../../admin/addannouncement.php?st=error");
		} else {
			//insert the data into database
			$query = "INSERT INTO [announcement] ([ann_title], [ann_detail], [created_at]) VALUES (?, ?, ?)";
			$array = [$anntitle, $anndetail, $create];
			$statement = sqlsrv_query($conn, $query, $array);

			//check if the statement executed successfully
			if ($statement) {
				header("location: ../../admin/editannouncement.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to publish new announcement. Please try again.";
				header("location: ../../admin/addannouncement.php?st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Failed to publish new announcement. Please ensure every input is correct.";
		header("location: ../../admin/addannouncement.php?st=error");
	}
?>