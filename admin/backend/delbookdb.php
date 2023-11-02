<?php
	session_start();
	include('../../clients/connect.php');
	
	if (isset($_GET['bktitle']) && isset($_GET['ISBN']) && isset($_GET['currentURL'])) {
		$btitle = $_GET['bktitle'];
		$bisbn = $_GET['ISBN'];

		$cURL = $_GET['currentURL'];

		sqlsrv_begin_transaction($conn);

		$query = "DELETE FROM [book] WHERE [ISBN] = ? AND [book_title] = ?";
		$array = [$bisbn, $btitle];
		$statement = sqlsrv_query($conn, $query, $array);

		$query2 = "DELETE FROM [bookcatalog] WHERE [ISBN] = ?";
		$array2 = [$bisbn];
		$statement2 = sqlsrv_query($conn, $query2, $array2);

		if ($statement && $statement2) {
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
	} else {
		$_SESSION['message'] = "Failed to complete this action.";
		header("location:" . $cURL . "&st=error");
	}
?>