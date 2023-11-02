<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_GET['ISBN'])) {
		$userid = $_SESSION['userid'];
		
		$bisbn = $_GET['ISBN'];

		$cURL = $_GET['currentURL'];

		//check if the current URL already contains query parameters
		$containsQuestionMark = (strpos($cURL, '?') !== false);
		$containsAmpersand = (strrpos($cURL, '&') !== false);

		if ($containsQuestionMark && $containsAmpersand) {
			$separator = '&'; // Both ? and & are present
		} else {
			$separator = ($containsQuestionMark) ? '&' : '?';
		}

		$query = "SELECT * FROM [bookmark] WHERE [user_id] = ? AND [ISBN] = ?";
		$array = [$userid, $bisbn];
        $statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "You already add this book into your favorite list.";
			header("location: ". $cURL . $separator . "st=error");
		} else {
			//insert the data into database
			$query2 = "INSERT INTO [bookmark] ([user_id], [ISBN]) VALUES (?, ?)";
			$array2 = [$userid, $bisbn];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			//check if the statement executed successfully
			if ($statement2) {
				$_SESSION['message'] = "Successfully add the book into favorite list.";
				header("location: ". $cURL . $separator . "st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to add the book into favorite list.";
				header("location: ". $cURL . $separator . "st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to add favorite.";
		header("location: ". $cURL . $separator . "st=error");
	}
?>