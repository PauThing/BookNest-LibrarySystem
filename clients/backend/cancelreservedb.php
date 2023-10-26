<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_GET['uid']) && isset($_GET['slot']) && isset($_GET['date'])) {
		$userid = $_GET['uid'];
		$timeslot = $_GET['slot'];
		$date = $_GET['date'];

		if ($userid != $_SESSION['userid']) {
			$_SESSION['message'] = "You are not able to cancel other people reservation";
			header("location: ../../clients/discussionr.php?st=error");
		} else {
			$query = "DELETE FROM [reservation] WHERE [user_id] = ? AND [time_slot] = ? AND [created_at] = ?";
			$array = [$userid, $timeslot, $date];
			$statement = sqlsrv_query($conn, $query, $array);

			if ($statement) {
				$_SESSION['message'] = "You have successfully cancelled your reservation.";
				header("location: ../../clients/discussionr.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to cancel the reservation.";
				header("location: ../../clients/discussionr.php?st=error");
			}
		}
	} else {
		$_SESSION['message'] = "Failed to complete this action.";
		header("location: ../../clients/discussionr.php?st=error");
	}
?>