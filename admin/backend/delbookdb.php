<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_GET['bktitle']) && isset($_GET['ISBN']) && isset($_GET['currentURL'])) {
		$btitle = $_GET['bktitle'];
		$bisbn = $_GET['ISBN'];

		$cURL = $_GET['currentURL'];

		$query = "SELECT * FROM [borrowinghistory] WHERE [ISBN] = ? AND [status] = 'On Loan'";
		$array = [$bisbn];
		$statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this book as there are students still borrowing it.";
			header("location:" . $cURL . "&st=error");
		} else {
			sqlsrv_begin_transaction($conn);

			$query2 = "DELETE FROM [book] WHERE [ISBN] = ? AND [book_title] = ?";
			$array2 = [$bisbn, $btitle];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			$query3 = "DELETE FROM [bookcatalog] WHERE [ISBN] = ?";
			$array3 = [$bisbn];
			$statement3 = sqlsrv_query($conn, $query3, $array3);

			if ($statement2 && $statement3) {
				//commit the transaction
				sqlsrv_commit($conn);
				header("location:" . $cURL . "&st=success");
			} else {
				//rollback the transaction if any statement failed
				sqlsrv_rollback($conn);

				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to delete this book.";
				header("location:" . $cURL . "&st=error");
			}
		}
	} else {
		$_SESSION['message'] = "Failed to complete this action.";
		header("location:" . $cURL . "&st=error");
	}
?>