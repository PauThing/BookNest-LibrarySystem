<?php
	session_start();
	include('../../clients/connect.php');
	
	if (isset($_GET['bktitle']) && isset($_GET['ISBN']) && isset($_GET['currentURL'])) {
		$btitle = $_GET['bktitle'];
		$bisbn = $_GET['ISBN'];

		$cURL = $_GET['currentURL'];

		$query = "DELETE FROM [book] WHERE [ISBN] = ? AND [book_title] = ?";
		$array = [$bisbn, $btitle];
		$statement = sqlsrv_query($conn, $query, $array);

		if ($statement) {
			header("location:" . $cURL . "&st=success");
		} else {
			//die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to delete this book.";
			header("location:" . $cURL . "&st=error");
		}
	} else {
		$_SESSION['message'] = "Failed to complete this action.";
		header("location:" . $cURL . "&st=error");
	}
?>