<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["reserve"])) {
		$userid = $_SESSION['userid'];
		
		$member = $_POST['member'];
		$droomid = $_POST['droom'];
		$slot = $_POST['slot'];

		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$create = date('Y-m-d');

		$query = "SELECT * FROM [reservation] WHERE [time_slot] = ? AND [created_at] = ?";
		$array = [$slot, $create];
        $statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "This slot has been reserved. Please select another slot.";
			header("location: ../../clients/discussionr.php?st=error");
		} else {
			//insert the data into database
			$query2 = "INSERT INTO [reservation] ([user_id], [droom_id], [member], [time_slot], [created_at]) VALUES (?, ?, ?, ?, ?)";
			$array2 = [$userid, $droomid, $member, $slot, $create];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			//check if the statement executed successfully
			if ($statement2) {
				header("location: ../../clients/discussionr.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to reserve a discussion room.";
				header("location: ../../clients/discussionr.php?st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to reserve a discussion room.";
		header("location: ../../clients/discussionr.php?st=error");
	}
?>